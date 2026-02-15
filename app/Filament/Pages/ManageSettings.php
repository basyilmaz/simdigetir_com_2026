<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Modules\Settings\Models\Setting;

class ManageSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationLabel = 'Ayarlar';
    
    protected static ?string $title = 'Site Ayarları';
    
    protected static ?int $navigationSort = 100;
    
    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill([
            // Contact
            'contact_phone' => Setting::getValue('contact.phone', ''),
            'contact_whatsapp' => Setting::getValue('contact.whatsapp', ''),
            'contact_email' => Setting::getValue('contact.email', ''),
            'contact_address' => Setting::getValue('contact.address', ''),
            
            // Marketing
            'marketing_gtm_head' => Setting::getValue('marketing.gtm_head', ''),
            'marketing_gtm_body' => Setting::getValue('marketing.gtm_body', ''),
            'marketing_ga4_id' => Setting::getValue('marketing.ga4_id', ''),
            
            // Social
            'social_instagram' => Setting::getValue('social.instagram', ''),
            'social_linkedin' => Setting::getValue('social.linkedin', ''),
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
                                    ->placeholder('+90 212 XXX XX XX'),
                                Forms\Components\TextInput::make('contact_whatsapp')
                                    ->label('WhatsApp Numarası')
                                    ->placeholder('905321234567')
                                    ->helperText('Ülke kodu ile başlayın, boşluk kullanmayın'),
                                Forms\Components\TextInput::make('contact_email')
                                    ->label('E-posta')
                                    ->email()
                                    ->placeholder('info@simdigetir.com'),
                                Forms\Components\Textarea::make('contact_address')
                                    ->label('Adres')
                                    ->rows(2),
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
                            
                        Forms\Components\Tabs\Tab::make('Sosyal Medya')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\TextInput::make('social_instagram')
                                    ->label('Instagram')
                                    ->url()
                                    ->placeholder('https://instagram.com/simdigetir'),
                                Forms\Components\TextInput::make('social_linkedin')
                                    ->label('LinkedIn')
                                    ->url()
                                    ->placeholder('https://linkedin.com/company/simdigetir'),
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
        
        // Marketing
        Setting::setValue('marketing.gtm_head', $data['marketing_gtm_head'], 'marketing', $userId);
        Setting::setValue('marketing.gtm_body', $data['marketing_gtm_body'], 'marketing', $userId);
        Setting::setValue('marketing.ga4_id', $data['marketing_ga4_id'], 'marketing', $userId);
        
        // Social
        Setting::setValue('social.instagram', $data['social_instagram'], 'social', $userId);
        Setting::setValue('social.linkedin', $data['social_linkedin'], 'social', $userId);
        
        Notification::make()
            ->title('Ayarlar kaydedildi')
            ->success()
            ->send();
    }
}
