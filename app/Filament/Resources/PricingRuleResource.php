<?php

namespace App\Filament\Resources;

use App\Domain\Pricing\Services\PricingQuoteResolver;
use App\Domain\Pricing\Services\PricingServiceCatalog;
use App\Filament\Resources\PricingRuleResource\Pages;
use App\Models\PricingRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class PricingRuleResource extends Resource
{
    protected static ?string $model = PricingRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Fiyat Kurallari';

    protected static ?string $modelLabel = 'Fiyat Kurali';

    protected static ?string $pluralModelLabel = 'Fiyat Kurallari';

    protected static ?string $navigationGroup = 'Operasyon';

    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Kural Bilgileri')
                ->icon('heroicon-o-adjustments-horizontal')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Kural Adi')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('rule_type')
                        ->label('Kural Tipi')
                        ->options(static::ruleTypeOptions())
                        ->required()
                        ->native(false)
                        ->default(PricingServiceCatalog::SERVICE_BASE_PRICE_RULE_TYPE),
                    Forms\Components\TextInput::make('priority')
                        ->label('Oncelik')
                        ->numeric()
                        ->required()
                        ->default(100),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ])->columns(2),

            Forms\Components\Section::make('Hizmet Baz Fiyati')
                ->description('Landing quote widget bu katalogdan beslenir. Baz fiyatlari burada yonetin.')
                ->icon('heroicon-o-banknotes')
                ->visible(fn (Get $get): bool => (string) $get('rule_type') === PricingServiceCatalog::SERVICE_BASE_PRICE_RULE_TYPE)
                ->schema([
                    Forms\Components\TextInput::make('service_type_key')
                        ->label('Hizmet Anahtari')
                        ->required()
                        ->maxLength(40)
                        ->placeholder('moto'),
                    Forms\Components\TextInput::make('service_label')
                        ->label('Gorunen Hizmet Adi')
                        ->required()
                        ->maxLength(120)
                        ->placeholder('Moto Kurye'),
                    Forms\Components\TextInput::make('service_base_price_try')
                        ->label('Baz Fiyat (TL)')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->step('0.01'),
                    Forms\Components\TextInput::make('fallback_minutes')
                        ->label('Tahmini Sure Fallback (dk)')
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->default(45),
                    Forms\Components\Toggle::make('service_is_default')
                        ->label('Varsayilan Hizmet')
                        ->default(false),
                ])->columns(2),

            Forms\Components\Section::make('Dinamik Kural JSON')
                ->description('Genel surge/indirim/carpan kurallari icin kullanin. Hizmet baz fiyatlari icin yukaridaki alani tercih edin.')
                ->icon('heroicon-o-code-bracket')
                ->visible(fn (Get $get): bool => (string) $get('rule_type') !== PricingServiceCatalog::SERVICE_BASE_PRICE_RULE_TYPE)
                ->schema([
                    Forms\Components\Textarea::make('conditions_json')
                        ->label('Kosullar (JSON)')
                        ->rows(6)
                        ->default('{}'),
                    Forms\Components\Textarea::make('effect_json')
                        ->label('Etki (JSON)')
                        ->rows(6)
                        ->default('{}'),
                ])->columns(2),

            Forms\Components\Section::make('Gecerlilik Suresi')
                ->icon('heroicon-o-clock')
                ->schema([
                    Forms\Components\DateTimePicker::make('active_from')
                        ->label('Baslangic Tarihi')
                        ->seconds(false),
                    Forms\Components\DateTimePicker::make('active_until')
                        ->label('Bitis Tarihi')
                        ->seconds(false),
                ])->columns(2)
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Kural Adi')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('rule_type')
                    ->label('Tip')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => static::ruleTypeOptions()[$state] ?? $state),
                Tables\Columns\TextColumn::make('service_display')
                    ->label('Hizmet')
                    ->state(fn (PricingRule $record): string => static::resolveServiceDisplay($record))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('service_base_amount_display')
                    ->label('Baz Fiyat')
                    ->state(fn (PricingRule $record): string => static::resolveServiceBaseAmountDisplay($record))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Oncelik')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Guncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('priority')
            ->emptyStateHeading(static::emptyStateHeading())
            ->emptyStateDescription(static::emptyStateDescription())
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Kural Ekle'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Duzenle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Sil'),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('bootstrap_service_base_prices')
                    ->label('Varsayilan Hizmet Fiyatlarini Hazirla')
                    ->icon('heroicon-o-sparkles')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        static::bootstrapServiceBasePriceRules();

                        Notification::make()
                            ->title('Varsayilan hizmet fiyatlari hazirlandi')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('dry_run')
                    ->label('Test Hesaplama')
                    ->icon('heroicon-o-beaker')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('service_type')
                            ->label('Hizmet')
                            ->options(app(PricingServiceCatalog::class)->optionMapForSelect(static::fallbackServiceOptions()))
                            ->native(false)
                            ->required(),
                        Forms\Components\TextInput::make('base_amount_try')
                            ->label('Baz Tutar (TL, bos birakilabilir)')
                            ->numeric()
                            ->minValue(0)
                            ->step('0.01'),
                        Forms\Components\TextInput::make('zone')
                            ->label('Bolge')
                            ->default('A'),
                        Forms\Components\TextInput::make('hour')
                            ->label('Saat')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(23)
                            ->default(12),
                    ])
                    ->action(function (array $data): void {
                        $catalog = app(PricingServiceCatalog::class);
                        $serviceType = (string) ($data['service_type'] ?? 'moto');
                        $baseAmount = isset($data['base_amount_try']) && $data['base_amount_try'] !== ''
                            ? (int) round((float) $data['base_amount_try'] * 100)
                            : (int) ($catalog->resolveBaseAmountForService($serviceType, static::fallbackServiceOptions()) ?? 1000);

                        $resolver = app(PricingQuoteResolver::class);
                        $result = $resolver->resolveFromDatabase([
                            'base_amount' => $baseAmount,
                            'service_type' => $serviceType,
                            'zone' => (string) ($data['zone'] ?? ''),
                            'hour' => (int) ($data['hour'] ?? 0),
                            'currency' => 'TRY',
                        ]);

                        $applied = collect($result['applied_rules'] ?? [])
                            ->pluck('rule_name')
                            ->filter()
                            ->values()
                            ->implode(', ');

                        Notification::make()
                            ->title('Test Hesaplama Sonucu')
                            ->body(sprintf(
                                'Hizmet: %s | Baz: %s | Toplam: %s | Kurallar: %s',
                                $catalog->resolveLabelForService($serviceType, static::fallbackServiceOptions()) ?? strtoupper($serviceType),
                                static::formatAmount($baseAmount),
                                static::formatAmount((int) ($result['total_amount'] ?? 0)),
                                $applied !== '' ? $applied : '-'
                            ))
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function mutateFormDataBeforeFill(array $data): array
    {
        $data['conditions_json'] = json_encode($data['conditions'] ?? [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $data['effect_json'] = json_encode($data['effect'] ?? [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        if (($data['rule_type'] ?? null) === PricingServiceCatalog::SERVICE_BASE_PRICE_RULE_TYPE) {
            $data['service_type_key'] = (string) Arr::get($data, 'conditions.service_type', '');
            $data['service_label'] = (string) Arr::get($data, 'effect.service_label', '');
            $data['service_base_price_try'] = number_format(((int) Arr::get($data, 'effect.base_amount', 0)) / 100, 2, '.', '');
            $data['fallback_minutes'] = (int) Arr::get($data, 'effect.fallback_minutes', 45);
            $data['service_is_default'] = (bool) Arr::get($data, 'effect.is_default', false);
        }

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['rule_type'] ?? null) === PricingServiceCatalog::SERVICE_BASE_PRICE_RULE_TYPE) {
            $serviceType = trim((string) ($data['service_type_key'] ?? ''));
            $serviceLabel = trim((string) ($data['service_label'] ?? ''));
            $basePriceTry = $data['service_base_price_try'] ?? null;

            if ($serviceType === '' || $serviceLabel === '' || $basePriceTry === null || $basePriceTry === '') {
                throw ValidationException::withMessages([
                    'service_type_key' => 'Hizmet anahtari zorunludur.',
                    'service_label' => 'Gorunen hizmet adi zorunludur.',
                    'service_base_price_try' => 'Baz fiyat zorunludur.',
                ]);
            }

            $data['conditions'] = [
                'service_type' => $serviceType,
            ];
            $data['effect'] = [
                'type' => 'set_base_amount',
                'service_label' => $serviceLabel,
                'base_amount' => (int) round((float) $basePriceTry * 100),
                'fallback_minutes' => max(1, (int) ($data['fallback_minutes'] ?? 45)),
                'is_default' => (bool) ($data['service_is_default'] ?? false),
            ];
            unset(
                $data['conditions_json'],
                $data['effect_json'],
                $data['service_type_key'],
                $data['service_label'],
                $data['service_base_price_try'],
                $data['fallback_minutes'],
                $data['service_is_default']
            );

            return $data;
        }

        $conditions = json_decode((string) ($data['conditions_json'] ?? '{}'), true);
        $effect = json_decode((string) ($data['effect_json'] ?? '{}'), true);
        $data['conditions'] = is_array($conditions) ? $conditions : [];
        $data['effect'] = is_array($effect) ? $effect : [];
        unset($data['conditions_json'], $data['effect_json']);

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPricingRules::route('/'),
            'create' => Pages\CreatePricingRule::route('/create'),
            'edit' => Pages\EditPricingRule::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'rule_type'];
    }

    public static function emptyStateHeading(): string
    {
        return 'Henüz fiyat kuralı tanımlanmadı';
    }

    public static function emptyStateDescription(): string
    {
        return 'Bu ekran boşsa dinamik fiyat kuralları devrede değildir. Yeni kural ekleyerek operasyon bazlı fiyat mantığını yönetin.';
    }

    public static function ruleTypeOptions(): array
    {
        return [
            PricingServiceCatalog::SERVICE_BASE_PRICE_RULE_TYPE => 'Hizmet Baz Fiyati',
            'pricing_adjustment' => 'Dinamik Ayar Kurali',
        ];
    }

    public static function resolveServiceDisplay(PricingRule $record): string
    {
        if ($record->rule_type !== PricingServiceCatalog::SERVICE_BASE_PRICE_RULE_TYPE) {
            return '-';
        }

        $label = trim((string) data_get($record->effect, 'service_label', ''));
        $key = trim((string) data_get($record->conditions, 'service_type', ''));

        if ($label === '' && $key === '') {
            return '-';
        }

        return $label !== '' && $key !== '' ? sprintf('%s (%s)', $label, $key) : ($label ?: $key);
    }

    public static function resolveServiceBaseAmountDisplay(PricingRule $record): string
    {
        if ($record->rule_type !== PricingServiceCatalog::SERVICE_BASE_PRICE_RULE_TYPE) {
            return '-';
        }

        return static::formatAmount((int) data_get($record->effect, 'base_amount', 0));
    }

    public static function formatAmount(int $amountMinor): string
    {
        return number_format($amountMinor / 100, 2, ',', '.') . ' TL';
    }

    public static function bootstrapServiceBasePriceRules(): void
    {
        foreach (static::defaultServiceBasePriceRules() as $rule) {
            PricingRule::query()->updateOrCreate(
                [
                    'rule_type' => PricingServiceCatalog::SERVICE_BASE_PRICE_RULE_TYPE,
                    'name' => $rule['name'],
                ],
                [
                    'priority' => $rule['priority'],
                    'conditions' => [
                        'service_type' => $rule['service_type_key'],
                    ],
                    'effect' => [
                        'type' => 'set_base_amount',
                        'service_label' => $rule['service_label'],
                        'base_amount' => $rule['base_amount'],
                        'fallback_minutes' => $rule['fallback_minutes'],
                        'is_default' => $rule['is_default'],
                    ],
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function defaultServiceBasePriceRules(): array
    {
        return [
            [
                'name' => 'Servis Baz Fiyati - Moto Kurye',
                'service_type_key' => 'moto',
                'service_label' => 'Moto Kurye',
                'base_amount' => 25000,
                'fallback_minutes' => 45,
                'priority' => 10,
                'is_default' => true,
            ],
            [
                'name' => 'Servis Baz Fiyati - Acil Kurye',
                'service_type_key' => 'urgent',
                'service_label' => 'Acil Kurye',
                'base_amount' => 35000,
                'fallback_minutes' => 35,
                'priority' => 20,
                'is_default' => false,
            ],
            [
                'name' => 'Servis Baz Fiyati - Aracli Kurye',
                'service_type_key' => 'van',
                'service_label' => 'Aracli Kurye',
                'base_amount' => 45000,
                'fallback_minutes' => 70,
                'priority' => 30,
                'is_default' => false,
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function fallbackServiceOptions(): array
    {
        return [
            ['value' => 'moto', 'label' => 'Moto Kurye', 'base_amount' => 25000, 'fallback_minutes' => 45, 'is_default' => true],
            ['value' => 'urgent', 'label' => 'Acil Kurye', 'base_amount' => 35000, 'fallback_minutes' => 35, 'is_default' => false],
            ['value' => 'van', 'label' => 'Aracli Kurye', 'base_amount' => 45000, 'fallback_minutes' => 70, 'is_default' => false],
        ];
    }
}
