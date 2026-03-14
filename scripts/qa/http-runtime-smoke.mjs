import fs from "node:fs/promises";
import path from "node:path";

const baseUrl = process.env.BASE_URL || "http://127.0.0.1:8000";
const outDir =
  process.env.OUT_DIR ||
  path.resolve("storage", "app", "qa", "http-runtime-smoke", "latest");

const pages = [
  "/",
  "/hakkimizda",
  "/hizmetler",
  "/kurumsal",
  "/iletisim",
  "/sss",
  "/kurye-basvuru",
];

const fetchWithTimeout = async (url, timeoutMs = 20000) => {
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), timeoutMs);
  try {
    return await fetch(url, {
      redirect: "follow",
      signal: controller.signal,
      headers: { Accept: "text/html" },
    });
  } finally {
    clearTimeout(timeout);
  }
};

const extractOgImage = (html) => {
  const patternA =
    /<meta\s+[^>]*property=["']og:image["'][^>]*content=["']([^"']+)["'][^>]*>/i;
  const patternB =
    /<meta\s+[^>]*content=["']([^"']+)["'][^>]*property=["']og:image["'][^>]*>/i;
  const match = html.match(patternA) || html.match(patternB);
  return match ? match[1] : "";
};

const run = async () => {
  await fs.mkdir(outDir, { recursive: true });

  const results = [];
  for (const page of pages) {
    const url = `${baseUrl}${page}`;
    const result = {
      page,
      url,
      status: 0,
      ok: false,
      ogImage: "",
      hasCastintech: false,
      error: "",
    };

    try {
      const response = await fetchWithTimeout(url);
      const body = await response.text();

      result.status = response.status;
      result.ok = response.status === 200;
      result.ogImage = extractOgImage(body);
      result.hasCastintech =
        page === "/" ? body.toLowerCase().includes("castintech") : false;

      if (!result.ok) {
        result.error = `HTTP_${response.status}`;
      }

      if (!result.ogImage || !result.ogImage.toLowerCase().endsWith(".jpg")) {
        result.error = result.error || "OG_IMAGE_NOT_JPG";
      }

      if (page === "/" && !result.hasCastintech) {
        result.error = result.error || "FOOTER_BRAND_NOT_FOUND";
      }
    } catch (error) {
      result.error = String(error?.message || error);
    }

    results.push(result);
  }

  const report = {
    generatedAt: new Date().toISOString(),
    baseUrl,
    results,
  };

  const reportPath = path.join(outDir, "report.json");
  await fs.writeFile(reportPath, JSON.stringify(report, null, 2), "utf8");
  console.log(reportPath);

  const failed = results.filter(
    (item) => item.status !== 200 || Boolean(item.error),
  );
  if (failed.length > 0) {
    process.exit(1);
  }
};

run().catch((error) => {
  console.error(error);
  process.exit(1);
});
