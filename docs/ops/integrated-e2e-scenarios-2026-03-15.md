# Uçtan Uca (E2E) Adım Adım Entegre Test Senaryoları
**Tarih:** 15 Mart 2026
**Uygulama Alanı:** Frontend (Müşteri) -> Backend (Admin) Eşzamanlı Doğrulama

Bu doküman, bir işlemin frontend'de yapıldıktan hemen sonra backend'de (admin) nasıl yansıdığının adım adım test edilmesini sağlar.

---

## Senaryo 1: Yeni Sipariş Oluşturma ve Admin Doğrulaması (Guest to Order)

| Adım | İşlem Yapan | Platform | Eylem / Aksiyon | Beklenen Sonuç / Doğrulama |
| :--- | :--- | :--- | :--- | :--- |
| 1.1 | Müşteri | Frontend | Ana sayfada "Levent" -> "Maslak", "Motorlu Kurye" seç ve "Fiyat Hesapla"ya tıkla. | Fiyat hesaplama hata vermeden ve overlay'de takılmadan fiyatı getirmelidir. |
| 1.2 | Müşteri | Frontend | Çıkan teklifte "Siparişe Devam Et" butonuna tıkla. Checkout öncesi telefon (5551234567) + şifre ile yeni hesap oluştur. Nakit ödeme seç. | Sipariş başarılı ekranı ve sipariş numarası (örn: ORD101) görülmelidir. |
| **1.3** | **Admin** | **Backend** | Panelde **Siparişler** listesine gir. | **`ORD101` numaralı sipariş listelenmelidir.** |
| **1.4** | **Admin** | **Backend** | Liste kolonlarını kontrol et. | **"Müşteri" sütununda "Smoke Pickup" yerine adım 1.2'de girilen müşteri adı bulunmalıdır.** |
| **1.5** | **Admin** | **Backend** | Listedeki "Tutar" kolonunu kontrol et. | **Fiyat `150,00 ₺` şeklinde doğru formatlanmış olmalı, `15,000 ₺` görünmemelidir.** |

---

## Senaryo 2: Sipariş Durumu (State) Değişimi ve Müşteri Paneline Yansıması

| Adım | İşlem Yapan | Platform | Eylem / Aksiyon | Beklenen Sonuç / Doğrulama |
| :--- | :--- | :--- | :--- | :--- |
| 2.1 | Admin | Backend | `ORD101` siparişinin sağındaki "Durum Değiştir" eylemine (action) tıkla. | Modal sorunsuz açılmalıdır. |
| 2.2 | Admin | Backend | Durumu "Ödeme Bekliyor"dan "Kurye Atandı" (Assigned) durumuna çek ve kaydet. | Başarı bildirimi (toast) çıkmalı ve sipariş state'i güncellenmelidir. |
| **2.3** | **Müşteri** | **Frontend** | `/hesabim` (Müşteri Portalı) adresine gir, "Aktif Siparişlerim" tablosunu aç. | **Siparişin durumu "Ödeme Bekliyor" yerine "Kurye Atandı" olarak görünmelidir.** |
| 2.4 | Admin | Backend | Durumu "Teslim Edildi" (Delivered) yap ve kaydet. | Başarı bildirimi çıkmalıdır. |
| **2.5** | **Müşteri** | **Frontend** | `/hesabim` (Müşteri Portalı) sayfasını yenile. | **Sipariş "Aktif Siparişler"den kalkıp "Tamamlanan Siparişler" listesine geçmelidir.** |

---

## Senaryo 3: B2B İletişim Formu (Lead) ve Admin Detay Doğrulaması

| Adım | İşlem Yapan | Platform | Eylem / Aksiyon | Beklenen Sonuç / Doğrulama |
| :--- | :--- | :--- | :--- | :--- |
| 3.1 | Ziyaretçi | Frontend | `/iletisim` veya kurumsal başvuru sayfasına git. | Sayfa 404 vermeden açılmalıdır. |
| 3.2 | Ziyaretçi | Frontend | Formda İsim: "Can V.", Firma: "GetirCorp", Telefon: "5559998877" girerek gönder. | "Form başarıyla gönderildi" (201 Created) mesajı alınmalıdır. |
| **3.3** | **Admin** | **Backend** | Panelde **Talepler** listesine gir. | **Yeni talep listede en üstte olmalıdır.** |
| **3.4** | **Admin** | **Backend** | Liste kolonlarını kontrol et. | **"Firma" sütunu boş olmamalı, "GetirCorp" yazmalıdır.** |
| **3.5** | **Admin** | **Backend** | Talebin yanındaki "Görüntüle" (View) butonuna tıkla. | **Detay sayfasında "Aktivite Akışı" (Activity Feed) bileşeni kırık/bozuk olmamalı, formun gönderim bilgisi net okunabilmelidir.** |

---

## Senaryo 4: Veri Bütünlüğü ve Fiyat Sabotajı (Güvenlik Testi)

| Adım | İşlem Yapan | Platform | Eylem / Aksiyon | Beklenen Sonuç / Doğrulama |
| :--- | :--- | :--- | :--- | :--- |
| 4.1 | Hacker | Frontend | Sipariş adımında form post edilirken tarayıcıdan (DevTools) gönderilen payload içindeki fiyat değerini `1.00 TL` olarak değiştir. | Sunucu tarafındaki form validasyonu/quote mismatch bu isteği `422 Unprocessable Content` ile reddetmelidir. |
| **4.2** | **Admin** | **Backend** | Panelde **Siparişler** listesine gir. | **Sistemde fiyatı `1.00 TL` olan sahte/manipüle edilmiş sipariş oluşmamış olmalıdır.** |
| 4.3 | Hacker | Frontend | Sipariş notu veya İletişim formu ismine `<script>alert('XSS')</script>` payload'ı gir ve gönder. | Sunucu kabul etmeli (veya sanitize edip reddetmeli). |
| **4.4** | **Admin** | **Backend** | Müşteri veya Talep listesine gir. | **Adminin tarayıcısında `alert()` fonksiyonu (XSS tetiklemesi) ÇALIŞMAMALI, script düz metin (text) olarak escape edilmiş halde gösterilmelidir.** |
