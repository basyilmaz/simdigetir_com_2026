import fs from "node:fs/promises";
import path from "node:path";

const reportPath = path.resolve(
  "storage",
  "app",
  "qa",
  "mobile-regression",
  "2026-02-28",
  "report.json",
);
const outPath = path.resolve(
  "docs",
  "qa",
  "mobile-regression-checklist-2026-02-28.md",
);

const pageOrder = [
  "home",
  "about",
  "services",
  "corporate",
  "contact",
  "faq",
  "courier-apply",
];
const viewportOrder = ["375", "390", "768", "1024"];

const isFail = (result) =>
  result.status !== 200 ||
  result.analysis?.hasHorizontalOverflow ||
  (result.analysis?.brokenImageCount || 0) > 0;

const run = async () => {
  const report = JSON.parse(await fs.readFile(reportPath, "utf8"));
  const lines = [];
  lines.push("# Mobile Regression Checklist - 2026-02-28");
  lines.push("");
  lines.push(`Base URL: ${report.baseUrl}`);
  lines.push(`Rapor: \`${reportPath}\``);
  lines.push("Screenshot klasoru: `storage/app/qa/mobile-regression/2026-02-28`");
  lines.push("");
  lines.push("## Breakpoints");
  lines.push("- 375x812");
  lines.push("- 390x844");
  lines.push("- 768x1024");
  lines.push("- 1024x1366");
  lines.push("");
  lines.push("## Checklist Sonuclari");
  lines.push("");
  lines.push("| Sayfa | 375 | 390 | 768 | 1024 | Not |");
  lines.push("|---|---|---|---|---|---|");

  for (const pageId of pageOrder) {
    const cells = [];
    for (const viewportId of viewportOrder) {
      const result = report.results.find(
        (r) => r.pageId === pageId && r.viewportId === viewportId,
      );
      if (!result) {
        cells.push("N/A");
        continue;
      }

      if (isFail(result)) {
        cells.push(
          `FAIL (ovf:${result.analysis?.overflowX ?? "?"}, img:${result.analysis?.brokenImageCount ?? "?"})`,
        );
      } else {
        cells.push("PASS");
      }
    }

    const fails = report.results.filter(
      (r) => r.pageId === pageId && isFail(r),
    );
    const note =
      fails.length === 0
        ? "-"
        : fails
            .map(
              (f) =>
                `${f.viewportId}: ovf=${f.analysis?.overflowX ?? "?"}, img=${f.analysis?.brokenImageCount ?? "?"}`,
            )
            .join("; ");

    lines.push(
      `| ${pageId} | ${cells[0]} | ${cells[1]} | ${cells[2]} | ${cells[3]} | ${note} |`,
    );
  }

  const issues = report.results.filter(isFail);
  lines.push("");
  lines.push("## Bulgular (Oncelikli)");
  if (issues.length === 0) {
    lines.push("- Kritik issue bulunmadi.");
  } else {
    for (const issue of issues) {
      lines.push(
        `- ${issue.pageId}@${issue.viewportId}: status=${issue.status}, overflowX=${issue.analysis?.overflowX ?? 0}, brokenImages=${issue.analysis?.brokenImageCount ?? 0}`,
      );
      for (const offender of (issue.analysis?.offenders || []).slice(0, 3)) {
        lines.push(
          `  - offender: ${offender.selector} (left=${offender.left}, right=${offender.right}, width=${offender.width})`,
        );
      }
    }
  }

  lines.push("");
  lines.push("## Kabul Kriteri");
  lines.push("- Tum sayfalarda status=200");
  lines.push("- hasHorizontalOverflow=false");
  lines.push("- brokenImageCount=0");
  lines.push("- Ana CTA ve formlar mobilde tek kolon ve tasmadan gorunur");
  lines.push("");
  lines.push("## Tekrar Calistirma");
  lines.push("```bash");
  lines.push("node scripts/qa/mobile-regression-check.mjs");
  lines.push("```");

  await fs.mkdir(path.dirname(outPath), { recursive: true });
  await fs.writeFile(outPath, `${lines.join("\n")}\n`, "utf8");
  console.log(outPath);
};

run().catch((error) => {
  console.error(error);
  process.exit(1);
});
