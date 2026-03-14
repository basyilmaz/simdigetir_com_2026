# SimdiGetir.com - cPanel Kurulum Rehberi

## 🎯 cPanel Üzerinden Deployment

### Ön Hazırlık

**Gereksinimler:**
- ✅ cPanel erişimi
- ✅ PHP 8.2 veya üzeri
- ✅ MySQL database erişimi
- ✅ Terminal (SSH) erişimi (önerilen)
- ✅ Git (cPanel'de mevcut olmalı)

---

## 📦 YÖNTEM 1: Git Version Control (ÖNERİLEN)

### Adım 1: Git Version Control Kurulumu

1. **cPanel'e Giriş Yap**
   - https://yourdomain.com:2083 (veya hosting sağlayıcınızın cPanel URL'i)

2. **Git Version Control Bul**
   - Arama çubuğunda "Git" yaz
   - "Git Version Control" simgesine tıkla

3. **Repository Oluştur**
   - "Create" butonuna tıkla
   - **Clone URL:** `https://github.com/basyilmaz/simdigetir_com_2026.git`
   - **Repository Path:** `/home/kullaniciadi/repositories/simdigetir_com_2026`
   - **Repository Name:** `simdigetir_com_2026`
   - "Create" butonuna tıkla

4. **Repository'yi Web Root'a Deploy Et**
   - Oluşturulan repository'ye tıkla
   - "Manage" butonuna tıkla
   - **Deployment Path:** `/home/kullaniciadi/public_html/simdigetir`
     (veya subdomain için: `/home/kullaniciadi/public_html/`)
   - "Deploy HEAD Commit" butonuna tıkla

### Adım 2: MySQL Database Oluştur

1. **cPanel → MySQL Databases**

2. **Yeni Database Oluştur**
   - Database Name: `getir_simdi`
   - "Create Database" tıkla
   - ✅ **Full Database Name:** `kullaniciadi_getir_simdi` (cPanel otomatik prefix ekler)

3. **Yeni User Oluştur**
   - Username: `getir_simdi_user`
   - Password: `your-secure-db-password` (veya "Generate Password" ile güçlü şifre)
   - "Create User" tıkla
   - ✅ **Full Username:** `kullaniciadi_getir_simdi_user`

4. **User'ı Database'e Ekle**
   - "Add User to Database" bölümünde:
     - User: `kullaniciadi_getir_simdi_user`
     - Database: `kullaniciadi_getir_simdi`
   - "Add" tıkla
   - **ALL PRIVILEGES** seç
   - "Make Changes" tıkla

### Adım 3: .env Dosyasını Oluştur

**cPanel → File Manager:**

1. **Deployment klasörüne git**
   - `/home/kullaniciadi/public_html/simdigetir`

2. **`.env` dosyası oluştur**
   - Sağ tıkla → "New File"
   - Dosya adı: `.env`
   - Dosyaya sağ tıkla → "Edit"

3. **Aşağıdaki içeriği yapıştır:**

```env
APP_NAME=SimdiGetir
APP_ENV=production
APP_KEY=base64:your-generated-app-key
APP_DEBUG=false
APP_TIMEZONE=Europe/Istanbul
APP_URL=https://simdigetir.com

APP_LOCALE=tr
APP_FALLBACK_LOCALE=tr
APP_FAKER_LOCALE=tr_TR

APP_MAINTENANCE_DRIVER=file

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# ÖNEMLİ: cPanel'in verdiği tam database bilgilerini kullan
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=kullaniciadi_getir_simdi
DB_USERNAME=kullaniciadi_getir_simdi_user
DB_PASSWORD=your-secure-db-password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=mail.simdigetir.com
MAIL_PORT=587
MAIL_USERNAME=info@simdigetir.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@simdigetir.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
```

**⚠️ ÖNEMLİ:**
- `kullaniciadi_` prefix'ini kendi cPanel kullanıcı adınızla değiştirin
- cPanel'de MySQL → "Current Databases" bölümünden tam isimleri görebilirsiniz

### Adım 4: Terminal (SSH) ile Kurulum Komutları

**cPanel → Terminal** (veya SSH):

```bash
# 1. Deployment klasörüne git
cd ~/public_html/simdigetir

# 2. Composer bağımlılıklarını kur
composer install --optimize-autoloader --no-dev

# 3. NPM bağımlılıkları ve build
npm ci --production
npm run build

# 4. Dizin izinleri
chmod -R 775 storage bootstrap/cache

# 5. Migration ve Seeding
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force

# 6. Cache optimizasyonları
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "✅ Kurulum tamamlandı!"
```

### Adım 5: Domain/Subdomain Yapılandırması

**Seçenek A: Ana Domain (simdigetir.com)**

1. **cPanel → Domains**
2. Ana domaini seç
3. **Document Root:** `/home/kullaniciadi/public_html/simdigetir/public`
4. Save

**Seçenek B: Subdomain (test.simdigetir.com)**

1. **cPanel → Subdomains**
2. "Create a Subdomain"
3. **Subdomain:** `test`
4. **Document Root:** `/home/kullaniciadi/public_html/simdigetir/public`
5. Create

### Adım 6: SSL Sertifikası

**cPanel → SSL/TLS Status**

1. Domain'i seç (simdigetir.com)
2. "Run AutoSSL" tıkla
3. Let's Encrypt otomatik kurulacak

---

## 📦 YÖNTEM 2: File Manager ile Manuel Upload

### Eğer Git yoksa veya tercih etmiyorsanız:

1. **Lokal bilgisayarda ZIP oluştur**
   ```bash
   # Proje klasöründe
   git archive --format=zip --output=simdigetir.zip master
   ```

2. **cPanel → File Manager**
   - `/home/kullaniciadi/public_html/` klasörüne git
   - "Upload" butonuna tıkla
   - `simdigetir.zip` yükle

3. **ZIP'i Extract Et**
   - ZIP dosyasına sağ tıkla
   - "Extract" tıkla
   - Klasör adı: `simdigetir`

4. **Yukarıdaki Adım 2-6'yı takip et**

---

## 🔧 Önemli cPanel Ayarları

### PHP Versiyonu

**cPanel → Select PHP Version (veya MultiPHP Manager)**

1. **PHP 8.2** seç (minimum 8.2)
2. **PHP Extensions** aktif olmalı:
   - ✅ mysqli
   - ✅ pdo_mysql
   - ✅ mbstring
   - ✅ xml
   - ✅ openssl
   - ✅ curl
   - ✅ zip
   - ✅ gd
   - ✅ json
   - ✅ tokenizer

### .htaccess Kontrolü

**public/.htaccess** dosyası zaten mevcut ve doğru yapılandırılmış. Kontrol için:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### Symlink (Storage)

Terminal'de:
```bash
cd ~/public_html/simdigetir
php artisan storage:link
```

---

## 🔍 Kurulum Sonrası Kontroller

### 1. Database Test

**Terminal:**
```bash
cd ~/public_html/simdigetir
php artisan db:show
```

**Beklenen çıktı:**
```
MySQL  8.0.x
Database: kullaniciadi_getir_simdi
```

### 2. Admin Kullanıcısı Test

**Terminal:**
```bash
php artisan tinker
>>> \App\Models\User::count()
=> 1
>>> \App\Models\User::first()->email
=> "admin@simdigetir.com"
>>> exit
```

### 3. Web Test

**Tarayıcıda test et:**
- ✅ https://simdigetir.com/ → Ana sayfa
- ✅ https://simdigetir.com/admin → Admin paneli
  - Email: `admin@simdigetir.com`
  - Şifre: `<admin-password-from-env>`
- ✅ https://simdigetir.com/kurye → İlçeler sayfası
- ✅ https://simdigetir.com/sitemap.xml → Sitemap

### 4. Permissions Test

**Terminal:**
```bash
cd ~/public_html/simdigetir
ls -la storage/
ls -la bootstrap/cache/
```

Klasörler `775` veya `777` olmalı.

---

## 🔄 Güncelleme (Git Pull)

**Her güncellemede:**

```bash
# 1. Git repository'ye git
cd ~/repositories/simdigetir_com_2026

# 2. Pull latest changes
git pull origin master

# 3. cPanel'de "Deploy HEAD Commit" tıkla

# 4. Terminal'de deployment klasörüne git
cd ~/public_html/simdigetir

# 5. Update komutları
composer install --optimize-autoloader --no-dev
npm ci --production
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "✅ Güncelleme tamamlandı!"
```

---

## ⚠️ Sorun Giderme

### "500 Internal Server Error"

**Çözüm 1: İzinleri düzelt**
```bash
cd ~/public_html/simdigetir
chmod -R 775 storage bootstrap/cache
```

**Çözüm 2: Log'u kontrol et**
```bash
tail -50 storage/logs/laravel.log
```

**Çözüm 3: .htaccess**
`public/.htaccess` dosyasının mevcut olduğundan emin olun.

### "Database connection failed"

**Kontrol et:**
```bash
# .env dosyasında database bilgileri doğru mu?
cat .env | grep DB_

# cPanel → MySQL Databases → Current Databases
# Tam database ve user ismini buradan al
```

**Test et:**
```bash
php artisan db:show
```

### "Composer not found"

cPanel'de composer genellikle şu yolda:
```bash
/usr/local/bin/composer
# veya
php ~/composer.phar
```

**Alias oluştur:**
```bash
alias composer='php ~/composer.phar'
```

### "npm: command not found"

cPanel'de Node.js Setup:
1. **cPanel → Setup Node.js App**
2. Node.js version: 18.x veya üzeri
3. Application root: `/home/kullaniciadi/public_html/simdigetir`
4. Run NPM Install: Yes

### "Storage permissions"

```bash
cd ~/public_html/simdigetir
find storage -type f -exec chmod 664 {} \;
find storage -type d -exec chmod 775 {} \;
find bootstrap/cache -type f -exec chmod 664 {} \;
find bootstrap/cache -type d -exec chmod 775 {} \;
```

### "Cache issues"

**Tüm cache'i temizle:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
rm -rf bootstrap/cache/*.php
```

---

## 📊 cPanel Özellikleri

### Cron Jobs (Scheduler)

**cPanel → Cron Jobs**

Laravel scheduler için:
```
* * * * * cd /home/kullaniciadi/public_html/simdigetir && php artisan schedule:run >> /dev/null 2>&1
```

### Backup

**cPanel → Backup**

Tam backup al:
- Home Directory Backup
- MySQL Database Backup

**Veya Terminal:**
```bash
# Database backup
mysqldump -u kullaniciadi_getir_simdi_user -p kullaniciadi_getir_simdi > backup_$(date +%Y%m%d).sql

# Files backup
tar -czf backup_$(date +%Y%m%d).tar.gz ~/public_html/simdigetir
```

### Error Log

**cPanel → Errors**

Son hataları gösterir. Terminal ile:
```bash
tail -100 ~/public_html/simdigetir/storage/logs/laravel.log
```

---

## ✅ cPanel Deployment Checklist

### İlk Kurulum
- [ ] Git Version Control ile repository clone edildi
- [ ] MySQL database oluşturuldu (prefix ile)
- [ ] MySQL user oluşturuldu ve database'e eklendi
- [ ] .env dosyası oluşturuldu ve doğru bilgiler girildi
- [ ] PHP versiyonu 8.2+ ve extensions aktif
- [ ] Composer install çalıştırıldı
- [ ] NPM build tamamlandı
- [ ] Storage/cache izinleri ayarlandı
- [ ] Migration çalıştırıldı
- [ ] Admin user seeded
- [ ] Cache'ler oluşturuldu
- [ ] Domain document root ayarlandı (`/public`)
- [ ] SSL sertifikası kuruldu
- [ ] Admin paneline giriş test edildi
- [ ] Landing pages test edildi

### Her Güncelleme
- [ ] Git pull yapıldı
- [ ] cPanel'de "Deploy HEAD Commit" tıklandı
- [ ] Composer install çalıştırıldı
- [ ] NPM build yapıldı (gerekirse)
- [ ] Migration çalıştırıldı (varsa)
- [ ] Cache temizlendi ve yenilendi
- [ ] Test edildi

---

## 📞 Yardım & Destek

**cPanel Dokümantasyon:**
- https://docs.cpanel.net/

**Laravel cPanel Deployment:**
- https://laravel.com/docs/11.x/deployment

**Proje Repository:**
- https://github.com/basyilmaz/simdigetir_com_2026

---

**Not:** cPanel kullanıcı arayüzü hosting sağlayıcıya göre farklılık gösterebilir. Yukarıdaki adımlar standart cPanel için geçerlidir.

---

**Son Güncelleme:** 2026-02-15
**Versiyon:** 1.0.0 - cPanel Production Ready



