# SimdiGetir - Admin Panel Geliştirme Roadmap

## 📊 Mevcut Durum (v1.0.0)

### ✅ Tamamlanan Özellikler
- [x] Filament 3.3 Admin Panel kurulumu
- [x] Lead (Müşteri Talepleri) yönetimi
- [x] Settings (Site Ayarları) yönetimi
- [x] Admin kullanıcı sistemi
- [x] Email verified kontrolü

---

## 🎯 Phase 1: Kullanıcı Yönetimi (v1.1.0)

### 1.1 Rol & İzin Sistemi

**Roller:**
- 🔴 **Super Admin** - Tüm yetkiler
- 🟡 **Admin** - Sipariş ve kullanıcı yönetimi
- 🟢 **Staff** - Sadece görüntüleme ve sipariş düzenleme
- 🔵 **Kurye** - Sadece kendi siparişlerini görür

**Gerekli Paketler:**
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

**Dosyalar:**
- `app/Filament/Resources/RoleResource.php` - Rol yönetimi
- `app/Filament/Resources/PermissionResource.php` - İzin yönetimi
- `database/seeders/RolePermissionSeeder.php` - Varsayılan roller

**İzinler (Permissions):**
- `view_leads` - Lead'leri görüntüleme
- `create_leads` - Lead oluşturma
- `edit_leads` - Lead düzenleme
- `delete_leads` - Lead silme
- `view_orders` - Siparişleri görüntüleme
- `manage_users` - Kullanıcı yönetimi
- `manage_settings` - Site ayarları
- `view_analytics` - İstatistikleri görüntüleme

### 1.2 User (Kullanıcı) Yönetimi

**Özellikler:**
- Kullanıcı listesi (tablo görünümü)
- Kullanıcı ekleme/düzenleme/silme
- Rol atama
- Email doğrulama durumu
- Aktif/Pasif durum
- Son giriş tarihi
- Kullanıcı profil bilgileri

**Filament Resource:**
```php
// app/Filament/Resources/UserResource.php
- name, email, phone
- role selection
- email_verified_at
- is_active toggle
- created_at, updated_at
```

**Filtreler:**
- Rol bazında filtreleme
- Aktif/Pasif filtreleme
- Email doğrulanmış/doğrulanmamış

**Aksiyonlar:**
- Toplu email gönderme
- Toplu aktif/pasif yapma
- Password reset linki gönderme

---

## 🎨 Phase 2: Dashboard & İstatistikler (v1.2.0)

### 2.1 Özet Dashboard

**Widgets:**
1. **Bugünkü İstatistikler**
   - Toplam lead sayısı (bugün)
   - Yeni siparişler (bugün)
   - Aktif kuryeler (şu anda)
   - Tamamlanan teslimatlar (bugün)

2. **Haftalık/Aylık Grafikler**
   - Lead trend grafiği (son 30 gün)
   - Sipariş trend grafiği
   - Gelir trend grafiği (opsiyonel)

3. **Son Aktiviteler**
   - Son 10 lead
   - Son 10 sipariş
   - Son kullanıcı girişleri

**Filament Widgets:**
```php
// app/Filament/Widgets/StatsOverview.php
- TotalLeads
- TodayOrders
- ActiveCouriers
- CompletedDeliveries

// app/Filament/Widgets/LeadChart.php
- Line chart (son 30 gün)

// app/Filament/Widgets/RecentLeads.php
- Table widget
```

### 2.2 Analytics (İstatistikler)

**Raporlar:**
- Lead kaynak analizi (hangi sayfadan geldi)
- İlçe bazında lead dağılımı
- Mahalle bazında popülerlik
- Saatlik lead yoğunluğu
- Kurye performans raporu

**Export:**
- Excel export (Laravel Excel)
- PDF export (DomPDF)
- CSV export

---

## 📦 Phase 3: Sipariş Yönetimi (v1.3.0)

### 3.1 Order (Sipariş) Modeli

**Database Schema:**
```sql
orders table:
- id
- order_number (unique)
- customer_name
- customer_phone
- customer_email
- pickup_address (nereden)
- delivery_address (nereye)
- pickup_district_id
- delivery_district_id
- pickup_lat, pickup_lng
- delivery_lat, delivery_lng
- distance (km)
- estimated_price
- final_price
- status (pending, assigned, picked_up, in_transit, delivered, cancelled)
- courier_id (nullable)
- assigned_at
- picked_up_at
- delivered_at
- notes
- created_at, updated_at
```

**Order Statuses:**
- 🟡 **Pending** - Beklemede
- 🔵 **Assigned** - Kuryeye atandı
- 🟢 **Picked Up** - Kuryede
- 🚚 **In Transit** - Yolda
- ✅ **Delivered** - Teslim edildi
- ❌ **Cancelled** - İptal edildi

### 3.2 Order Resource

**Özellikler:**
- Sipariş listesi
- Sipariş oluşturma formu
- Kurye atama
- Durum güncelleme
- Harita görünümü (Google Maps API)
- Mesafe hesaplama
- Fiyat hesaplama

**Filament Forms:**
```php
// Customer Info
TextInput::make('customer_name')
TextInput::make('customer_phone')
TextInput::make('customer_email')

// Pickup
TextInput::make('pickup_address')
Select::make('pickup_district_id')

// Delivery
TextInput::make('delivery_address')
Select::make('delivery_district_id')

// Courier Assignment
Select::make('courier_id')
    ->relationship('courier', 'name')
    ->searchable()

// Status
Select::make('status')
    ->options([...])
```

**Aksiyonlar:**
- Kurye ata
- Durumu güncelle
- WhatsApp bildirimi gönder
- SMS gönder
- Email gönder
- Siparişi iptal et

### 3.3 Otomatik Kurye Atama

**Algoritma:**
- Müsait kuryeler listesi
- En yakın kurye bulma (GPS koordinat)
- Kurye yükü dengeleme
- Öncelik sistemi

---

## 🚴 Phase 4: Kurye Yönetimi (v1.4.0)

### 4.1 Courier (Kurye) Başvuruları

**Database Schema:**
```sql
courier_applications table:
- id
- name
- phone
- email
- tc_no
- birth_date
- address
- district_id
- driving_license_type
- vehicle_type (motosiklet, bisiklet, araba)
- vehicle_plate
- has_helmet
- is_smoker
- criminal_record_file (nullable)
- photo_file (nullable)
- status (pending, approved, rejected)
- notes
- reviewed_by (admin user id)
- reviewed_at
- created_at, updated_at
```

**Courier Application Resource:**
- Başvuru listesi
- Detay görünümü
- Onaylama/Reddetme
- Email bildirimi
- Notlar ekleme

### 4.2 Courier (Kurye) Yönetimi

**Database Schema:**
```sql
couriers table:
- id
- user_id (foreign key to users)
- name
- phone
- email
- tc_no
- district_id (ana çalışma bölgesi)
- vehicle_type
- vehicle_plate
- rating (1-5)
- total_deliveries
- is_available (müsait mi?)
- current_lat, current_lng (GPS konum)
- last_location_update
- is_active
- created_at, updated_at
```

**Courier Resource:**
- Kurye listesi
- Kurye profili
- Teslimat geçmişi
- Performans metrikleri
- GPS konum takibi
- Müsaitlik durumu

**Kurye Dashboard (Courier Panel):**
- Kurye için ayrı panel
- Atanan siparişler
- Sipariş detayları
- Durum güncelleme
- Harita görünümü
- Kazanç raporu

---

## 📢 Phase 5: Reklam & Banner Yönetimi (v1.5.0)

### 5.1 Banner Yönetimi

**Database Schema:**
```sql
banners table:
- id
- title
- description
- image_path
- link_url
- position (home_hero, home_sidebar, kurye_page)
- is_active
- start_date
- end_date
- click_count
- impression_count
- order (sıralama)
- created_at, updated_at
```

**Banner Pozisyonları:**
- `home_hero` - Ana sayfa hero bölümü
- `home_sidebar` - Ana sayfa sidebar
- `kurye_page` - Kurye sayfaları
- `footer` - Footer

**Banner Resource:**
```php
// app/Filament/Resources/BannerResource.php
- title, description
- image upload (Filament FileUpload)
- link URL
- position select
- is_active toggle
- date range picker (start/end)
- order number
```

**Özellikler:**
- Drag & drop sıralama
- Resim önizleme
- Otomatik aktif/pasif (tarih bazlı)
- Click/Impression tracking
- A/B testing

### 5.2 Kampanya Yönetimi

**Database Schema:**
```sql
campaigns table:
- id
- name
- description
- discount_type (percentage, fixed)
- discount_value
- code (promo kodu)
- min_order_amount
- max_discount_amount
- usage_limit (toplam kullanım)
- usage_count (kullanılan)
- per_user_limit
- is_active
- start_date
- end_date
- created_at, updated_at
```

**Campaign Resource:**
- Kampanya listesi
- Kampanya oluşturma
- İndirim kodu
- Kullanım limiti
- Tarih aralığı
- İstatistikler

---

## 📧 Phase 6: Bildirim Sistemi (v1.6.0)

### 6.1 Email Bildirimleri

**Laravel Notifications:**
```php
// app/Notifications/
- NewLeadNotification (admin'e)
- OrderAssignedNotification (kuryeye)
- OrderStatusNotification (müşteriye)
- WelcomeNotification (yeni kullanıcıya)
```

**Email Templates:**
- Sipariş onayı
- Kurye ataması
- Teslimat tamamlandı
- Lead alındı

### 6.2 SMS Bildirimleri

**SMS Gateway Entegrasyonu:**
- NetGSM, İletimerkezi, veya Twilio
- Sipariş durumu SMS'leri
- Onay kodları
- Kurye bilgilendirme

### 6.3 WhatsApp Bildirimleri

**WhatsApp Business API:**
- Sipariş özeti
- Kurye bilgileri
- Teslimat güncellemeleri

---

## 🔧 Phase 7: Gelişmiş Özellikler (v2.0.0)

### 7.1 Ödeme Sistemi

**Payment Gateway:**
- İyzico entegrasyonu
- PayTR entegrasyonu
- Kredi kartı ile ödeme
- Online ödeme takibi

**Database:**
```sql
payments table:
- id
- order_id
- amount
- payment_method
- transaction_id
- status
- paid_at
```

### 7.2 Canlı Kurye Takibi

**Real-time Tracking:**
- Socket.io veya Pusher
- Canlı GPS koordinatları
- Harita üzerinde kurye gösterimi
- Müşteri için tracking sayfası

### 7.3 API Geliştirme

**Mobile App için REST API:**
```
POST /api/orders - Sipariş oluştur
GET /api/orders/{id} - Sipariş detayı
PUT /api/courier/location - Kurye konumu güncelle
GET /api/courier/orders - Kurye siparişleri
```

**API Documentation:**
- Swagger/OpenAPI
- Postman collection

### 7.4 Raporlama Modülü

**Gelişmiş Raporlar:**
- Gelir raporu
- Kurye performans raporu
- İlçe bazında analiz
- Müşteri memnuniyet raporu
- Zaman bazlı yoğunluk raporu

---

## 📱 Phase 8: Mobil Uygulama (v2.1.0)

### 8.1 Kurye Mobil Uygulaması

**Flutter/React Native:**
- Sipariş listesi
- Sipariş detayı
- Harita navigasyon
- Durum güncelleme
- Push notification

### 8.2 Müşteri Mobil Uygulaması

**Özellikler:**
- Sipariş verme
- Canlı takip
- Geçmiş siparişler
- Favori adresler
- İndirim kodları

---

## 🛠️ Teknik Gereksinimler

### Database Migrations
```bash
# Sırasıyla çalıştırılacak migration'lar
php artisan make:migration create_roles_and_permissions_tables
php artisan make:migration create_orders_table
php artisan make:migration create_couriers_table
php artisan make:migration create_courier_applications_table
php artisan make:migration create_banners_table
php artisan make:migration create_campaigns_table
php artisan make:migration create_payments_table
```

### Composer Paketleri
```bash
# Rol & İzin
composer require spatie/laravel-permission

# Excel Export
composer require maatwebsite/excel

# PDF Export
composer require barryvdh/laravel-dompdf

# SMS
composer require netgsm/netgsm-php

# Payment
composer require iyzico/iyzipay-php

# Real-time
composer require pusher/pusher-php-server

# API Documentation
composer require darkaonline/l5-swagger
```

### Frontend Paketleri
```bash
# Harita
npm install @googlemaps/js-api-loader

# Charts
npm install chart.js

# Real-time
npm install pusher-js
```

---

## 📋 Öncelik Sıralaması

### 🔴 Yüksek Öncelik (İlk 2 Ay)
1. ✅ Kullanıcı yönetimi (Roller & İzinler)
2. ✅ Dashboard & İstatistikler
3. ✅ Sipariş yönetimi (temel)

### 🟡 Orta Öncelik (3-4 Ay)
4. ✅ Kurye yönetimi
5. ✅ Banner & Reklam yönetimi
6. ✅ Email bildirimleri

### 🟢 Düşük Öncelik (5-6 Ay)
7. ✅ SMS & WhatsApp bildirimleri
8. ✅ Ödeme sistemi
9. ✅ Canlı takip

### 🔵 Gelecek Planlar (6+ Ay)
10. ✅ API geliştirme
11. ✅ Mobil uygulama
12. ✅ Gelişmiş raporlama

---

## 🎯 Versiyon Planı

- **v1.0.0** (Şu An) - Landing pages + Basic admin
- **v1.1.0** - Kullanıcı yönetimi + Roller
- **v1.2.0** - Dashboard + İstatistikler
- **v1.3.0** - Sipariş yönetimi
- **v1.4.0** - Kurye yönetimi
- **v1.5.0** - Banner & Kampanya
- **v1.6.0** - Bildirimler
- **v2.0.0** - Ödeme + Canlı takip + API
- **v2.1.0** - Mobil uygulama

---

## 📝 Development Checklist Template

Her feature için:
```markdown
### Feature: [Feature Adı]

**Status:** 🔴 Not Started | 🟡 In Progress | 🟢 Completed

**Tasks:**
- [ ] Database migration oluştur
- [ ] Model oluştur
- [ ] Filament Resource oluştur
- [ ] Form validation
- [ ] Testler yaz
- [ ] Documentation
- [ ] Git commit
- [ ] Production deploy

**Files:**
- database/migrations/xxx_create_xxx_table.php
- app/Models/Xxx.php
- app/Filament/Resources/XxxResource.php
- tests/Feature/XxxTest.php

**Dependencies:**
- [ ] Composer paketleri
- [ ] NPM paketleri
- [ ] .env değişkenleri

**Notes:**
[Özel notlar buraya]
```

---

## 🚀 Sonraki Adım

**Hemen başlanacak (Phase 1):**
1. Spatie Permission paketi kurulumu
2. Role ve Permission Filament Resource'ları
3. User Resource güncelleme
4. Seeders oluşturma

**Kod örneği hazır mı?** Söyle hemen başlayalım! 🎯
