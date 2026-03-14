<?php

namespace App\Filament\Resources;

use App\Domain\Pricing\Services\PricingQuoteResolver;
use App\Filament\Resources\PricingRuleResource\Pages;
use App\Models\PricingRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PricingRuleResource extends Resource
{
    protected static ?string $model = PricingRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Fiyat Kuralları';

    protected static ?string $modelLabel = 'Fiyat Kuralı';

    protected static ?string $pluralModelLabel = 'Fiyat Kuralları';

    protected static ?string $navigationGroup = 'Operasyon';

    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Kural Bilgileri')
                ->icon('heroicon-o-adjustments-horizontal')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Kural Adı')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('rule_type')
                        ->label('Kural Tipi')
                        ->required()
                        ->maxLength(40),
                    Forms\Components\TextInput::make('priority')
                        ->label('Öncelik')
                        ->numeric()
                        ->required()
                        ->default(100),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ])->columns(2),

            Forms\Components\Section::make('Koşullar & Etki')
                ->icon('heroicon-o-code-bracket')
                ->schema([
                    Forms\Components\Textarea::make('conditions_json')
                        ->label('Koşullar (JSON)')
                        ->rows(6)
                        ->default('{}'),
                    Forms\Components\Textarea::make('effect_json')
                        ->label('Etki (JSON)')
                        ->rows(6)
                        ->default('{}'),
                ])->columns(2),

            Forms\Components\Section::make('Geçerlilik Süresi')
                ->icon('heroicon-o-clock')
                ->schema([
                    Forms\Components\DateTimePicker::make('active_from')
                        ->label('Başlangıç Tarihi')
                        ->seconds(false),
                    Forms\Components\DateTimePicker::make('active_until')
                        ->label('Bitiş Tarihi')
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
                    ->label('Kural Adı')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('rule_type')
                    ->label('Tip')
                    ->badge(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Öncelik')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('active_from')
                    ->label('Başlangıç')
                    ->dateTime('d.m.Y H:i')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('active_until')
                    ->label('Bitiş')
                    ->dateTime('d.m.Y H:i')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('priority')
            ->actions([
                Tables\Actions\EditAction::make()->label('Düzenle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Sil'),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('dry_run')
                    ->label('Test Hesaplama')
                    ->icon('heroicon-o-beaker')
                    ->color('info')
                    ->form([
                        Forms\Components\TextInput::make('base_amount')
                            ->label('Baz Tutar')
                            ->numeric()
                            ->required()
                            ->default(1000),
                        Forms\Components\TextInput::make('zone')
                            ->label('Bölge')
                            ->default('A'),
                        Forms\Components\TextInput::make('hour')
                            ->label('Saat')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(23)
                            ->default(12),
                    ])
                    ->action(function (array $data) {
                        $resolver = app(PricingQuoteResolver::class);
                        $result = $resolver->resolveFromDatabase([
                            'base_amount' => (int) $data['base_amount'],
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
                                'Toplam: %s %s | Uygulanan Kurallar: %s',
                                number_format((int) $result['total_amount']),
                                (string) ($result['currency'] ?? 'TRY'),
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

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
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
}
