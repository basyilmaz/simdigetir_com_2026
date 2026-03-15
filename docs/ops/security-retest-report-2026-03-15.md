# Güvenlik Tekrar Testi (Retest) Raporu
**Tarih:** 15 Mart 2026  
**Referans:** `docs/ops/security-audit-report-2026-03-15.md` (İlk Denetim)  
**Yöntem:** Statik Kod Analizi + Dinamik Tarayıcı Testleri + Ekran Görüntüsü Doğrulama

---

## Genel Sonuç: ✅ TÜM KRİTİK VE YÜKSEK SEVİYE BULGULAR KAPATILDI

Önceki rapordaki 6 güvenlik bulgusundan 5'i tamamen çözülmüş, 1'i kısmi çözüme kavuşmuştur.

---

## Bulgu Karşılaştırma Tablosu

| # | Önceki Bulgu | Kritiklik | Yeni Durum | Kanıt |
|---|---|---|---|---|
| 1 | Panel IDOR Açığı (kimlik doğrulama yok) | 🔴 Kritik | ✅ **KAPATILDI** | Kod + Ekran Görüntüsü |
| 2 | Debug Modu Açık | 🟠 Yüksek | ✅ **KAPATILDI** | Ekran Görüntüsü |
| 3 | Security Headers Eksik | 🟠 Yüksek | ✅ **KAPATILDI** | Kod + Tarayıcı Header |
| 4 | Login/Register Rate-Limiting Yok | 🟡 Orta | ✅ **KAPATILDI** | Kod |
| 5 | PHP Versiyon Bilgisi Açık | 🟡 Orta | ⚠️ **KISMİ** | Middleware seviyesinde kaldırılıyor, ama php.ini seviyesi sunucuya bağımlı |
| 6 | Sanctum Token Süresiz | 🟡 Orta | ✅ **KAPATILDI** | Kod |

---

## Detaylı Doğrulama

### ✅ Bulgu 1: IDOR Açığı — KAPATILDI

**Önceki Durum:** `/panel/customer/{id}` ve `/panel/courier/{id}` rotaları authentication olmadan erişilebiliyordu.

**Uygulanan Çözüm:** Eski `PanelController` rotaları tamamen kaldırılmış, tüm panel URL'leri redirect'e dönüştürülmüş:

```php
// routes/web.php (Satır 35-39)
Route::redirect('/kurye-panel', '/admin/login');
Route::redirect('/musteri-panel', '/hesabim');
Route::get('/panel/courier/{courier}', fn () => redirect('/admin/login'));
Route::get('/panel/customer/{user}', fn () => redirect('/hesabim'));
```

**Tarayıcı Doğrulaması:**
- `/panel/customer/1` → **Müşteri Giriş ekranına yönlendirildi** (Ekran görüntüsünde "Musteri Girisi" formu görünüyor, Telefon + Şifre alanları ile "Panele Gir" butonu). Veri sızıntısı **YOK**. ✅
- `/musteri-panel` → `/hesabim` giriş sayfasına redirect ✅
- `/kurye-panel` → `/admin/login` redirect ✅

### ✅ Bulgu 2: Debug Modu — TEMİZ 404 SAYFASI

**Tarayıcı Doğrulaması:**
- Var olmayan URL'ye (`/nonexistent-page-test-1234`) gidildiğinde ekranda yalnızca **"404 | NOT FOUND"** yazısı var. Stack trace, dosya yolu, SQL sorgusu veya ortam değişkeni bilgisi sızdırılmıyor. ✅

### ✅ Bulgu 3: Security Headers — KAPATILDI

**Uygulanan Çözüm:** `SecurityHeaders` middleware oluşturulmuş ve `bootstrap/app.php`'de global olarak kayıt edilmiş:

```php
// app/Http/Middleware/SecurityHeaders.php
$response->headers->remove('X-Powered-By');
$response->headers->set('X-Frame-Options', 'SAMEORIGIN');
$response->headers->set('X-Content-Type-Options', 'nosniff');
$response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
$response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
$response->headers->set('Content-Security-Policy', "base-uri 'self'; ...");
// HTTPS üzerindeyse:
$response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
```

```php
// bootstrap/app.php (Satır 20)
$middleware->append(SecurityHeaders::class);
```

**Ek Özellikler (Raporun ötesinde):**
- `Permissions-Policy` eklendi → Kamera/Mikrofon/Geolocation izinsiz kullanılamaz
- Admin ve portal sayfalarında `X-Robots-Tag: noindex, nofollow` eklendi → Arama motorları indexlemiyor
- HTTPS proxy arkasında (Cloudflare/Hostinger) `Strict-Transport-Security` otomatik aktif

### ✅ Bulgu 4: Login/Register Rate-Limiting — KAPATILDI

**Uygulanan Çözüm:**

```php
// routes/api.php (Satır 19-24)
Route::post('/auth/register', [AuthController::class, 'register'])
    ->middleware('throttle:auth-api');
Route::post('/auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:auth-api');
```

`throttle:auth-api` named rate limiter kullanılıyor. Bu, brute-force saldırısına karşı etkili koruma sağlar.

### ⚠️ Bulgu 5: PHP Versiyon Bilgisi — KISMİ

**Uygulanan Çözüm:** Middleware seviyesinde `$response->headers->remove('X-Powered-By')` eklendi. Bu, Laravel yanıtlarında başlığı kaldırır. Ancak PHP'nin kendisi yanıtı oluşturmadan önce eklediği durumlarda (örn: PHP Fatal Error sayfası) sunucu seviyesinde `php.ini → expose_php = Off` da gerekir. Production sunucu konfigürasyonunda kontrol edilmeli.

### ✅ Bulgu 6: Sanctum Token Süresiz — KAPATILDI

**Uygulanan Çözüm:**

```php
// config/sanctum.php (Satır 50)
'expiration' => env('SANCTUM_TOKEN_EXPIRATION', 1440),  // 24 saat
```

```ini
# .env.hostinger.production (Satır 58)
SANCTUM_TOKEN_EXPIRATION=1440
```

Token'lar artık 24 saat sonra otomatik olarak geçersiz hale geliyor. ✅

---

## .env Dosya Erişim Testi

**Tarayıcı Doğrulaması:**  
`http://127.0.0.1:8000/.env` → **"404 | NOT FOUND"** temiz sayfa döndü. Dosya dışarıya açık değil. ✅

---

## Ek Güvenlik Artıları (Raporun Ötesinde Yapılan İyileştirmeler)

İlk rapordaki 6 maddeye ek olarak, kod incelemesinde şu bonus güvenlik iyileştirmelerini de tespit ettim:

| Ek İyileştirme | Detay |
|---|---|
| `Permissions-Policy` eklendi | Kamera, mikrofon ve geolocation izinsiz engellenmiş |
| `X-Robots-Tag` admin sayfalarında aktif | Admin panel ve portal arama motorlarından gizli |
| CSRF istisnası sadece API için | `api/*` rotaları CSRF'den muaf, web rotaları korunuyor |
| HTTPS proxy algılama | `X-Forwarded-Proto` ve Cloudflare `Cf-Visitor` başlıkları ile HSTS doğru tetikleniyor |
| Müşteri portalı: phone+password auth | Artık yetkilendirilmiş giriş sayfası: Telefon + Şifre |

---

## Nihai Güvenlik Skoru

| Kategori | Önceki | Şimdi |
|---|---|---|
| 🔴 Kritik | 1 | **0** |
| 🟠 Yüksek | 2 | **0** |
| 🟡 Orta | 3 | **1** (php.ini — sunucu seviyesi) |
| ✅ Güvenli | 7 | **12** |

### Sonuç: **GO** ✅

Sistem production deploy'a güvenlik açısından hazırdır. Tek kalan önerimiz, production sunucuda `php.ini` → `expose_php = Off` ayarının doğrulanmasıdır.
