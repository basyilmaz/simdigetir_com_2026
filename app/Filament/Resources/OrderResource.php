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

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Siparişler';

    protected static ?string $modelLabel = 'Sipariş';

    protected static ?string $pluralModelLabel = 'Siparişler';

    protected static ?string $navigationGroup = 'Operasyon';

    protected static ?int $navigationSort = 41;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Sipariş Bilgileri')
                ->icon('heroicon-o-shopping-bag')
                ->schema([
                    Forms\Components\TextInput::make('order_no')
                        ->label('Sipariş No')
                        ->disabled(),
                    Forms\Components\TextInput::make('state')
                        ->label('Durum')
                        ->disabled(),
                    Forms\Components\TextInput::make('payment_state')
                        ->label('Ödeme Durumu')
                        ->disabled(),
                    Forms\Components\TextInput::make('customer_id')
                        ->label('Müşteri ID')
                        ->disabled(),
                ])->columns(2),

            Forms\Components\Section::make('Alış Noktası')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Forms\Components\TextInput::make('pickup_name')
                        ->label('Gönderici Adı')
                        ->disabled(),
                    Forms\Components\TextInput::make('pickup_phone')
                        ->label('Gönderici Telefon')
                        ->disabled(),
                    Forms\Components\Textarea::make('pickup_address')
                        ->label('Alış Adresi')
                        ->disabled()
                        ->rows(2),
                ])->columns(2),

            Forms\Components\Section::make('Teslimat Noktası')
                ->icon('heroicon-o-flag')
                ->schema([
                    Forms\Components\TextInput::make('dropoff_name')
                        ->label('Alıcı Adı')
                        ->disabled(),
                    Forms\Components\TextInput::make('dropoff_phone')
                        ->label('Alıcı Telefon')
                        ->disabled(),
                    Forms\Components\Textarea::make('dropoff_address')
                        ->label('Teslimat Adresi')
                        ->disabled()
                        ->rows(2),
                ])->columns(2),

            Forms\Components\Section::make('Ücret Bilgileri')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Forms\Components\TextInput::make('total_amount')
                        ->label('Toplam Tutar')
                        ->disabled(),
                    Forms\Components\TextInput::make('currency')
                        ->label('Para Birimi')
                        ->disabled(),
                    Forms\Components\KeyValue::make('price_breakdown')
                        ->label('Fiyat Detayı')
                        ->disabled(),
                ])->columns(2),

            Forms\Components\Section::make('Tarih Bilgileri')
                ->schema([
                    Forms\Components\DateTimePicker::make('created_at')
                        ->label('Oluşturulma Tarihi')
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
                    ->label('Sipariş No')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Müşteri')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('customer.email')
                    ->label('Müşteri E-posta')
                    ->searchable()
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
                        'pending_payment' => 'Ödeme Bekleniyor',
                        'paid' => 'Ödendi',
                        'assigned' => 'Atandı',
                        'picked_up' => 'Alındı',
                        'delivered' => 'Teslim Edildi',
                        'closed' => 'Kapatıldı',
                        'cancelled' => 'İptal',
                        'failed' => 'Başarısız',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('payment_state')
                    ->label('Ödeme')
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
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Tutar')
                    ->formatStateUsing(fn ($state) => number_format((int) $state) . ' ₺'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentTransactions_count')
                    ->label('Ödeme Kaydı')
                    ->counts('paymentTransactions')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('state')
                    ->label('Sipariş Durumu')
                    ->options(collect(OrderState::cases())->mapWithKeys(fn (OrderState $state) => [$state->value => match ($state->value) {
                        'draft' => 'Taslak',
                        'pending_payment' => 'Ödeme Bekleniyor',
                        'paid' => 'Ödendi',
                        'assigned' => 'Atandı',
                        'picked_up' => 'Alındı',
                        'delivered' => 'Teslim Edildi',
                        'closed' => 'Kapatıldı',
                        'cancelled' => 'İptal',
                        'failed' => 'Başarısız',
                        default => $state->value,
                    }])->all()),
                Tables\Filters\SelectFilter::make('payment_state')
                    ->label('Ödeme Durumu')
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
                        $rows = Order::query()
                            ->latest('id')
                            ->cursor()
                            ->map(fn (Order $order): array => [
                                $order->id,
                                $order->order_no,
                                $order->state,
                                $order->payment_state,
                                $order->total_amount,
                                $order->currency,
                                optional($order->created_at)->format('Y-m-d H:i:s'),
                            ]);

                        return CsvExporter::download(
                            filename: 'orders-' . now()->format('Ymd-His') . '.csv',
                            headers: ['ID', 'Sipariş No', 'Durum', 'Ödeme Durumu', 'Tutar', 'Para Birimi', 'Tarih'],
                            rows: $rows
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Görüntüle'),
                Tables\Actions\Action::make('payments')
                    ->label('Ödemeler')
                    ->icon('heroicon-o-credit-card')
                    ->color('info')
                    ->url(fn (Order $record): string => PaymentTransactionResource::getUrl('index', [
                        'tableSearch' => (string) $record->id,
                    ]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('manual_transition')
                    ->label('Durum Değiştir')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('to_state')
                            ->label('Yeni Durum')
                            ->options(collect(OrderState::cases())->mapWithKeys(fn (OrderState $state) => [$state->value => match ($state->value) {
                                'draft' => 'Taslak',
                                'pending_payment' => 'Ödeme Bekleniyor',
                                'paid' => 'Ödendi',
                                'assigned' => 'Atandı',
                                'picked_up' => 'Alındı',
                                'delivered' => 'Teslim Edildi',
                                'closed' => 'Kapatıldı',
                                'cancelled' => 'İptal',
                                'failed' => 'Başarısız',
                                default => $state->value,
                            }])->all())
                            ->required(),
                        Forms\Components\Textarea::make('reason')
                            ->label('Neden')
                            ->required()
                            ->minLength(5)
                            ->rows(3)
                            ->placeholder('Durum değişikliği nedeni zorunludur'),
                    ])
                    ->action(function (Order $record, array $data): void {
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
                                ->title('Sipariş durumu güncellendi')
                                ->success()
                                ->send();
                        } catch (InvalidOrderTransitionException $e) {
                            Notification::make()
                                ->title('Geçersiz durum değişikliği')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('state', ['pending_payment', 'paid', 'assigned'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }
}
