<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Modules\Settings\Models\Setting;

class ManageSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Ayarlar';

    protected static ?string $title = 'Site Ayarları';

    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user?->can('settings.manage') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            // Contact
            'contact_phone' => Setting::getValue('contact.phone', ''),
            'contact_whatsapp' => Setting::getValue('contact.whatsapp', ''),
            'contact_email' => Setting::getValue('contact.email', ''),
            'contact_address' => Setting::getValue('contact.address', ''),
            'business_hours_label' => Setting::getValue('business.hours_label', ''),
            'business_hours_weekdays' => Setting::getValue('business.hours_weekdays', ''),
            'business_hours_weekend' => Setting::getValue('business.hours_weekend', ''),

            // Brand
            'brand_logo_url' => Setting::getValue('brand.logo_url', ''),
            'brand_logo_alt' => Setting::getValue('brand.logo_alt', ''),
            'brand_logo_height_sm' => Setting::getValue('brand.logo_height_sm', ''),
            'brand_logo_height_md' => Setting::getValue('brand.logo_height_md', ''),
            'brand_logo_height_lg' => Setting::getValue('brand.logo_height_lg', ''),

            // Marketing
            'marketing_gtm_head' => Setting::getValue('marketing.gtm_head', ''),
            'marketing_gtm_body' => Setting::getValue('marketing.gtm_body', ''),
            'marketing_ga4_id' => Setting::getValue('marketing.ga4_id', ''),

            // Checkout
            'checkout_bank_transfer_title' => Setting::getValue('checkout.bank_transfer_title', 'Havale / EFT Odeme Talimati'),
            'checkout_bank_transfer_body' => Setting::getValue(
                'checkout.bank_transfer_body',
                'Havale odemenizi tamamladiktan sonra siparisiniz finans ekibinin kontrolune duser.'
            ),
            'checkout_bank_transfer_bank_name' => Setting::getValue('checkout.bank_transfer_bank_name', ''),
            'checkout_bank_transfer_account_holder' => Setting::getValue('checkout.bank_transfer_account_holder', ''),
            'checkout_bank_transfer_iban' => Setting::getValue('checkout.bank_transfer_iban', ''),
            'checkout_bank_transfer_reference_note' => Setting::getValue(
                'checkout.bank_transfer_reference_note',
                'Aciklama alanina siparis numaranizi yazin.'
            ),

            // Social
            'social_facebook' => Setting::getValue('social.facebook', ''),
            'social_instagram' => Setting::getValue('social.instagram', ''),
            'social_twitter' => Setting::getValue('social.twitter', ''),
            'social_linkedin' => Setting::getValue('social.linkedin', ''),
            'social_youtube' => Setting::getValue('social.youtube', ''),

            // Operations
            'ops_sla_lead_new_minutes' => (int) Setting::getValue('ops.sla_lead_new_minutes', 15),
            'ops_sla_ticket_open_minutes' => (int) Setting::getValue('ops.sla_ticket_open_minutes', 30),
            'ops_sla_order_pending_payment_minutes' => (int) Setting::getValue('ops.sla_order_pending_payment_minutes', 20),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Ayarlar')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('İletişim')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\TextInput::make('contact_phone')
                                    ->label('Telefon')
                                    ->tel()
                                    ->placeholder('+90 551 356 72 92'),
                                Forms\Components\TextInput::make('contact_whatsapp')
                                    ->label('WhatsApp Numarası')
                                    ->placeholder('905513567292')
                                    ->helperText('Ülke kodu ile başlayın, boşluk kullanmayın'),
                                Forms\Components\TextInput::make('contact_email')
                                    ->label('E-posta')
                                    ->email()
                                    ->placeholder('info@simdigetir.com'),
                                Forms\Components\Textarea::make('contact_address')
                                    ->label('Adres')
                                    ->rows(2),
                                Forms\Components\TextInput::make('business_hours_label')
                                    ->label('Hızlı Saat Etiketi')
                                    ->placeholder('7/24 Aktif Hizmet'),
                                Forms\Components\TextInput::make('business_hours_weekdays')
                                    ->label('Hafta İçi Çalışma Saatleri')
                                    ->placeholder('Pzt-Cum 00:00 - 23:59'),
                                Forms\Components\TextInput::make('business_hours_weekend')
                                    ->label('Hafta Sonu Çalışma Saatleri')
                                    ->placeholder('Cts-Paz 00:00 - 23:59'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Marka')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\TextInput::make('brand_logo_url')
                                    ->label('Logo URL')
                                    ->url()
                                    ->placeholder('https://.../logo.svg'),
                                Forms\Components\TextInput::make('brand_logo_alt')
                                    ->label('Logo Alt Yazısı')
                                    ->placeholder('SimdiGetir'),
                                Forms\Components\TextInput::make('brand_logo_height_sm')
                                    ->label('Logo Height SM (px)')
                                    ->numeric(),
                                Forms\Components\TextInput::make('brand_logo_height_md')
                                    ->label('Logo Height MD (px)')
                                    ->numeric(),
                                Forms\Components\TextInput::make('brand_logo_height_lg')
                                    ->label('Logo Height LG (px)')
                                    ->numeric(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Pazarlama')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Forms\Components\TextInput::make('marketing_ga4_id')
                                    ->label('Google Analytics 4 ID')
                                    ->placeholder('G-XXXXXXXXXX'),
                                Forms\Components\Textarea::make('marketing_gtm_head')
                                    ->label('GTM Head Script')
                                    ->rows(5)
                                    ->helperText('<head> içine eklenecek GTM kodu'),
                                Forms\Components\Textarea::make('marketing_gtm_body')
                                    ->label('GTM Body Script')
                                    ->rows(5)
                                    ->helperText('<body> açılışına eklenecek GTM kodu'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Checkout')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Forms\Components\TextInput::make('checkout_bank_transfer_title')
                                    ->label('Havale Basligi')
                                    ->placeholder('Havale / EFT Odeme Talimati'),
                                Forms\Components\Textarea::make('checkout_bank_transfer_body')
                                    ->label('Havale Aciklamasi')
                                    ->rows(4)
                                    ->helperText('Checkout odeme adiminda ve siparis detayinda gorunur.'),
                                Forms\Components\TextInput::make('checkout_bank_transfer_bank_name')
                                    ->label('Banka Adi')
                                    ->placeholder('Orn: Garanti BBVA'),
                                Forms\Components\TextInput::make('checkout_bank_transfer_account_holder')
                                    ->label('Hesap Sahibi')
                                    ->placeholder('Orn: SimdiGetir Ltd. Sti.'),
                                Forms\Components\TextInput::make('checkout_bank_transfer_iban')
                                    ->label('IBAN')
                                    ->placeholder('TR00 0000 0000 0000 0000 0000 00'),
                                Forms\Components\Textarea::make('checkout_bank_transfer_reference_note')
                                    ->label('Referans Notu')
                                    ->rows(3)
                                    ->helperText('Musteriye hangi aciklama/ref kodunu yazmasi gerektigini anlatin.'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Sosyal Medya')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\TextInput::make('social_facebook')
                                    ->label('Facebook')
                                    ->url()
                                    ->placeholder('https://facebook.com/simdigetir'),
                                Forms\Components\TextInput::make('social_instagram')
                                    ->label('Instagram')
                                    ->url()
                                    ->placeholder('https://instagram.com/simdigetir'),
                                Forms\Components\TextInput::make('social_twitter')
                                    ->label('X / Twitter')
                                    ->url()
                                    ->placeholder('https://x.com/simdigetir'),
                                Forms\Components\TextInput::make('social_linkedin')
                                    ->label('LinkedIn')
                                    ->url()
                                    ->placeholder('https://linkedin.com/company/simdigetir'),
                                Forms\Components\TextInput::make('social_youtube')
                                    ->label('YouTube')
                                    ->url()
                                    ->placeholder('https://youtube.com/@simdigetir'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Operasyon')
                            ->icon('heroicon-o-bolt')
                            ->schema([
                                Forms\Components\TextInput::make('ops_sla_lead_new_minutes')
                                    ->label('Yeni Lead SLA (dk)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(15)
                                    ->helperText('Yeni lead bu dakikayı aşarsa SLA alarmına girer.'),
                                Forms\Components\TextInput::make('ops_sla_ticket_open_minutes')
                                    ->label('Açık Ticket SLA (dk)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(30)
                                    ->helperText('Açık/Pending destek talebi bu dakikayı aşarsa SLA alarmına girer.'),
                                Forms\Components\TextInput::make('ops_sla_order_pending_payment_minutes')
                                    ->label('Ödeme Bekleyen Sipariş SLA (dk)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(20)
                                    ->helperText('Pending payment sipariş bu dakikayı aşarsa SLA alarmına girer.'),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Kaydet')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $userId = auth()->id();

        // Contact
        Setting::setValue('contact.phone', $data['contact_phone'], 'contact', $userId);
        Setting::setValue('contact.whatsapp', $data['contact_whatsapp'], 'contact', $userId);
        Setting::setValue('contact.email', $data['contact_email'], 'contact', $userId);
        Setting::setValue('contact.address', $data['contact_address'], 'contact', $userId);

        // Business
        Setting::setValue('business.hours_label', $data['business_hours_label'], 'business', $userId);
        Setting::setValue('business.hours_weekdays', $data['business_hours_weekdays'], 'business', $userId);
        Setting::setValue('business.hours_weekend', $data['business_hours_weekend'], 'business', $userId);

        // Brand
        Setting::setValue('brand.logo_url', $data['brand_logo_url'], 'brand', $userId);
        Setting::setValue('brand.logo_alt', $data['brand_logo_alt'], 'brand', $userId);
        Setting::setValue('brand.logo_height_sm', $data['brand_logo_height_sm'], 'brand', $userId);
        Setting::setValue('brand.logo_height_md', $data['brand_logo_height_md'], 'brand', $userId);
        Setting::setValue('brand.logo_height_lg', $data['brand_logo_height_lg'], 'brand', $userId);

        // Marketing
        Setting::setValue('marketing.gtm_head', $data['marketing_gtm_head'], 'marketing', $userId);
        Setting::setValue('marketing.gtm_body', $data['marketing_gtm_body'], 'marketing', $userId);
        Setting::setValue('marketing.ga4_id', $data['marketing_ga4_id'], 'marketing', $userId);

        // Checkout
        Setting::setValue('checkout.bank_transfer_title', $data['checkout_bank_transfer_title'], 'checkout', $userId);
        Setting::setValue('checkout.bank_transfer_body', $data['checkout_bank_transfer_body'], 'checkout', $userId);
        Setting::setValue('checkout.bank_transfer_bank_name', $data['checkout_bank_transfer_bank_name'], 'checkout', $userId);
        Setting::setValue('checkout.bank_transfer_account_holder', $data['checkout_bank_transfer_account_holder'], 'checkout', $userId);
        Setting::setValue('checkout.bank_transfer_iban', $data['checkout_bank_transfer_iban'], 'checkout', $userId);
        Setting::setValue('checkout.bank_transfer_reference_note', $data['checkout_bank_transfer_reference_note'], 'checkout', $userId);

        // Social
        Setting::setValue('social.facebook', $data['social_facebook'], 'social', $userId);
        Setting::setValue('social.instagram', $data['social_instagram'], 'social', $userId);
        Setting::setValue('social.twitter', $data['social_twitter'], 'social', $userId);
        Setting::setValue('social.linkedin', $data['social_linkedin'], 'social', $userId);
        Setting::setValue('social.youtube', $data['social_youtube'], 'social', $userId);

        // Operations
        Setting::setValue('ops.sla_lead_new_minutes', (int) ($data['ops_sla_lead_new_minutes'] ?? 15), 'operations', $userId);
        Setting::setValue('ops.sla_ticket_open_minutes', (int) ($data['ops_sla_ticket_open_minutes'] ?? 30), 'operations', $userId);
        Setting::setValue('ops.sla_order_pending_payment_minutes', (int) ($data['ops_sla_order_pending_payment_minutes'] ?? 20), 'operations', $userId);

        Notification::make()
            ->title('Ayarlar kaydedildi')
            ->success()
            ->send();
    }
}
