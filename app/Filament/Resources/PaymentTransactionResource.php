<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentTransactionResource\Pages;
use App\Models\PaymentTransaction;
use App\Support\CsvExporter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentTransactionResource extends Resource
{
    protected static ?string $model = PaymentTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Ödemeler';

    protected static ?string $modelLabel = 'Ödeme İşlemi';

    protected static ?string $pluralModelLabel = 'Ödemeler';

    protected static ?string $navigationGroup = 'Finans';

    protected static ?int $navigationSort = 21;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('İşlem Bilgileri')
                ->icon('heroicon-o-credit-card')
                ->schema([
                    Forms\Components\TextInput::make('id')
                        ->label('İşlem ID')
                        ->disabled(),
                    Forms\Components\TextInput::make('provider')
                        ->label('Sağlayıcı')
                        ->disabled(),
                    Forms\Components\TextInput::make('provider_reference')
                        ->label('Referans No')
                        ->disabled(),
                    Forms\Components\TextInput::make('status')
                        ->label('Durum')
                        ->disabled(),
                ])->columns(2),

            Forms\Components\Section::make('Tutar Bilgileri')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Forms\Components\Placeholder::make('amount_display')
                        ->label('Tutar')
                        ->content(fn (?PaymentTransaction $record): string => static::formatAmount($record?->amount, $record?->currency)),
                    Forms\Components\TextInput::make('currency')
                        ->label('Para Birimi')
                        ->disabled(),
                    Forms\Components\TextInput::make('order_id')
                        ->label('Sipariş ID')
                        ->disabled(),
                    Forms\Components\TextInput::make('pricing_quote_id')
                        ->label('Fiyat Teklifi ID')
                        ->disabled(),
                ])->columns(2),

            Forms\Components\Section::make('Teknik Detaylar')
                ->schema([
                    Forms\Components\Placeholder::make('processed_at_display')
                        ->label('İşlem / Kayıt Durumu')
                        ->content(fn (?PaymentTransaction $record): string => static::resolveProcessedAtSummary($record)),
                    Forms\Components\KeyValue::make('request_payload')
                        ->label('İstek Verisi')
                        ->disabled(),
                    Forms\Components\KeyValue::make('callback_payload')
                        ->label('Dönüş Verisi')
                        ->disabled(),
                    Forms\Components\DateTimePicker::make('created_at')
                        ->label('Oluşturulma Tarihi')
                        ->disabled(),
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
                Tables\Columns\TextColumn::make('provider')
                    ->label('Sağlayıcı')
                    ->badge(),
                Tables\Columns\TextColumn::make('provider_reference')
                    ->label('Referans No')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'succeeded' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Bekliyor',
                        'succeeded' => 'Başarılı',
                        'failed' => 'Başarısız',
                        'cancelled' => 'İptal',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Tutar')
                    ->formatStateUsing(fn ($state, PaymentTransaction $record): string => static::formatAmount($state, $record->currency)),
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Sipariş')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('İşlem / Kayıt')
                    ->state(fn (PaymentTransaction $record) => $record->processed_at ?? $record->created_at)
                    ->dateTime('d.m.Y H:i')
                    ->description(fn (PaymentTransaction $record): ?string => static::resolveProcessedAtMeta($record))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Bekliyor',
                        'succeeded' => 'Başarılı',
                        'failed' => 'Başarısız',
                        'cancelled' => 'İptal',
                    ]),
                Tables\Filters\Filter::make('created_at_range')
                    ->label('Tarih Aralığı')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Başlangıç'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Bitiş'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_csv')
                    ->label('CSV Dışa Aktar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function () {
                        $rows = PaymentTransaction::query()
                            ->latest('id')
                            ->cursor()
                            ->map(fn (PaymentTransaction $payment): array => static::exportRow($payment));

                        return CsvExporter::download(
                            filename: 'payment-transactions-' . now()->format('Ymd-His') . '.csv',
                            headers: ['ID', 'Sağlayıcı', 'Referans No', 'Durum', 'Tutar', 'Para Birimi', 'Sipariş ID', 'Tarih'],
                            rows: $rows
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Görüntüle'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentTransactions::route('/'),
            'view' => Pages\ViewPaymentTransaction::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['provider_reference', 'provider', 'status'];
    }

    public static function formatAmount(mixed $amount, ?string $currency = 'TRY'): string
    {
        if ($amount === null || $amount === '') {
            return '-';
        }

        $formatted = number_format(((int) $amount) / 100, 2, ',', '.');
        $normalizedCurrency = strtoupper(trim((string) ($currency ?: 'TRY')));
        $suffix = in_array($normalizedCurrency, ['TRY', 'TL'], true) ? '₺' : $normalizedCurrency;

        return "{$formatted} {$suffix}";
    }

    public static function resolveProcessedAtSummary(?PaymentTransaction $payment): string
    {
        if (! $payment) {
            return '-';
        }

        if ($payment->processed_at) {
            return 'İşlendi: '.$payment->processed_at->format('d.m.Y H:i');
        }

        if ($payment->created_at) {
            return 'Bekliyor - Kayıt: '.$payment->created_at->format('d.m.Y H:i');
        }

        return '-';
    }

    public static function resolveProcessedAtMeta(PaymentTransaction $payment): ?string
    {
        return $payment->processed_at ? null : 'Henüz işlenmedi';
    }

    /**
     * @return array<int, string|int|null>
     */
    public static function exportRow(PaymentTransaction $payment): array
    {
        return [
            $payment->id,
            $payment->provider,
            $payment->provider_reference,
            $payment->status,
            static::formatAmount($payment->amount, $payment->currency),
            $payment->currency,
            $payment->order_id,
            optional($payment->processed_at ?? $payment->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
