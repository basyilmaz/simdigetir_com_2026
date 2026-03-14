import fs from "node:fs/promises";
import path from "node:path";
import { chromium } from "playwright";

const baseUrl = process.env.BASE_URL || "http://127.0.0.1:8000";
const outRoot =
  process.env.OUT_DIR ||
  path.resolve("storage", "app", "qa", "mobile-regression", "2026-02-28");

const pages = [
  { id: "home", path: "/" },
  { id: "about", path: "/hakkimizda" },
  { id: "services", path: "/hizmetler" },
  { id: "corporate", path: "/kurumsal" },
  { id: "contact", path: "/iletisim" },
  { id: "faq", path: "/sss" },
  { id: "courier-apply", path: "/kurye-basvuru" },
];

const viewports = [
  { id: "375", width: 375, height: 812, deviceScaleFactor: 2 },
  { id: "390", width: 390, height: 844, deviceScaleFactor: 3 },
  { id: "768", width: 768, height: 1024, deviceScaleFactor: 2 },
  { id: "1024", width: 1024, height: 1366, deviceScaleFactor: 2 },
];

const ensureDir = async (dir) => fs.mkdir(dir, { recursive: true });

const analyzePage = async (page) =>
  page.evaluate(() => {
    const offcanvas = document.getElementById("offcanvas-sidebar");
    const offcanvasWasHidden = offcanvas && !offcanvas.classList.contains("active");
    const offcanvasDisplay = offcanvas ? offcanvas.style.display : "";
    if (offcanvasWasHidden) {
      offcanvas.style.display = "none";
    }

    const vw = window.innerWidth;
    const root = document.documentElement;
    const overflowX = root.scrollWidth - vw;

    const offenders = [];
    for (const el of Array.from(document.querySelectorAll("body *"))) {
      const rect = el.getBoundingClientRect();
      if (rect.width <= 0 || rect.height <= 0) continue;
      if (el.closest("#offcanvas-sidebar")) continue;
      if (rect.right > vw + 1 || rect.left < -1) {
        const id = el.id ? `#${el.id}` : "";
        const cls = (el.className || "")
          .toString()
          .trim()
          .split(/\s+/)
          .filter(Boolean)
          .slice(0, 2)
          .map((c) => `.${c}`)
          .join("");
        offenders.push({
          tag: el.tagName.toLowerCase(),
          selector: `${el.tagName.toLowerCase()}${id}${cls}`,
          left: Math.round(rect.left),
          right: Math.round(rect.right),
          width: Math.round(rect.width),
        });
      }
      if (offenders.length >= 20) break;
    }

    const images = Array.from(document.images).map((img) => ({
      src: img.currentSrc || img.src,
      complete: img.complete,
      naturalWidth: img.naturalWidth,
      naturalHeight: img.naturalHeight,
      displayWidth: Math.round(img.getBoundingClientRect().width),
      displayHeight: Math.round(img.getBoundingClientRect().height),
      loading: img.getAttribute("loading"),
      decoding: img.getAttribute("decoding"),
    }));

    const brokenImages = images.filter((img) => img.complete && img.naturalWidth === 0);

    if (offcanvasWasHidden && offcanvas) {
      offcanvas.style.display = offcanvasDisplay;
    }

    return {
      viewportWidth: vw,
      scrollWidth: root.scrollWidth,
      overflowX,
      hasHorizontalOverflow: overflowX > 4,
      offenders,
      imageCount: images.length,
      brokenImageCount: brokenImages.length,
      brokenImages: brokenImages.slice(0, 10),
      imagesWithoutLazy: images.filter((i) => i.loading !== "lazy").length,
      imagesWithoutAsyncDecoding: images.filter((i) => i.decoding !== "async").length,
    };
  });

const run = async () => {
  await ensureDir(outRoot);
  const browser = await chromium.launch({ headless: true });
  const report = {
    generatedAt: new Date().toISOString(),
    baseUrl,
    pages,
    viewports,
    results: [],
  };

  try {
    for (const vp of viewports) {
      const context = await browser.newContext({
        viewport: { width: vp.width, height: vp.height },
        deviceScaleFactor: vp.deviceScaleFactor,
      });

      for (const p of pages) {
        const page = await context.newPage();
        const url = `${baseUrl}${p.path}`;
        const record = {
          pageId: p.id,
          path: p.path,
          viewportId: vp.id,
          viewport: { width: vp.width, height: vp.height },
          url,
        };

        try {
          const response = await page.goto(url, {
            waitUntil: "networkidle",
            timeout: 60000,
          });

          record.status = response?.status() ?? 0;
          await page.addStyleTag({
            content: `
              *, *::before, *::after {
                animation: none !important;
                transition: none !important;
              }
            `,
          });
          await page.waitForTimeout(1000);
          record.analysis = await analyzePage(page);

          const shotPath = path.join(outRoot, `${p.id}-${vp.id}.png`);
          await page.screenshot({ path: shotPath, fullPage: true });
          record.screenshot = shotPath;
        } catch (error) {
          record.status = 0;
          record.error = String(error?.message || error);
        } finally {
          report.results.push(record);
          await page.close();
        }
      }

      await context.close();
    }
  } finally {
    await browser.close();
  }

  const jsonPath = path.join(outRoot, "report.json");
  await fs.writeFile(jsonPath, JSON.stringify(report, null, 2), "utf8");
  console.log(jsonPath);
};

run().catch((error) => {
  console.error(error);
  process.exit(1);
});
