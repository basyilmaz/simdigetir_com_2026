# Reklam Platformu Calisma Bilgilendirmesi (2026-03-11)

## 1) Amac

Bu dokuman, admin paneldeki Reklam Platformu bolumunun nasil calistigini, hangi modullerin ne yaptigini ve gunluk operasyon akisini aciklar.

## 2) Modul Yapisi

- `AdsCore`: Admin resource katmani (Kampanyalar, Baglantilar, Donusumler), temel modeller, job ve servisler.
- `AdsGoogle`: Google tarafi entegrasyon akislari.
- `AdsMeta`: Meta tarafi entegrasyon akislari.
- `Attribution`: Lead kaynagi ve iliskili atiflama alanlari.
- `Reporting`: Reklam performansinin raporlanmasi.

Canli durumda bu modullerin tumu `enabled` olarak calismaktadir.

## 3) Admin Panel Kullanimi (Standart Akis)

Panelde bilgi sayfasi:

- `/admin/ads-platform-guide` (Reklam Platformu > Nasil Calisir)

1. `Baglantilar` ekraninda platform baglantisi olustur.
   - Zorunlu alanlar: `platform`, `name`, `status`.
2. `Kampanyalar` ekraninda yeni kampanya ac.
   - Zorunlu alanlar: `ad_connection_id`, `platform`, `name`, `status`.
3. `Donusumler` ekranindan gelen event durumlarini takip et.
   - Beklenen durumlar: `pending`, `sent`, `confirmed`, `failed`.
4. Gerekli ise kampanya duzenleme/duraklatma aksiyonlarini kampanya kaydi uzerinden yap.

## 4) Rol ve Yetki Modeli (Ads Yetkileri)

- `super-admin`: tum ads yetkileri (`ads.view`, `ads.manage`, `ads.publish`, `ads.report`)
- `admin`: tum ads yetkileri
- `operations`: `ads.view`
- `support`: `ads.view`
- `finance`: `ads.report`

Not: `super-admin` politikada bypass yetkisine sahip olsa da, canli ortamda rol-permission kayitlari seed edilmis durumdadir.

## 5) Operasyonel Kontrol Listesi

Gunluk/hata aninda asagidaki kontroller uygulanir:

1. Admin erisimi:
   - `/admin/ad-campaigns`
   - `/admin/ad-connections`
   - `/admin/ad-conversions`
2. Health komutu:
   - `php artisan ads:health-check --hours=48`
3. Uygulama logu:
   - `storage/logs/laravel.log` icinde yeni `production.ERROR` var mi kontrol et.
4. Canli cache yenileme (gerekirse):
   - `php artisan optimize:clear`
   - `php artisan optimize`

## 6) Canli Dogrulama Ozeti (Bu Turda Yapilan)

- Admin panel Ads sayfalari: `200 OK` (desktop + mobile)
- Create akisi (baglanti + kampanya): basarili
- `livewire/update` 500: gozlenmedi
- `ads:health-check`: `ads_health=ok`
- Son loglarda Ads ile ilgili yeni `production.ERROR`: gozlenmedi

## 7) Ekip Icine Hazir Bilgilendirme Metni

Asagidaki metin ekip kanalinda dogrudan paylasilabilir:

> Reklam Platformu admin modulleri canlida dogrulandi. Kampanyalar, Baglantilar ve Donusumler ekranlari desktop ve mobilde 200 OK calisiyor. Admin rolunde create akislari (baglanti + kampanya) basarili. Livewire 500 hatasi gozlenmedi. Health-check sonucu ads_health=ok. Operasyon takibi icin standart kontrol listesi dokumanda paylasilmistir.
