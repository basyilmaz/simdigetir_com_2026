# Kapsamlı E2E Browser Test Senaryoları (Frontend & Backend)
**Tarih:** 15 Mart 2026
**Amaç:** Canlı ve Staging ortamlarda uçtan uca çalışması gereken tüm kritik iş akışlarının manuel veya otomasyon (Dusk/Cypress) browser testleri aracılığıyla doğrulanması.

---

## 🟢 BÖLÜM 1: FRONTEND (MÜŞTERİ DENEYİMİ) TEst Senaryoları

### Senaryo F1: Misafir Ziyaretçinin Fiyat Alması (Guest Quoting)
**Başlangıç Noktası:** `https://simdigetir.com/` (Çıkış yapılmış durum)
1. **Adım:** Fiyat Hesaplama (Hero) widget'ında Alım Adresi olarak 'Levent, Beşiktaş', Teslimat Adresi olarak 'Maslak, Sarıyer' gir.
2. **Adım:** Araç tipi olarak 'Motorlu Kurye' seç.
3. **Adım:** 'Fiyat Hesapla' butonuna tıkla.
4. **Beklenen Sonuç:** Yükleme animasyonu en fazla 2 sn sürmeli ve tahmini fiyat + ETA ekranda belirlemeli. Sayfa donmamalı veya başka bir bileşenle engellenmemeli.

### Senaryo F2: Misafir Ziyaretçinin Hesaba Dönüşüp Sipariş Vermesi (Guest to Auth to Order)
**Bağımlılık:** Senaryo F1 başarılı olmalı.
1. **Adım:** Fiyat aldıktan sonra çıkan 'Siparişe Devam Et' (veya checkout) butonuna tıkla.
2. **Adım:** Sistem kayıt sayfasına (`/hesabim/kayit` veya modal) yönlendirmeli.
3. **Adım:** Yeni telefon (örn: `5551112233`), isim ve şifre girerek kayıt işlemini tamamla.
4. **Adım:** Başarılı kayıttan sonra sistem doğrudan sipariş tamamlama (Checkout) adımına geçmeli.
5. **Adım:** Gönderici/Alıcı detaylarını doldur ve 'Teslimatta Öde (Nakit)' seçeneğini seç.
6. **Beklenen Sonuç:** Sipariş başarıyla oluşturulmalı, ekranda başarı mesajı ve Sipariş No (örn: ORD...) görünmeli. Sistem `/siparis-takip` veya `/hesabim/siparisler/ORD...` ekranına yönlendirmeli.

### Senaryo F3: Kayıtlı Müşteri Girişi ve Dashbord Kontrolü
**Başlangıç Noktası:** `https://simdigetir.com/hesabim/giris`
1. **Adım:** F2'de oluşturulan telefon numarası ve şifre ile giriş yap.
2. **Adım:** Kullanıcı paneline (`/hesabim`) yönlendirilmeyi doğrula.
3. **Adım:** 'Aktif Siparişler' listesinde F2'de verilen siparişi gör.
4. **Adım:** Siparişin durumunun (Örn: 'Ödeme Bekleniyor' veya 'Hazırlanıyor') doğruluğunu teyit et.
5. **Beklenen Sonuç:** Sistem hata vermeden sipariş listesini müşteri rolüne göre filtreleyerek göstermelidir. Başka bir müşterinin siparişi görünmemelidir.

### Senaryo F4: Geçersiz Rota (404) ve IDOR Kontrolü
1. **Adım:** Çıkış yap (Logout).
2. **Adım:** Tarayıcı adres çubuğuna doğrudan `https://simdigetir.com/hesabim` veya `https://simdigetir.com/panel/customer/1` yaz.
3. **Beklenen Sonuç:** Kullanıcı veri görmemeli, doğrudan `/hesabim/giris` sayfasına yönlendirilmelidir (Authentication Middleware doğrulaması).

---

## 🔵 BÖLÜM 2: BACKEND (ADMİN YÖNETİMİ) TEst Senaryoları

### Senaryo B1: Admin Girişi ve Order İlişki Kontrolü
**Başlangıç Noktası:** `https://simdigetir.com/admin`
1. **Adım:** Admin yetkisindeki bir hesapla giriş yap.
2. **Adım:** Sol menüden 'Siparişler' modülüne geç.
3. **Adım:** Senaryo F2'de oluşturduğumuz yeni siparişi listede bul.
4. **Adım:** Listedeki 'Müşteri' sütununun boş olmadığını ve 'Test Kullanıcısı' (F2'deki isim) yazdığını doğrula.
5. **Adım:** Siparişe ait 'Görüntüle' veya 'Düzenle' butonuna tıkla.
6. **Beklenen Sonuç:** Sipariş detayı sayfası (View/Edit) hata vermeden açılmalı. İçeride Müşteri Profili (Telefon, İsim) başarılı şekilde Database'den (user rel) çekilip gösterilmelidir.

### Senaryo B2: Operasyon (Durum Değiştirme) ve Bildirim Akışı
**Bağımlılık:** Senaryo B1 detay sayfası açık olmalı.
1. **Adım:** Siparişin durumunu (State) 'Pending Payment' (Ödeme Bekliyor) durumundan 'Assigned' (Kurye Atandı) durumuna manuel eylem (action) butonu ile geçir.
2. **Adım:** Durumu sırasıyla 'Picked Up' (Alındı) ve 'Delivered' (Teslim Edildi) olarak ilerlet.
3. **Adım:** Her durum değişikliğinde hata almadığını (Toast mesajı geldiğini) teyit et.
4. **Beklenen Sonuç:** Sipariş durumu hatasız geçiş yapmalı ve (eğer aktifse) Audit Log (Bildirim) sekmesinde bu değişiklik kaydedilmelidir.

### Senaryo B3: Finans (Tutar Format Teyidi)
1. **Adım:** Admin panelinde 'Siparişler' veya 'Ödemeler' listesine gir.
2. **Adım:** F2'de verilen siparişin tutarına (Örn: Hesaplamada 150 TL çıkmıştı) bak.
3. **Beklenen Sonuç:** Tutar listesinde `150,00 ₺` yazmalıdır. `15,000 ₺` veya saçma yüksek değerler **çıkmamalıdır** (Cast / Money formatting testi).

### Senaryo B4: CRM ve Talepler Listesi (Leads)
1. **Adım:** Frontend üzerinden iletişim formunu (veya Kurye Başvuru formunu) doldur. (Örn: Firma adı: XYZ Corp).
2. **Adım:** Admin panelinde 'Talepler' modülüne geç.
3. **Adım:** Listedeki 'Firma' sütununun formdan gelen veriyi yansıttığını (Boş olmadığını) doğrula.
4. **Adım:** Talep detay (View) sayfasına gir.
5. **Beklenen Sonuç:** Detay sayfasında "Aktivite Akışı" tablosunun kırık/bozuk olmadığını ve temiz listelendiğini doğrula.

---

## 🟡 BÖLÜM 3: GÜVENLİK VE PERFORMANS YÜK TESTLERİ

### Senaryo S1: Kötü Niyetli Veri Girişi (XSS/SQLi)
1. **Adım:** Fiyat hesapla ekranında Adres bölümüne veya İletişim Formundaki İsim bölümüne `<script>alert(1)</script>` yaz.
2. **Beklenen Sonuç:** Sistem bu stringi sanitize (temizlemeli) etmeli, admin panelinde isim alanında script çalışmamalı, sadece text olarak görünmelidir.

### Senaryo S2: Checkout Öncesi Fiyat Manipülasyonu
1. **Adım:** F1 sipariş adımlarında Developer Tools'u açıp post edilen payload'daki fiyat değerini (Amount) manuel olarak 1 TL yap.
2. **Beklenen Sonuç:** Sunucu (Backend Validation) bu müdahaleyi reddetmeli ve "Fiyat uyuşmazlığı" (Quote mismatch) veya "Geçersiz İstek" döndürmelidir. Admin panelinde 1 TL'lik sahte sipariş oluşmamalıdır.
