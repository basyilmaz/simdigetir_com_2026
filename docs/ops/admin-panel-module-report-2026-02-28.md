# Admin Panel Erişim ve Modül Durum Raporu (2026-02-28)

## 1) Yönetici Paneli Erişim Bilgileri

### Panel Path ve Auth
- Filament panel path: `/admin`
- Login endpoint: `/admin/login`
- Çıkış endpoint: `/admin/logout`
- Dashboard endpoint: `/admin`
- Kaynak: `app/Providers/Filament/AdminPanelProvider.php`

### Ortam Bazlı URL
- Production APP_URL: `https://simdigetir.com`
- Production admin URL: `https://simdigetir.com/admin`
- Local örnek APP_URL (`.env.example`): `http://localhost`
- Local admin URL (örnek): `http://localhost/admin`

## 2) Admin Kullanıcı Bilgisi Kaynağı

- Seeder dosyası: `database/seeders/AdminUserSeeder.php`
- Kullanılan env alanları:
  - `ADMIN_NAME`
  - `ADMIN_EMAIL`
  - `ADMIN_PASSWORD`
- `.env` içinde bu alanlar tanımlı değil.
- Seeder fallback değerleri:
  - Email: `admin@simdigetir.com`
  - Ad: `Admin`
  - Şifre fallback: `change-me-in-env`

Not:
- Gerçek DB’de aktif admin kullanıcısının varlığı bu raporda doğrulanmamıştır.
- Güvenlik için `ADMIN_PASSWORD` değeri production `.env` içinde açıkça set edilmelidir.

## 3) Modül Durumları

Dosya: `modules_statuses.json`

- `Settings`: `true` (aktif)
- `Leads`: `true` (aktif)
- `Landing`: `true` (aktif)

Klasör doğrulaması (`Modules/`):
- `Modules/Landing`
- `Modules/Leads`
- `Modules/Settings`

## 4) Provider ve Panel Yükleme Durumu

`bootstrap/providers.php` içinde aktif provider'lar:
- `App\Providers\AuthServiceProvider`
- `App\Providers\AppServiceProvider`
- `App\Providers\Filament\AdminPanelProvider`

Değerlendirme:
- Admin panel provider bootstrap seviyesinde yüklü.
- Panel path/id ve middleware zinciri doğru yapılandırılmış.

## 5) Admin Route Kapsamı Özeti

Komut: `php artisan route:list --path=admin`

Toplam admin route: `42`

Öne çıkan alanlar:
- Auth: login/logout
- Dashboard
- Landing page builder kaynakları (landing pages, sections, items, revisions)
- Leads
- Settings (Manage Settings page)
- Orders / Payment Transactions / Pricing Rules
- Couriers / Settlements / Support Tickets / Sitemap Entries / Form Definitions / Legal Documents

## 6) Riskler ve Önerilen Aksiyonlar

1. `ADMIN_*` env alanlarını production ortamda açıkça tanımlayın.
2. Fallback şifre (`change-me-in-env`) kullanımını engellemek için deploy check ekleyin.
3. Admin erişim için IP allowlist veya ek MFA değerlendirmesi yapın.
4. Raporu release checklist'e ekleyip her deploy öncesi `route:list --path=admin` snapshot alın.

## 7) Kullanılan Kaynak Dosyalar

- `.env`
- `.env.example`
- `app/Providers/Filament/AdminPanelProvider.php`
- `database/seeders/AdminUserSeeder.php`
- `modules_statuses.json`
- `bootstrap/providers.php`
- `php artisan route:list --path=admin` çıktısı
