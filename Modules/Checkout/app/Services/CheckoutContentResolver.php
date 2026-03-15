<?php

namespace Modules\Checkout\Services;

use Modules\Settings\Models\Setting;

class CheckoutContentResolver
{
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
