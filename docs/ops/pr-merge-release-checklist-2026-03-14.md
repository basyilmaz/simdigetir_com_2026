# SimdiGetir PR Merge Ready Release Checklist (2026-03-14)

## Kapsam

- Hedef: Mevcut NOGO durumunu kontrollu sekilde GO'ya tasimak.
- Politika: Deploy-last, quality gate zorunlu, P0/P1 sifir olmadan merge/deploy yok.
- Referanslar:
  - `docs/ops/controlled-delivery-plan-2026-03-14.md`
  - `docs/ops/hostinger-release-governance-checklist-2026-03-10.md`
  - `docs/ops/versioning-policy.md`

## Baseline (2026-03-14)

- Current branch: `release/2026-03-14-gate-go`
- Release lock commit: `7e6920a8`
- VERSION (current): `1.0.7`
- Existing tags: `v1.0.1`, `v1.0.2`, `v1.0.3`, `v1.0.6` (+ `v1.0.7` target)

## PR Merge Ready Checklist

Durum anahtari:
- `PASS`: Kapanmis
- `FAIL`: Bloklayici
- `PENDING`: Henuz tamamlanmadi

1. Worktree clean kurali
- Check: `./scripts/hygiene/assert-clean-worktree.ps1`
- Current: `PASS`
- Not: Final release commit sonrasi temiz worktree tekrar dogrulandi.

2. Hook enforcement aktifligi
- Check: `git config --get core.hooksPath`
- Current: `PASS` (`.githooks`)

3. UTF-8 BOM'suz + LF kurali
- Check: `.editorconfig`, `.gitattributes`, pre-commit hygiene kontrolu
- Current: `PASS`

4. OS artifact/kirik git ref temizligi
- Check: `.git/refs/**/desktop.ini` kalintilari
- Current: `PASS`
- Not: kalinti sayisi `0`.

5. Version bump policy enforcement
- Check: `.githooks/pre-push` + `.github/workflows/quality-gate.yml`
- Current: `PASS` (kurallar var)

6. Version-tag uyumu
- Check: `VERSION` ve tag parity
- Current: `PASS` (`VERSION=1.0.7`, release tag `v1.0.7`)

7. Zorunlu quality gate
- Check: `./scripts/run-quality-gate.ps1`
- Current: `PASS` (142 test / 732 assertion)

8. P0/P1 acik issue sifirlama
- Check: release backlog kaydi
- Current: `PASS` (`docs/ops/release-backlog-status-2026-03-14.md`)

9. Frontend-backend parity matrisi guncelligi
- Check: `docs/ops/frontend-backend-gonogo-matrix-2026-03-10.md`
- Current: `PASS`

10. Rollback hazirlik kaniti
- Check: rollback adimlari + prova kaydi
- Current: `PASS` (`docs/ops/release-rollback-drill-2026-03-14.md`)

11. Staging/preflight raporu
- Check: `storage/app/qa/hostinger-preflight/*/report.json`
- Current: `PASS` (`2026-03-14-070805`, quality gate dahil tum adimlar PASS, decision=GO)

12. Final GO/NOGO karari
- Current: `GO`
- Gecis kosulu: Asagidaki 6 adimin tamami `PASS`.

## NOGO -> GO Kapanis Listesi (6 Adim, net sira)

### Adim 1 - Freeze ve temiz baseline kaydi

- Kapanis kosulu:
  - Worktree temiz.
  - Broken ref kalintilari temiz.
  - Baseline kaydi (commit/tag + DB backup ref + env checklist hash) dokumante.
- Komutlar:
  - `./scripts/hygiene/assert-clean-worktree.ps1`
  - `Get-ChildItem .git/refs -Recurse | ? { $_.Name -match 'desktop\.ini' }`
- Durum: `CLOSED (PASS)`

### Adim 2 - Tek backlog ve P0/P1 sifirlama plani

- Kapanis kosulu:
  - Tum acik isler tek backlog dokumaninda.
  - P0/P1 maddeleri net owner ve due-date ile atanmis.
- Kanit:
  - `docs/ops/*backlog*` veya release issue listesi.
- Durum: `CLOSED (PASS)`

### Adim 3 - Quality gate'i yesile cekme

- Kapanis kosulu:
  - `./scripts/run-quality-gate.ps1` tam `PASS`.
  - Test regressions kapanmis.
- Durum: `CLOSED (PASS)`

### Adim 4 - Staging/preflight'i quality gate ile tekrar alma

- Kapanis kosulu:
  - Preflight raporunda:
    - Quality Gate = `PASS`
    - Backend Regression = `PASS`
    - Frontend Regression = `PASS`
    - Strict Env = `PASS`
  - Final decision = `GO`
- Komut:
  - `./scripts/run-hostinger-preflight.ps1 -EnvFile .env.hostinger.production`
- Durum: `CLOSED (PASS)`

### Adim 5 - Release kimligi (version + tag) kilitleme

- Kapanis kosulu:
  - `VERSION` bump yapildi.
  - Release tag olusturuldu ve push edildi.
  - Release note olusturuldu.
- Komutlar:
  - `php scripts/version/bump-version.php --part=patch`
  - `git tag <release_tag>`
  - `git push origin <branch> --tags`
- Durum: `CLOSED (PASS)`

### Adim 6 - Merge readiness ve GO karari

- Kapanis kosulu:
  - Adim 1-5 PASS.
  - P0=0, P1=0.
  - Rollback prova kanitli.
  - PR checklist tamamen PASS.
- Durum: `CLOSED (GO)`

## Tek Tek Kapanis Logu

- [x] Adim 1 kapandi
- [x] Adim 2 kapandi
- [x] Adim 3 kapandi
- [x] Adim 4 kapandi
- [x] Adim 5 kapandi
- [x] Adim 6 kapandi (GO)

## Sonuc (bu an)

- PR merge-ready.
- Durum: `GO`
- Kanitlar:
  - Quality gate: `PASS`
  - Preflight report: `storage/app/qa/hostinger-preflight/2026-03-14-070805/report.json` (`GO`)
  - Rollback prova: `docs/ops/release-rollback-drill-2026-03-14.md`
