# SimdiGetir Kontrollu Gelistirme ve Yayin Plani

**Tarih:** 14 Mart 2026  
**Politika:** Canliya alim en son asama

## 1. Hedef

Gelistirmeyi kontrollu, geri alinabilir ve olculebilir hale getirmek; production ortamina sadece tam gate sonrasi gecmek.

## 2. Fazlar

1. Baseline ve freeze
- Mevcut canli commit/tag kaydi
- DB yedek referansi
- Ortam degiskenleri checklist kaydi

2. Kapsam ve oncelik
- Tum isler `P0/P1/P2` siniflandirilir
- Her is icin test edilebilir kabul kriteri yazilir

3. Gelistirme (deploy yok)
- Moduler sinirlarla branch bazli implementasyon
- Her degisiklik batch'i sonrasi kalite kapisi

4. Staging dogrulama
- E2E test matrisi (frontend, backend, admin, reklam)
- Uygulama loglari ve hata oranlari kontrolu

5. Release gate
- GO/NOGO toplantisi
- Rollback provasi
- Versiyon bump + tag hazirligi

6. Canliya alim (son adim)
- Onayli pencere
- Smoke test
- 30-60 dk yakin izleme

## 3. Skill Eslestirme

- `simdigetir-delivery-governor`
  - Plan disiplini, branch kurallari, quality-gate zorunlulugu, deploy-last
- `simdigetir-frontend-backend-parity`
  - UI/API eslesme matrisi, kapsama eksikleri, parity GO/NOGO
- `simdigetir-admin-ads-ops`
  - Reklam baglantilari, Pixel/CAPI akis, conversion dogrulama
- `simdigetir-release-gate`
  - Son gate, rollback hazirligi, versiyon/tag, canli smoke

Destekleyici mevcut skill set:
- `frontend-delivery`
- `backend-delivery`
- `qa-test-automation`
- `db-migration-safety`
- `release-governance`
- `modular-safe-delivery`

## 4. Zorunlu Kurallar

1. Canli ortama dogrudan coding/deploy yok.
2. `P0` veya `P1` acikken release yok.
3. Yeni goreve baslamadan once worktree temiz olmalidir:

```powershell
./scripts/hygiene/assert-clean-worktree.ps1
```

4. Git hook kurulumlari tum ekipte zorunludur:

```powershell
./scripts/hygiene/install-git-hooks.ps1
```

5. Kod dosyalari UTF-8 (BOM'suz) + LF olmak zorundadir (hook ile denetlenir).
6. Kod degisikligi sonrasi kalite kapisi calistirilir:

```powershell
./scripts/run-quality-gate.ps1
```

7. Her GitHub push'ta `VERSION` guncellenir.
8. Her canliya alimda yeni release version + tag uretilir.
9. Rollback adimlari test edilmeden `GO` verilmez.

## 5. Cikis Kriteri (GO)

- Tum zorunlu checklist maddeleri `pass`
- `P0=0` ve `P1=0`
- Quality gate yesil
- Kritik E2E senaryolari basarili
- Rollback kanitli

## 6. Isletim Notu

Bu plan aktif oldugu surece once gelistirme + staging + gate yapilir; production deployment sadece son kapida uygulanir.
