# Güvenlik Testi ve Penetrasyon Analizi Raporu
**Tarih:** 15 Mart 2026  
**Ortam:** Localhost (`http://127.0.0.1:8000`) + Kaynak Kodu Analizi  
**Yöntem:** Statik Kod Analizi + Dinamik Tarayıcı Testleri (Read-Only)

---

## Genel Değerlendirme

| Kritiklik | Bulgu Sayısı |
|---|---|
| 🔴 Kritik | 1 |
| 🟠 Yüksek | 2 |
| 🟡 Orta | 3 |
| 🟢 Düşük / Bilgi | 2 |
| ✅ Olumlu (Güvenli) | 7 |

---

## 🔴 KRİTİK BULGULAR

### 1. Panel/Dashboard Rotalarında Kimlik Doğrulama Yok (IDOR)
**Dosya:** `routes/web.php` (Satır 37-40)  
**Etki:** ÇOK YÜKSEK

```
/panel/customer/{user}  → Auth middleware YOK
/panel/courier/{courier} → Auth middleware YOK
/musteri-panel?user_id=X → Auth middleware YOK
/kurye-panel?courier_id=X → Auth middleware YOK
```

**Sorun:** Bu 4 rota herhangi bir `auth` veya `auth:sanctum` middleware'i tarafından korunmuyor. Herhangi bir saldırgan URL'deki `user` veya `courier` ID'sini değiştirerek (IDOR — Insecure Direct Object Reference) **başka müşterilerin siparişlerini**, kuryenin kazanç bilgilerini ve cüzdan hareketlerini görebilir.

**Kanıt:** `PanelController.php` → `customerDashboard()` ve `courierDashboard()` metodları gelen kullanıcının yetkisini kontrol etmeden doğrudan veriyi döndürüyor.

**Öneri:** Bu rotalar acil olarak `auth` middleware arkasına alınmalı ve kullanıcı sadece kendi verisini görmelidir:
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/panel/customer/{user}', ...);
    Route::get('/panel/courier/{courier}', ...);
});
// + Policy: $user->id === $requestedUser->id kontrolü
```

---

## 🟠 YÜKSEK ÖNCELİKLİ BULGULAR

### 2. Debug Modu Açık (Bilgi Sızıntısı)
**Dosya:** `.env` (Satır 4) — `APP_DEBUG=true`  
**Etki:** YÜKSEK

**Sorun:** Hata oluştuğunda Laravel Ignition debug sayfası açılarak sunucu dosya yolları (`C:\YazilimProjeler\...`), veritabanı sorguları, ortam değişkenleri ve stack trace bilgileri dışarıya sızdırılıyor.

**Durum:** Localhost'ta bu normaldir, ancak `.env.hostinger.production` dosyasında `APP_DEBUG=false` olduğu doğrulandı. Production güvende, fakat deploy öncesi bu kontrol mutlaka yapılmalıdır.

### 3. Güvenlik Başlıkları (Security Headers) Eksik
**Etki:** YÜKSEK

Yanıt (response) başlıklarında şu kritik güvenlik başlıkları eksik:

| Başlık | Durum | Koruduğu Saldırı |
|---|---|---|
| `X-Frame-Options` | ❌ Eksik | Clickjacking |
| `X-Content-Type-Options` | ❌ Eksik | MIME Sniffing |
| `Content-Security-Policy` | ❌ Eksik | XSS, Data Injection |
| `Strict-Transport-Security` | ❌ Eksik | SSL Downgrade |
| `Referrer-Policy` | ❌ Eksik | Referer Bilgi Sızıntısı |

**Öneri:** Bir middleware veya Nginx/Apache sunucu konfigürasyonu ile eklenmeli.

---

## 🟡 ORTA ÖNCELİKLİ BULGULAR

### 4. Login/Register Endpoint'lerinde Rate-Limiting Eksik
**Dosya:** `routes/api.php` (Satır 19)

**Sorun:** `/api/v1/auth/login` ve `/api/v1/auth/register` rotalarında `throttle` middleware'i tanımlı değil. Sadece `/api/v1/quotes` rotasında `throttle:30,1` bulunuyor. Bu durum, brute-force saldırısına (şifre deneme) davetiye çıkarır.

**Öneri:**
```php
Route::post('/auth/login', ...)->middleware('throttle:5,1');   // 5 deneme/dk
Route::post('/auth/register', ...)->middleware('throttle:3,1'); // 3 kayıt/dk
```

### 5. PHP Versiyon Bilgisi Açık (`X-Powered-By`)
**Etki:** ORTA

Yanıt başlığında `X-Powered-By: PHP/8.2.12` açıkça gösteriliyor. Saldırgan bu bilgiyle versiyon-spesifik açıkları hedefleyebilir.

**Öneri:** `php.ini` → `expose_php = Off`

### 6. Sanctum Token Süre Sınırı Yok
**Dosya:** `config/sanctum.php` (Satır 50) — `'expiration' => null`

**Sorun:** API token'ları süresiz geçerlidir. Bir token ele geçirildiğinde, kullanıcı çıkış yapana kadar kalıcı erişim sağlanır.

**Öneri:** `'expiration' => 1440` (24 saat) gibi makul bir süre belirlenmeli.

---

## 🟢 BİLGİ SEVİYESİ

### 7. Checkout Session CORS Kontrolü
Checkout session endpoint'leri (`/api/v1/checkout-sessions`) public erişime açık. Mevcut durumda `config/cors.php` dosyası bulunamadı. Laravel 11 CORS'u `bootstrap/app.php` üzerinden yönetiyor olabilir, ancak production'da izin verilen origin'lerin kısıtlanması gerekir.

### 8. API Yetkilendirme Yönlendirmesi
Yetkisiz `/api/v1/orders` gibi isteğe tarayıcıdan (browser) gidildiğinde JSON `401` yerine `/admin/login` sayfasına yönlendirme yapılıyor. Bu, REST API standardına uymaz fakat güvenlik açığı oluşturmaz.

---

## ✅ OLUMLU BULGULAR (Güvenli Alanlar)

| Alan | Test | Sonuç |
|---|---|---|
| Admin paneli koruma | `/admin` → Login'e yönlendirme | ✅ Güvenli |
| `.env` dosyası erişimi | `/.env` → 404 | ✅ Güvenli |
| SQL Injection koruması | Tüm sorgular Eloquent ORM, tek `DB::raw` safe | ✅ Güvenli |
| Password hashing | `User` model: `'password' => 'hashed'` cast | ✅ Güvenli |
| CSRF koruması | Sanctum middleware'de `ValidateCsrfToken` aktif | ✅ Güvenli |
| Form validasyonu | Tüm controller'larda `$request->validate()` kullanılıyor | ✅ Güvenli |
| Ödeme callback imza doğrulama | `PaymentSignatureService::verify()` çift kontrol | ✅ Güvenli |

---

## Acil Aksiyon Planı (Öncelik Sırasıyla)

| # | Aksiyon | Kritiklik | Tahmini Süre |
|---|---|---|---|
| 1 | Panel rotalarına `auth` middleware + Policy ekle | 🔴 Kritik | 30 dk |
| 2 | Login/Register'a `throttle` middleware ekle | 🟡 Orta | 10 dk |
| 3 | Security headers middleware ekle | 🟠 Yüksek | 20 dk |
| 4 | Sanctum token expiration ayarla | 🟡 Orta | 5 dk |
| 5 | `expose_php = Off` (php.ini) | 🟡 Orta | 2 dk |
| 6 | Production deploy öncesi `APP_DEBUG=false` doğrula | 🟠 Yüksek | Checklist |
