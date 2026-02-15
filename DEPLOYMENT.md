# SimdiGetir.com - Deployment Guide

## 📋 Sunucuda İlk Kurulum

### 1. Repository'yi Clone Et

```bash
# SSH ile sunucuya bağlan
ssh kullaniciadi@sunucu-ip

# Web dizinine git
cd /var/www/

# Repository'yi clone et
git clone https://github.com/basyilmaz/simdigetir_com_2026.git simdigetir_com_2026
cd simdigetir_com_2026
```

### 2. .env Dosyasını Oluştur

```bash
# .env.example'dan kopyala
cp .env.example .env

# .env dosyasını düzenle
nano .env
```

**.env İçeriği:**
```env
APP_NAME=SimdiGetir
APP_ENV=production
APP_KEY=base64:Jnmm7Gu+BOZDzTAYiPNkyxE5KOwb7jcqYLT7PpyatY8=
APP_DEBUG=false
APP_TIMEZONE=Europe/Istanbul
APP_URL=https://simdigetir.com

APP_LOCALE=tr
APP_FALLBACK_LOCALE=tr
APP_FAKER_LOCALE=tr_TR

# Database - MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=getir_simdi
DB_USERNAME=getir_simdi_user
DB_PASSWORD=Yilmaz2154!-!-

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail - SMTP (sonradan yapılandırılacak)
MAIL_MAILER=log
MAIL_HOST=mail.simdigetir.com
MAIL_PORT=587
MAIL_USERNAME=info@simdigetir.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@simdigetir.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Composer Bağımlılıklarını Kur

```bash
# Production mode (dev paketleri hariç)
composer install --optimize-autoloader --no-dev

# VEYA tüm paketlerle (dev için)
composer install --optimize-autoloader
```

### 4. NPM Bağımlılıkları ve Build

```bash
# NPM paketlerini kur
npm ci --production

# Asset'leri build et
npm run build
```

### 5. MySQL Database Oluştur

**cPanel / phpMyAdmin:**
```sql
CREATE DATABASE getir_simdi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'getir_simdi_user'@'localhost' IDENTIFIED BY 'Yilmaz2154!-!-';
GRANT ALL PRIVILEGES ON getir_simdi.* TO 'getir_simdi_user'@'localhost';
FLUSH PRIVILEGES;
```

**Veya SSH:**
```bash
mysql -u root -p
# Yukarıdaki SQL komutlarını çalıştır
```

### 6. Laravel Kurulum Komutları

```bash
# Dizin izinleri
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# VEYA shared hosting için
chmod -R 775 storage bootstrap/cache

# Cache temizle
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Migration'ları çalıştır
php artisan migrate --force

# Admin kullanıcısı oluştur
php artisan db:seed --class=AdminUserSeeder --force

# Cache oluştur (production için)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize
php artisan optimize
```

### 7. Web Sunucu Yapılandırması

**Nginx:**
```nginx
server {
    listen 80;
    server_name simdigetir.com www.simdigetir.com;
    root /var/www/simdigetir_com_2026/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Apache (.htaccess zaten mevcut):**
- Document Root: `/var/www/simdigetir_com_2026/public`
- `AllowOverride All` ayarlandığından emin olun
- `mod_rewrite` modülü aktif olmalı

### 8. SSL Sertifikası (Let's Encrypt)

```bash
# Certbot kur
sudo apt install certbot python3-certbot-nginx

# SSL sertifikası al
sudo certbot --nginx -d simdigetir.com -d www.simdigetir.com

# Auto-renewal test
sudo certbot renew --dry-run
```

---

## 🔄 Güncelleme (Git Pull)

Yeni değişiklikleri sunucuya almak için:

```bash
# Sunucuya bağlan
ssh kullaniciadi@sunucu-ip

# Proje dizinine git
cd /var/www/simdigetir_com_2026

# Git pull
git pull origin master

# Composer bağımlılıklarını güncelle
composer install --optimize-autoloader --no-dev

# NPM build (eğer frontend değişikliği varsa)
npm ci --production
npm run build

# Cache'leri temizle ve yeniden oluştur
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migration'ları çalıştır (eğer yeni migration varsa)
php artisan migrate --force

# Optimize
php artisan optimize
```

**Tek komutla güncelleme:**
```bash
git pull && \
composer install --optimize-autoloader --no-dev && \
npm run build && \
php artisan migrate --force && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
php artisan optimize && \
echo "✓ Deployment tamamlandı!"
```

---

## ✅ Doğrulama Testleri

### 1. Database Bağlantısı
```bash
php artisan db:show
```

### 2. Admin Kullanıcısı
```bash
php artisan tinker
>>> \App\Models\User::count()
>>> \App\Models\User::first()->email
```

### 3. Web Sitesi Test
- **Ana Sayfa:** https://simdigetir.com/
- **Admin Panel:** https://simdigetir.com/admin
  - Email: admin@simdigetir.com
  - Şifre: Yilmaz2154!-!
- **Sitemap:** https://simdigetir.com/sitemap.xml
- **Robots:** https://simdigetir.com/robots.txt

### 4. Performans Test
```bash
# Response time kontrolü
curl -o /dev/null -s -w "Time: %{time_total}s\n" https://simdigetir.com/
```

---

## 🔧 Sorun Giderme

### Hata: "500 Internal Server Error"
```bash
# Log dosyasını kontrol et
tail -f storage/logs/laravel.log

# İzinleri düzelt
chmod -R 775 storage bootstrap/cache
```

### Hata: "Database connection failed"
```bash
# .env dosyasını kontrol et
cat .env | grep DB_

# MySQL bağlantısını test et
php artisan db:show
```

### Hata: "CSS/JS yüklenmiyor"
```bash
# Build'i yenile
npm run build

# Public/build klasörünü kontrol et
ls -lh public/build/
```

### Cache Sorunları
```bash
# Tüm cache'i temizle
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Yeniden oluştur
php artisan optimize
```

---

## 📊 Monitoring & Bakım

### Log Takibi
```bash
# Real-time log takibi
tail -f storage/logs/laravel.log

# Son 100 satır
tail -100 storage/logs/laravel.log
```

### Database Backup (Günlük)
```bash
# Crontab ekle
crontab -e

# Her gece saat 02:00'de backup
0 2 * * * mysqldump -u getir_simdi_user -pYilmaz2154!-!- getir_simdi > /backup/simdigetir_$(date +\%Y\%m\%d).sql
```

### Disk Kullanımı
```bash
# Log dosyası boyutu
du -sh storage/logs/

# Eski logları temizle (30 günden eski)
find storage/logs/ -name "*.log" -mtime +30 -delete
```

---

## 🚀 Production Checklist

### İlk Deployment
- [ ] Repository clone edildi
- [ ] .env dosyası oluşturuldu ve yapılandırıldı
- [ ] Composer bağımlılıkları kuruldu (--no-dev)
- [ ] NPM build tamamlandı
- [ ] MySQL database oluşturuldu
- [ ] Migration'lar çalıştırıldı
- [ ] Admin kullanıcısı oluşturuldu
- [ ] Dizin izinleri ayarlandı
- [ ] Cache'ler oluşturuldu
- [ ] Web sunucu yapılandırıldı
- [ ] SSL sertifikası kuruldu
- [ ] Domain DNS ayarları yapıldı

### Her Güncelleme
- [ ] Git pull yapıldı
- [ ] Composer install çalıştırıldı
- [ ] NPM build tamamlandı (gerekirse)
- [ ] Migration'lar çalıştırıldı (varsa)
- [ ] Cache'ler temizlendi ve yenilendi
- [ ] Test edildi (admin panel + landing pages)

---

## 📞 İletişim & Destek

**Proje:** SimdiGetir Kurye Hizmeti
**Repository:** https://github.com/basyilmaz/simdigetir_com_2026
**Domain:** https://simdigetir.com

---

**Son Güncelleme:** 2026-02-15
**Versiyon:** 1.0.0 - Production Ready
