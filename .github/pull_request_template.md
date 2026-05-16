<!--
SimdiGetir Site PR Template — Castagent Migration Faz 1
Tüm zorunlu alanları doldur. Boş bırakılan PR'lar review almaz.
-->

## 📋 Plan Referansı (ZORUNLU)

<!-- Castagent ekosisteminde plan-gated rule. Hangi plan dosyası bu PR'a kaynak?  -->
<!-- Format: plans/simdigetir-<konu>.md veya vault/clients/simdigetir/decisions/<karar>.md -->

Plan: `plans/...`

## 🎯 Değişiklik Özeti

<!-- 2-3 cümle: ne yapıldı, neden? -->

## 🔒 Conversion-Protect Checklist (ZORUNLU)

> Site SimdiGetir kurye/lojistik üretim sitesidir. Conversion path (tel/wa.me/Google Ads tag) kapanma = direkt gelir kaybı.
> Detay: `vault/clients/simdigetir/site/risk-map.md` (13 kritik öğe).

| Kontrol | Etkilenen? |
|---|---|
| `href="tel:+905513567292"` link sayısı | ⬜ Aynı / ⬜ Arttı / ⬜ AZALDI 🚨 |
| `href="https://wa.me/905513567292"` link sayısı | ⬜ Aynı / ⬜ Arttı / ⬜ AZALDI 🚨 |
| `.whatsapp-float` sticky button | ⬜ Korundu / ⬜ DEĞİŞTİ 🚨 |
| Hero CTA "Hemen Ara" + "WhatsApp" | ⬜ Korundu / ⬜ DEĞİŞTİ 🚨 |
| Footer "Kurye Ol" link (`/kurye-basvuru`) | ⬜ Korundu / ⬜ Silindi 🚨 |
| GTM container `GTM-WDBCNZV4` | ⬜ Korundu / ⬜ DEĞİŞTİ 🚨 |
| GA4 `G-XYCY1D28EF` config | ⬜ Korundu / ⬜ DEĞİŞTİ 🚨 |
| Google Ads `AW-17989545006` config | ⬜ Korundu / ⬜ DEĞİŞTİ 🚨 |
| Meta Pixel `1657531168735846` | ⬜ Korundu / ⬜ DEĞİŞTİ 🚨 |
| `gtag('event','click_phone',...)` listener | ⬜ Korundu / ⬜ DEĞİŞTİ 🚨 |
| `gtag('event','click_whatsapp',...)` listener | ⬜ Korundu / ⬜ DEĞİŞTİ 🚨 |
| KVKK Aydınlatma Metni link | ⬜ Korundu / ⬜ DEĞİŞTİ 🚨 |

**🚨 herhangi biri AZALDI/DEĞİŞTİ ise:** Yılmaz açık onayı + plan dosyasında etki analizi şart.

## ✅ Quality Gate

<!-- ./scripts/run-quality-gate.ps1 lokal sonucu -->

- [ ] Hygiene checks PASS
- [ ] PHP lint PASS (no syntax errors)
- [ ] Blade syntax PASS
- [ ] `php artisan test` PASS (veya pre-existing fail dökümante)

## 📦 Modular Boundary

<!-- AGENTS.md: "Maintain strict modular boundaries. Prefer changes under Modules/<ModuleName>." -->

- [ ] Sadece etkilenen modül(ler) dokunuldu
- [ ] Shared contract (route/DTO/DB schema) değişikliği YOK / VAR + gerekçe

Etkilenen modül(ler): `<modül adı>`

## 🧪 Test Plan

<!-- Bu PR'ı manuel test etmek için adımlar -->

- [ ] Local: `php artisan serve` + browser test
- [ ] Local: `curl localhost:8000 | grep tel/wa.me/widget` sayım kontrol
- [ ] Staging (eğer varsa)

## 🚀 Deploy Adımları

**Blade-only fix:**
```bash
ssh simdigetir-hostinger '
cd /home/u473759453/domains/simdigetir.com/current
git fetch origin master && git reset --hard FETCH_HEAD
php artisan view:clear && php artisan view:cache
php artisan config:clear && php artisan config:cache
php artisan route:clear && php artisan route:cache
bash scripts/release/hostinger-opcache-reset.sh
'
```

**Migration/composer değişikliği var:**
```bash
# Proper atomic cutover — yeni release klasörü + symlink switch
# (TARGET_RELEASE name + composer install + asset build + cutover)
```

## ↩️ Rollback Komutu

```bash
ssh simdigetir-hostinger '
cd /home/u473759453/domains/simdigetir.com/current
git reset --hard <PREVIOUS_SHA>  # Bu PR öncesi HEAD
php artisan view:cache && php artisan config:cache
bash scripts/release/hostinger-opcache-reset.sh
'
```

## 🔍 Post-Deploy Verification (CASTAGENT ZORUNLU)

```bash
curl -sL https://simdigetir.com/?cachebust=$(date +%s) -o /tmp/sdg.html
echo "tel: $(grep -c 'href=\"tel:+905513567292\"' /tmp/sdg.html) (≥9?)"
echo "wa.me: $(grep -c 'href=\"https://wa.me/905513567292' /tmp/sdg.html) (≥5?)"
echo "GTM: $(grep -c 'GTM-WDBCNZV4' /tmp/sdg.html) (≥1?)"
echo "GA4: $(grep -c 'G-XYCY1D28EF' /tmp/sdg.html) (≥1?)"
echo "AW: $(grep -c 'AW-17989545006' /tmp/sdg.html) (≥1?)"
echo "Pixel: $(grep -c '1657531168735846' /tmp/sdg.html) (≥1?)"
```

## 📚 Referans Belgeler (Castagent vault)

- Plan dosyası: <yukarıda Plan Referansı bölümü>
- Risk haritası: `vault/clients/simdigetir/site/risk-map.md`
- Current state: `vault/clients/simdigetir/site/current-state.md`
- Conv baseline (kaynak): `vault/clients/simdigetir/conversion-baseline.md`
- Memory: `feedback_simdigetir_conversion_protect`, `feedback_simdigetir_plan_gated`

---

🤖 PR template — Castagent Migration Faz 1 (2026-05-17). Sorular için Yılmaz.
