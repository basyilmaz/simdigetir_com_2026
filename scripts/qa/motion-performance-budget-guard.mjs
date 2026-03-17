import fs from "node:fs/promises";
import path from "node:path";
import { chromium } from "playwright";

const baseUrl = process.env.BASE_URL || "http://127.0.0.1:8000";
const outDir =
  process.env.OUT_DIR ||
  path.resolve("storage", "app", "qa", "motion-budget", "latest");
const baselinePath =
  process.env.MOTION_BUDGET_BASELINE_PATH ||
  path.resolve("docs", "ops", "budgets", "motion-performance-budget-baseline.json");
const writeBaseline = process.argv.includes("--write-baseline");

const budgets = {
  maxScriptKb: Number(process.env.MOTION_BUDGET_MAX_SCRIPT_KB || 1200),
  maxStylesheetKb: Number(process.env.MOTION_BUDGET_MAX_STYLESHEET_KB || 260),
  maxMotionKb: Number(process.env.MOTION_BUDGET_MAX_MOTION_KB || 750),
  maxLottieJsonKb: Number(process.env.MOTION_BUDGET_MAX_LOTTIE_JSON_KB || 120),
  maxLcpMs: Number(process.env.MOTION_BUDGET_MAX_LCP_MS || 8000),
  maxCls: Number(process.env.MOTION_BUDGET_MAX_CLS || 0.2),
  maxScriptDeltaKb: Number(process.env.MOTION_BUDGET_MAX_SCRIPT_DELTA_KB || 120),
  maxStylesheetDeltaKb: Number(
    process.env.MOTION_BUDGET_MAX_STYLESHEET_DELTA_KB || 30,
  ),
  maxMotionDeltaKb: Number(process.env.MOTION_BUDGET_MAX_MOTION_DELTA_KB || 120),
  maxLottieJsonDeltaKb: Number(
    process.env.MOTION_BUDGET_MAX_LOTTIE_JSON_DELTA_KB || 30,
  ),
};

const motionAssetPattern =
  /(gsap|scrolltrigger|lottie|bodymovin|animate(\.min)?\.css|swiper|delivery-rider\.json)/i;

const round = (value, digits = 2) =>
  Number(Number(value || 0).toFixed(digits));

const toKb = (bytes) => round((bytes || 0) / 1024, 2);

const unique = (values) => Array.from(new Set(values.filter(Boolean)));

const resolveAssetUrl = (asset, pageUrl) => {
  try {
    return new URL(asset, pageUrl).href;
  } catch {
    return "";
  }
};

const fetchWithTimeout = async (url, timeoutMs = 45000) => {
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), timeoutMs);
  try {
    return await fetch(url, {
      redirect: "follow",
      signal: controller.signal,
      headers: { Accept: "*/*" },
    });
  } finally {
    clearTimeout(timeout);
  }
};

const extractScriptUrls = (html, pageUrl) => {
  const urls = [];
  const srcPattern = /<script\b[^>]*\bsrc=["']([^"']+)["'][^>]*>/gi;
  let match;
  while ((match = srcPattern.exec(html)) !== null) {
    const absolute = resolveAssetUrl(match[1], pageUrl);
    if (absolute) {
      urls.push(absolute);
    }
  }

  const dynamicPattern = /https?:\/\/[^"'`\s]+(?:lottie|bodymovin)[^"'`\s]*\.js[^"'`\s]*/gi;
  const dynamicMatches = html.match(dynamicPattern) || [];
  dynamicMatches.forEach((dynamicUrl) => {
    const absolute = resolveAssetUrl(dynamicUrl, pageUrl);
    if (absolute) {
      urls.push(absolute);
    }
  });

  return unique(urls);
};

const extractStylesheetUrls = (html, pageUrl) => {
  const urls = [];
  const patternA =
    /<link\b[^>]*\brel=["'][^"']*stylesheet[^"']*["'][^>]*\bhref=["']([^"']+)["'][^>]*>/gi;
  const patternB =
    /<link\b[^>]*\bhref=["']([^"']+)["'][^>]*\brel=["'][^"']*stylesheet[^"']*["'][^>]*>/gi;

  let match;
  while ((match = patternA.exec(html)) !== null) {
    const absolute = resolveAssetUrl(match[1], pageUrl);
    if (absolute) {
      urls.push(absolute);
    }
  }

  while ((match = patternB.exec(html)) !== null) {
    const absolute = resolveAssetUrl(match[1], pageUrl);
    if (absolute) {
      urls.push(absolute);
    }
  }

  return unique(urls);
};

const extractLottieJsonUrls = (html, pageUrl) => {
  const urls = [];
  const pattern = /\bdata-lottie-src=["']([^"']+)["']/gi;
  let match;
  while ((match = pattern.exec(html)) !== null) {
    const absolute = resolveAssetUrl(match[1], pageUrl);
    if (absolute) {
      urls.push(absolute);
    }
  }

  return unique(urls);
};

const measureAsset = async (url) => {
  const result = {
    url,
    ok: false,
    status: 0,
    bytes: 0,
    kb: 0,
    contentType: "",
    error: "",
  };

  try {
    const response = await fetchWithTimeout(url);
    result.status = response.status;
    result.contentType = response.headers.get("content-type") || "";
    if (!response.ok) {
      result.error = `HTTP_${response.status}`;
      return result;
    }

    const body = await response.arrayBuffer();
    result.bytes = body.byteLength;
    result.kb = toKb(body.byteLength);
    result.ok = true;
    return result;
  } catch (error) {
    result.error = String(error?.message || error);
    return result;
  }
};

const sumBytes = (assets) =>
  assets
    .filter((asset) => asset.ok)
    .reduce((total, asset) => total + (asset.bytes || 0), 0);

const collectPerformanceSample = async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ viewport: { width: 1366, height: 900 } });
  const page = await context.newPage();

  try {
    await page.addInitScript(() => {
      window.__motionPerfSample = {
        cls: 0,
        lcpMs: 0,
        longTaskTotalMs: 0,
      };

      try {
        new PerformanceObserver((entryList) => {
          for (const entry of entryList.getEntries()) {
            if (!entry.hadRecentInput) {
              window.__motionPerfSample.cls += entry.value;
            }
          }
        }).observe({ type: "layout-shift", buffered: true });
      } catch {}

      try {
        new PerformanceObserver((entryList) => {
          for (const entry of entryList.getEntries()) {
            window.__motionPerfSample.lcpMs = Math.max(
              window.__motionPerfSample.lcpMs,
              entry.startTime || 0,
            );
          }
        }).observe({ type: "largest-contentful-paint", buffered: true });
      } catch {}

      try {
        new PerformanceObserver((entryList) => {
          for (const entry of entryList.getEntries()) {
            window.__motionPerfSample.longTaskTotalMs += entry.duration || 0;
          }
        }).observe({ type: "longtask", buffered: true });
      } catch {}
    });

    await page.goto(baseUrl, { waitUntil: "networkidle", timeout: 60000 });
    await page.locator("[data-delivery-lottie]").scrollIntoViewIfNeeded().catch(() => {});
    await page.waitForTimeout(2500);

    const sample = await page.evaluate(() => {
      const nav = performance.getEntriesByType("navigation")[0];
      const fcp = performance.getEntriesByName("first-contentful-paint")[0];
      const metrics = window.__motionPerfSample || {
        cls: 0,
        lcpMs: 0,
        longTaskTotalMs: 0,
      };

      return {
        firstContentfulPaintMs: fcp ? Number(fcp.startTime.toFixed(2)) : null,
        largestContentfulPaintMs: Number((metrics.lcpMs || 0).toFixed(2)),
        cumulativeLayoutShift: Number((metrics.cls || 0).toFixed(4)),
        longTaskTotalMs: Number((metrics.longTaskTotalMs || 0).toFixed(2)),
        domContentLoadedMs: nav ? Number(nav.domContentLoadedEventEnd.toFixed(2)) : null,
        loadEventEndMs: nav ? Number(nav.loadEventEnd.toFixed(2)) : null,
      };
    });

    return sample;
  } finally {
    await page.close().catch(() => {});
    await context.close().catch(() => {});
    await browser.close().catch(() => {});
  }
};

const loadBaseline = async () => {
  try {
    const raw = await fs.readFile(baselinePath, "utf8");
    return JSON.parse(raw);
  } catch {
    return null;
  }
};

const run = async () => {
  await fs.mkdir(outDir, { recursive: true });

  const homeResponse = await fetchWithTimeout(baseUrl);
  const html = await homeResponse.text();

  const scriptUrls = extractScriptUrls(html, baseUrl);
  const stylesheetUrls = extractStylesheetUrls(html, baseUrl);
  const lottieJsonUrls = extractLottieJsonUrls(html, baseUrl);
  const motionUrls = unique(
    [...scriptUrls, ...stylesheetUrls, ...lottieJsonUrls].filter((url) =>
      motionAssetPattern.test(url),
    ),
  );

  const [scripts, stylesheets, motionAssets, lottieJsonAssets] = await Promise.all([
    Promise.all(scriptUrls.map((url) => measureAsset(url))),
    Promise.all(stylesheetUrls.map((url) => measureAsset(url))),
    Promise.all(motionUrls.map((url) => measureAsset(url))),
    Promise.all(lottieJsonUrls.map((url) => measureAsset(url))),
  ]);

  const totals = {
    scriptKb: toKb(sumBytes(scripts)),
    stylesheetKb: toKb(sumBytes(stylesheets)),
    motionKb: toKb(sumBytes(motionAssets)),
    lottieJsonKb: toKb(sumBytes(lottieJsonAssets)),
  };

  const perfSample = await collectPerformanceSample();
  const baseline = await loadBaseline();

  const warnings = [];
  const failures = [];
  const unresolvedAssets = [
    ...scripts.filter((asset) => !asset.ok),
    ...stylesheets.filter((asset) => !asset.ok),
  ];

  if (unresolvedAssets.length > 0) {
    warnings.push(`UNRESOLVED_ASSET_COUNT_${unresolvedAssets.length}`);
  }

  if (totals.scriptKb > budgets.maxScriptKb) {
    failures.push(`SCRIPT_BUDGET_EXCEEDED_${totals.scriptKb}KB`);
  }
  if (totals.stylesheetKb > budgets.maxStylesheetKb) {
    failures.push(`CSS_BUDGET_EXCEEDED_${totals.stylesheetKb}KB`);
  }
  if (totals.motionKb > budgets.maxMotionKb) {
    failures.push(`MOTION_BUDGET_EXCEEDED_${totals.motionKb}KB`);
  }
  if (totals.lottieJsonKb > budgets.maxLottieJsonKb) {
    failures.push(`LOTTIE_JSON_BUDGET_EXCEEDED_${totals.lottieJsonKb}KB`);
  }
  if (
    perfSample.largestContentfulPaintMs !== null &&
    perfSample.largestContentfulPaintMs > budgets.maxLcpMs
  ) {
    failures.push(`LCP_BUDGET_EXCEEDED_${perfSample.largestContentfulPaintMs}MS`);
  }
  if (perfSample.cumulativeLayoutShift > budgets.maxCls) {
    failures.push(`CLS_BUDGET_EXCEEDED_${perfSample.cumulativeLayoutShift}`);
  }

  const bundleDiff = baseline
    ? {
        scriptKbDelta: round(totals.scriptKb - Number(baseline?.totals?.scriptKb || 0)),
        stylesheetKbDelta: round(
          totals.stylesheetKb - Number(baseline?.totals?.stylesheetKb || 0),
        ),
        motionKbDelta: round(totals.motionKb - Number(baseline?.totals?.motionKb || 0)),
        lottieJsonKbDelta: round(
          totals.lottieJsonKb - Number(baseline?.totals?.lottieJsonKb || 0),
        ),
      }
    : null;

  if (bundleDiff) {
    if (Math.abs(bundleDiff.scriptKbDelta) > budgets.maxScriptDeltaKb) {
      failures.push(`SCRIPT_DELTA_BUDGET_EXCEEDED_${bundleDiff.scriptKbDelta}KB`);
    }
    if (Math.abs(bundleDiff.stylesheetKbDelta) > budgets.maxStylesheetDeltaKb) {
      failures.push(`CSS_DELTA_BUDGET_EXCEEDED_${bundleDiff.stylesheetKbDelta}KB`);
    }
    if (Math.abs(bundleDiff.motionKbDelta) > budgets.maxMotionDeltaKb) {
      failures.push(`MOTION_DELTA_BUDGET_EXCEEDED_${bundleDiff.motionKbDelta}KB`);
    }
    if (Math.abs(bundleDiff.lottieJsonKbDelta) > budgets.maxLottieJsonDeltaKb) {
      failures.push(
        `LOTTIE_JSON_DELTA_BUDGET_EXCEEDED_${bundleDiff.lottieJsonKbDelta}KB`,
      );
    }
  } else {
    warnings.push("BUNDLE_BASELINE_MISSING");
  }

  const report = {
    generatedAt: new Date().toISOString(),
    baseUrl,
    budgets,
    totals,
    bundleDiff,
    perfSample,
    pass: failures.length === 0,
    failures,
    warnings,
    baseline: {
      path: baselinePath,
      exists: Boolean(baseline),
      written: false,
    },
    assets: {
      scripts,
      stylesheets,
      motionAssets,
      lottieJsonAssets,
    },
  };

  if (writeBaseline) {
    await fs.mkdir(path.dirname(baselinePath), { recursive: true });
    await fs.writeFile(
      baselinePath,
      JSON.stringify(
        {
          generatedAt: report.generatedAt,
          baseUrl,
          budgets,
          totals,
          perfSample,
        },
        null,
        2,
      ),
      "utf8",
    );
    report.baseline.written = true;
  }

  const reportPath = path.join(outDir, "report.json");
  await fs.writeFile(reportPath, JSON.stringify(report, null, 2), "utf8");
  console.log(reportPath);

  if (!report.pass) {
    process.exit(1);
  }
};

run().catch((error) => {
  console.error(error);
  process.exit(1);
});
