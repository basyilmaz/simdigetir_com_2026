# Repo Hygiene Policy

**Tarih:** 14 Mart 2026

## 1. Amac

Kirli worktree, encoding bozulmasi ve platforma gore satir sonu farklarindan kaynaklanan regressions'lari onlemek.

## 2. Zorunlu Kurallar

1. Yeni goreve baslamadan once worktree temiz olacak.
2. Tum ekipte git hooks aktif olacak.
3. Kod dosyalari UTF-8 (BOM'suz) ve LF olacak.
4. `desktop.ini`, `Thumbs.db`, `.DS_Store` gibi OS artefaktlari commit edilmeyecek.
5. `vendor/` ve `node_modules/` altindan dosya commit edilmeyecek.
6. Push oncesi quality gate gecerli olacak.
7. Push oncesi `VERSION` degisimi zorunlu olacak.

## 3. Kurulum

```powershell
./scripts/hygiene/install-git-hooks.ps1
```

## 4. Baseline Temizligi (Tek Seferlik)

Repo gecmisinde takip edilen OS artefaktlari varsa indeks disina alin:

```powershell
./scripts/hygiene/untrack-os-artifacts.ps1
```

Sadece raporlamak icin:

```powershell
./scripts/hygiene/untrack-os-artifacts.ps1 -WhatIf
```

## 5. Gunluk Kullanim

Yeni bir goreve gecmeden:

```powershell
./scripts/hygiene/assert-clean-worktree.ps1
```

Manuel hygiene dogrulamasi:

```powershell
php scripts/hygiene/check-file-hygiene.php --staged
```

## 6. CI Politika Eslesmesi

`.github/workflows/quality-gate.yml`:
- changed files icin hygiene check
- version bump check
- quality gate
