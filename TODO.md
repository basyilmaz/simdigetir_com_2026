# TODO

## Meta veri seti geçişi (2026-11-22)

- [ ] Bu değişikliği birleştir ve yayınla.
- [ ] Sunucuda önce dene: `php artisan ads:meta-repoint-pixel <ESKI_PIXEL_ID> 321760916162419 --dry-run`
- [ ] Sonra uygula: `php artisan ads:meta-repoint-pixel <ESKI_PIXEL_ID> 321760916162419`
- [ ] Sunucu `.env`: `META_PIXEL_ID=321760916162419`, `META_CAPI_ACCESS_TOKEN` değerini kullanıcı girer; ardından `php artisan config:cache`.
- [ ] Canlı `/` kaynağında `fbq('init', '321760916162419')` ve `facebook-domain-verification` etiketini doğrula.
- [ ] Meta Business'ta alan adını doğrula; Events Manager'da yeni veri setine olay geldiğini kontrol et.
