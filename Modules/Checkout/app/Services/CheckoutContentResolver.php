<?php

namespace Modules\Checkout\Services;

use Modules\Settings\Models\Setting;

class CheckoutContentResolver
{
    /**
     * @return array{phone_display:string,phone_href:string,whatsapp_href:string,email:string,email_href:string,home_href:string,contact_href:string,privacy_href:string,terms_href:string,support_note:string}
     */
    public function supportChannels(): array
    {
        $phoneDisplay = trim((string) Setting::getValue('contact.phone', '+90 551 356 72 92'));
        $normalizedPhone = preg_replace('/[^0-9+]/', '', $phoneDisplay) ?? '';
        $whatsAppPhone = preg_replace('/[^0-9]/', '', (string) Setting::getValue('contact.whatsapp', '905513567292')) ?? '';
        $email = trim((string) Setting::getValue('contact.email', 'webgetir@simdigetir.com'));

        return [
            'phone_display' => $phoneDisplay !== '' ? $phoneDisplay : '+90 551 356 72 92',
            'phone_href' => $normalizedPhone !== '' ? 'tel:'.$normalizedPhone : 'tel:+905513567292',
            'whatsapp_href' => 'https://wa.me/'.($whatsAppPhone !== '' ? $whatsAppPhone : '905513567292'),
            'email' => $email !== '' ? $email : 'webgetir@simdigetir.com',
            'email_href' => 'mailto:'.($email !== '' ? $email : 'webgetir@simdigetir.com'),
            'home_href' => route('home'),
            'contact_href' => route('contact'),
            'privacy_href' => url('/kvkk'),
            'terms_href' => url('/kullanim-kosullari'),
            'support_note' => (string) Setting::getValue(
                'checkout.support_note',
                'Sorunuz varsa destek ekibimiz telefon, WhatsApp veya e-posta uzerinden yardimci olur.'
            ),
        ];
    }

    /**
     * @return array{intro:string,help:string}
     */
    public function entryCopy(): array
    {
        return [
            'intro' => (string) Setting::getValue(
                'checkout.entry_intro',
                'Adres bilgilerinizi girerek teklif alin, uygun oldugunuzda guvenli checkout akisina devam edin.'
            ),
            'help' => (string) Setting::getValue(
                'checkout.entry_help',
                'Daha once hesap olusturduysaniz mevcut siparislerinizi ve takip linklerinizi Hesabim ekranindan gorebilirsiniz.'
            ),
        ];
    }

    /**
     * @return array{intro:string,help:string}
     */
    public function loginCopy(): array
    {
        return [
            'intro' => (string) Setting::getValue(
                'checkout.login_intro',
                'Kayitli telefon numaraniz ve sifrenizle siparislerinizi, odeme durumunu ve takip linklerini goruntuleyin.'
            ),
            'help' => (string) Setting::getValue(
                'checkout.login_help',
                'Sifrenizi hatirlamiyorsaniz destek ekibimiz telefon numaranizi dogrulayarak size yardimci olur.'
            ),
        ];
    }

    /**
     * @return array{intro:string,help:string}
     */
    public function registerCopy(): array
    {
        return [
            'intro' => (string) Setting::getValue(
                'checkout.register_intro',
                'Hizli siparis, takip linkleri ve siparis gecmisi icin bir hesap olusturun.'
            ),
            'help' => (string) Setting::getValue(
                'checkout.register_help',
                'Kayit olduktan sonra ayni hesapla checkout ve Hesabim ekranlarini kullanabilirsiniz.'
            ),
        ];
    }

    /**
     * @return array{intro:string,help:string,error_help:string}
     */
    public function trackingCopy(): array
    {
        return [
            'intro' => (string) Setting::getValue(
                'checkout.tracking_intro',
                'Siparis numaraniz ve sipariste kullandiginiz telefon ile guncel durum bilgilerini goruntuleyin.'
            ),
            'help' => (string) Setting::getValue(
                'checkout.tracking_help',
                'Siparis numarasini SMS, e-posta veya musteri panelinizdeki siparis kartindan bulabilirsiniz.'
            ),
            'error_help' => (string) Setting::getValue(
                'checkout.tracking_error_help',
                'Bilgiler eslesmiyorsa destek hattimizla iletisime gecin; ekip siparis kaydini kontrol etsin.'
            ),
        ];
    }

    /**
     * @return array{title:string,body:string,bank_name:string,account_holder:string,iban:string,reference_note:string}
     */
    public function bankTransferInstructions(): array
    {
        return [
            'title' => (string) Setting::getValue('checkout.bank_transfer_title', 'Havale / EFT Odeme Talimati'),
            'body' => (string) Setting::getValue(
                'checkout.bank_transfer_body',
                'Havale odemenizi tamamladiktan sonra siparisiniz finans ekibinin kontrolune duser. Onaylandiginda operasyon akisina otomatik gecer.'
            ),
            'bank_name' => (string) Setting::getValue('checkout.bank_transfer_bank_name', ''),
            'account_holder' => (string) Setting::getValue('checkout.bank_transfer_account_holder', ''),
            'iban' => (string) Setting::getValue('checkout.bank_transfer_iban', ''),
            'reference_note' => (string) Setting::getValue(
                'checkout.bank_transfer_reference_note',
                'Aciklama alanina siparis numaranizi yazin. Bu fazda dekont yukleme yok; admin reconcile ile onaylanir.'
            ),
        ];
    }
}
