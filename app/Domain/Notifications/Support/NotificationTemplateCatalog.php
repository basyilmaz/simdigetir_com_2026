<?php

namespace App\Domain\Notifications\Support;

class NotificationTemplateCatalog
{
    /**
     * @return array<string, array{
     *   label:string,
     *   description:string,
     *   channels:array<int,string>,
     *   default_body:string,
     *   variables:array<string,string>
     * }>
     */
    public static function definitions(): array
    {
        return [
            'orders.order_created' => [
                'label' => 'Siparis alindi',
                'description' => 'Siparis olustugu anda musteriyi ilk kez bilgilendirir.',
                'channels' => ['sms'],
                'default_body' => 'Siparisiniz alindi. No: {order_no}. Alinis: {pickup_address}. Teslimat: {dropoff_address}. Takip: {track_url}',
                'variables' => [
                    'order_no' => 'Olusan siparis numarasi.',
                    'pickup_address' => 'Gonderinin alinacagi adres.',
                    'dropoff_address' => 'Teslimat adresi.',
                    'track_url' => 'Public siparis takip linki.',
                ],
            ],
            'orders.payment_pending_bank_transfer' => [
                'label' => 'Havale odemesi bekleniyor',
                'description' => 'Havale/EFT secilen siparislerde odeme talimatini SMS ile iletir.',
                'channels' => ['sms'],
                'default_body' => 'Siparisiniz alindi. No: {order_no}. Havale odemesi bekleniyor. Tutar: {total_amount}. {bank_transfer_instruction} Takip: {track_url}',
                'variables' => [
                    'order_no' => 'Siparis numarasi.',
                    'total_amount' => 'Odeme beklenen toplam tutar.',
                    'bank_transfer_instruction' => 'Admin panelde tanimlanan banka, IBAN ve referans notunun SMS ozeti.',
                    'track_url' => 'Public siparis takip linki.',
                ],
            ],
            'orders.pickup_completed' => [
                'label' => 'Kurye gonderiyi teslim aldi',
                'description' => 'Pickup tamamlandiginda gonderen ve aliciyi bilgilendirir.',
                'channels' => ['sms'],
                'default_body' => 'Kurye gonderinizi teslim aldi. No: {order_no}. Teslimat adresi: {dropoff_address}. Takip: {track_url}',
                'variables' => [
                    'order_no' => 'Siparis numarasi.',
                    'dropoff_address' => 'Teslimatin gidecegi adres.',
                    'track_url' => 'Public siparis takip linki.',
                ],
            ],
            'orders.delivery_completed' => [
                'label' => 'Siparis teslim edildi',
                'description' => 'Teslimat tamamlandiginda kapanis bilgilendirmesini gonderir.',
                'channels' => ['sms'],
                'default_body' => 'Siparisiniz teslim edildi. No: {order_no}. Teslimat tamamlandi. Takip: {track_url}',
                'variables' => [
                    'order_no' => 'Siparis numarasi.',
                    'track_url' => 'Public siparis takip linki.',
                ],
            ],
        ];
    }

    /**
     * @return array{
     *   label:string,
     *   description:string,
     *   channels:array<int,string>,
     *   default_body:string,
     *   variables:array<string,string>
     * }|null
     */
    public static function definition(?string $eventKey): ?array
    {
        $normalized = trim((string) $eventKey);

        return self::definitions()[$normalized] ?? null;
    }

    /**
     * @return array<string, array{body:string,variables:array<int,string>}>
     */
    public static function defaultSmsTemplates(): array
    {
        $templates = [];

        foreach (self::definitions() as $eventKey => $definition) {
            if (! in_array('sms', $definition['channels'], true)) {
                continue;
            }

            $templates[$eventKey] = [
                'body' => $definition['default_body'],
                'variables' => array_keys($definition['variables']),
            ];
        }

        return $templates;
    }

    /**
     * @return array<string, string>
     */
    public static function legacyTemplateBodies(): array
    {
        return [
            'orders.payment_pending_bank_transfer' => 'Siparisiniz alindi. No: {order_no}. Havale odemesi bekleniyor. Tutar: {total_amount}. Takip: {track_url}',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function recommendedEventKeyLabels(): array
    {
        $labels = [];

        foreach (self::definitions() as $eventKey => $definition) {
            $labels[$eventKey] = $eventKey.' - '.$definition['label'];
        }

        return $labels;
    }

    /**
     * @return array<int, string>
     */
    public static function variableSuggestions(): array
    {
        $variables = [];

        foreach (self::definitions() as $definition) {
            foreach (array_keys($definition['variables']) as $variable) {
                $variables[$variable] = $variable;
            }
        }

        return array_values($variables);
    }
}
