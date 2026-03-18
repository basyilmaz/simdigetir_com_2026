<?php

namespace App\Filament\Resources;

use App\Domain\Orders\Enums\OrderState;
use App\Domain\Orders\Exceptions\InvalidOrderTransitionException;
use App\Domain\Orders\Services\OrderStateTransitionService;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\OrderStateLogsRelationManager;
use App\Filament\Resources\OrderResource\RelationManagers\PaymentTransactionsRelationManager;
use App\Models\Order;
use App\Support\CsvExporter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Throwable;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Siparisler';

    protected static ?string $modelLabel = 'Siparis';

    protected static ?string $pluralModelLabel = 'Siparisler';

    protected static ?string $navigationGroup = 'Operasyon';

    protected static ?int $navigationSort = 41;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Siparis Bilgileri')
                ->icon('heroicon-o-shopping-bag')
                ->schema([
                    Forms\Components\TextInput::make('order_no')
                        ->label('Siparis No')
                        ->disabled(),
                    Forms\Components\TextInput::make('state')
                        ->label('Durum')
                        ->disabled(),
                    Forms\Components\TextInput::make('payment_state')
                        ->label('Odeme Durumu')
                        ->disabled(),
                    Forms\Components\TextInput::make('customer_id')
                        ->label('Musteri ID')
                        ->disabled(),
                    Forms\Components\Placeholder::make('customer_name_display')
                        ->label('Musteri')
                        ->content(fn (?Order $record): string => static::resolveCustomerName($record)),
                    Forms\Components\Placeholder::make('customer_contact_display')
                        ->label('Musteri Iletisim')
                        ->content(fn (?Order $record): string => static::resolveCustomerContact($record)),
                ])->columns(2),

            Forms\Components\Section::make('Alis Noktasi')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Forms\Components\TextInput::make('pickup_name')
                        ->label('Gonderici Adi')
                        ->disabled(),
                    Forms\Components\TextInput::make('pickup_phone')
                        ->label('Gonderici Telefon')
                        ->disabled(),
                    Forms\Components\Textarea::make('pickup_address')
                        ->label('Alis Adresi')
                        ->disabled()
                        ->rows(2),
                ])->columns(2),

            Forms\Components\Section::make('Teslimat Noktasi')
                ->icon('heroicon-o-flag')
                ->schema([
                    Forms\Components\TextInput::make('dropoff_name')
                        ->label('Alici Adi')
                        ->disabled(),
                    Forms\Components\TextInput::make('dropoff_phone')
                        ->label('Alici Telefon')
                        ->disabled(),
                    Forms\Components\Textarea::make('dropoff_address')
                        ->label('Teslimat Adresi')
                        ->disabled()
                        ->rows(2),
                ])->columns(2),

            Forms\Components\Section::make('Ucret Bilgileri')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Forms\Components\Placeholder::make('total_amount_display')
                        ->label('Toplam Tutar')
                        ->content(fn (?Order $record): string => static::formatAmount($record?->total_amount, $record?->currency)),
                    Forms\Components\TextInput::make('currency')
                        ->label('Para Birimi')
                        ->disabled(),
                    Forms\Components\KeyValue::make('price_breakdown')
                        ->label('Fiyat Detayi')
                        ->disabled(),
                ])->columns(2),

            Forms\Components\Section::make('Tarih Bilgileri')
                ->schema([
                    Forms\Components\DateTimePicker::make('created_at')
                        ->label('Olusturulma Tarihi')
                        ->disabled(),
                ])->collapsed(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_no')
                    ->label('Siparis No')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('customer_display')
                    ->label('Musteri')
                    ->state(fn (Order $record): string => static::resolveCustomerName($record))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $builder) use ($search): void {
                            $builder
                                ->whereHas('customer', function (Builder $customerQuery) use ($search): void {
                                    $customerQuery
                                        ->where('name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%")
                                        ->orWhere('phone', 'like', "%{$search}%");
                                })
                                ->orWhere('pickup_name', 'like', "%{$search}%")
                                ->orWhere('pickup_phone', 'like', "%{$search}%");
                        });
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('customer_contact')
                    ->label('Iletisim')
                    ->state(fn (Order $record): string => static::resolveCustomerContact($record))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('state')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending_payment' => 'warning',
                        'paid' => 'info',
                        'assigned' => 'primary',
                        'picked_up' => 'info',
                        'delivered' => 'success',
                        'closed' => 'gray',
                        'cancelled' => 'danger',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Taslak',
                        'pending_payment' => 'Odeme Bekleniyor',
                        'paid' => 'Odendi',
                        'assigned' => 'Atandi',
                        'picked_up' => 'Alindi',
                        'delivered' => 'Teslim Edildi',
                        'closed' => 'Kapatildi',
                        'cancelled' => 'Iptal',
                        'failed' => 'Basarisiz',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('payment_state')
                    ->label('Odeme')
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
                        'succeeded' => 'Basarili',
                        'failed' => 'Basarisiz',
                        'cancelled' => 'Iptal',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Tutar')
                    ->formatStateUsing(fn ($state, Order $record): string => static::formatAmount($state, $record->currency)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentTransactions_count')
                    ->label('Odeme Kaydi')
                    ->counts('paymentTransactions')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->emptyStateHeading(static::emptyStateHeading())
            ->emptyStateDescription(static::emptyStateDescription())
            ->filters([
                Tables\Filters\SelectFilter::make('state')
                    ->label('Siparis Durumu')
                    ->options(static::orderStateOptions()),
                Tables\Filters\SelectFilter::make('payment_state')
                    ->label('Odeme Durumu')
                    ->options([
                        'pending' => 'Bekliyor',
                        'succeeded' => 'Basarili',
                        'failed' => 'Basarisiz',
                        'cancelled' => 'Iptal',
                    ]),
                Tables\Filters\Filter::make('created_at_range')
                    ->label('Tarih Araligi')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Baslangic'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Bitis'),
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
                    ->label('CSV Disa Aktar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function () {
                        $rows = Order::query()
                            ->with('customer')
                            ->latest('id')
                            ->cursor()
                            ->map(fn (Order $order): array => static::exportRow($order));

                        return CsvExporter::download(
                            filename: 'orders-' . now()->format('Ymd-His') . '.csv',
                            headers: ['ID', 'Siparis No', 'Durum', 'Odeme Durumu', 'Tutar', 'Para Birimi', 'Tarih'],
                            rows: $rows
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Goruntule'),
                Tables\Actions\Action::make('payments')
                    ->label('Odemeler')
                    ->icon('heroicon-o-credit-card')
                    ->color('info')
                    ->url(fn (Order $record): string => PaymentTransactionResource::getUrl('index', [
                        'tableSearch' => (string) $record->id,
                    ]))
                    ->openUrlInNewTab(),
                static::manualTransitionTableAction(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Sil'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrderStateLogsRelationManager::class,
            PaymentTransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['order_no', 'pickup_name', 'dropoff_name', 'pickup_phone', 'dropoff_phone'];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('customer');
    }

    public static function resolveCustomerName(?Order $order): string
    {
        if (! $order) {
            return '-';
        }

        $customerName = trim((string) ($order->customer?->name ?? ''));
        if ($customerName !== '') {
            return $customerName;
        }

        $snapshotName = trim((string) Arr::get($order->checkout_snapshot ?? [], 'customer.name', ''));
        if ($snapshotName !== '') {
            return $snapshotName;
        }

        $pickupName = trim((string) ($order->pickup_name ?? ''));

        return $pickupName !== '' ? $pickupName : '-';
    }

    public static function resolveCustomerContact(?Order $order): string
    {
        if (! $order) {
            return '-';
        }

        foreach ([
            $order->customer?->email,
            $order->customer?->phone,
            Arr::get($order->checkout_snapshot ?? [], 'customer.email'),
            Arr::get($order->checkout_snapshot ?? [], 'customer.phone'),
            $order->pickup_phone,
        ] as $candidate) {
            $value = trim((string) ($candidate ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return '-';
    }

    public static function formatAmount(mixed $amount, ?string $currency = 'TRY'): string
    {
        if ($amount === null || $amount === '') {
            return '-';
        }

        $formatted = number_format(((int) $amount) / 100, 2, ',', '.');
        $normalizedCurrency = strtoupper(trim((string) ($currency ?: 'TRY')));
        $suffix = in_array($normalizedCurrency, ['TRY', 'TL'], true) ? "\u{20BA}" : $normalizedCurrency;

        return "{$formatted} {$suffix}";
    }

    /**
     * @return array<int, string|int|null>
     */
    public static function exportRow(Order $order): array
    {
        return [
            $order->id,
            $order->order_no,
            $order->state,
            $order->payment_state,
            static::formatAmount($order->total_amount, $order->currency),
            $order->currency,
            optional($order->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    public static function orderStateOptions(): array
    {
        return collect(OrderState::cases())->mapWithKeys(fn (OrderState $state) => [$state->value => match ($state->value) {
            'draft' => 'Taslak',
            'pending_payment' => 'Odeme Bekleniyor',
            'paid' => 'Odendi',
            'assigned' => 'Atandi',
            'picked_up' => 'Alindi',
            'delivered' => 'Teslim Edildi',
            'closed' => 'Kapatildi',
            'cancelled' => 'Iptal',
            'failed' => 'Basarisiz',
            default => $state->value,
        }])->all();
    }

    public static function manualTransitionFormSchema(): array
    {
        return [
            Forms\Components\Select::make('to_state')
                ->label('Yeni Durum')
                ->options(static::orderStateOptions())
                ->required(),
            Forms\Components\Textarea::make('reason')
                ->label('Neden')
                ->required()
                ->minLength(5)
                ->rows(3)
                ->placeholder('Durum degisikligi nedeni zorunludur'),
        ];
    }

    public static function handleManualTransition(Order $record, array $data): void
    {
        try {
            app(OrderStateTransitionService::class)->transition(
                order: $record,
                toState: OrderState::from((string) $data['to_state']),
                actorType: 'admin',
                actorId: auth()->id(),
                reason: (string) $data['reason'],
                metadata: ['source' => 'filament_manual_override']
            );

            Notification::make()
                ->title('Siparis durumu guncellendi')
                ->success()
                ->send();
        } catch (InvalidOrderTransitionException $e) {
            Notification::make()
                ->title('Gecersiz durum degisikligi')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } catch (Throwable $e) {
            Notification::make()
                ->title('Durum guncellenemedi')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public static function manualTransitionTableAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('manual_transition')
            ->label('Durum Degistir')
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->form(static::manualTransitionFormSchema())
            ->action(function (Order $record, array $data): void {
                static::handleManualTransition($record, $data);
            });
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('state', ['pending_payment', 'paid', 'assigned'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function emptyStateHeading(): string
    {
        return 'Henüz sipariş kaydı yok';
    }

    public static function emptyStateDescription(): string
    {
        return 'Siparişler checkout, müşteri paneli veya operasyon akışlarından oluşur. Liste boşsa tekliften siparişe dönüşüm ve ödeme adımlarını kontrol edin.';
    }
}
