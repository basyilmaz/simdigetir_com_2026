<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupportTicketResource\RelationManagers\MessagesRelationManager;
use App\Filament\Resources\SupportTicketResource\Pages;
use App\Models\SupportTicket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?string $navigationLabel = 'Destek Talepleri';

    protected static ?string $modelLabel = 'Destek Talebi';

    protected static ?string $pluralModelLabel = 'Destek Talepleri';

    protected static ?string $navigationGroup = 'Operasyon';

    protected static ?int $navigationSort = 44;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Talep Bilgileri')
                ->icon('heroicon-o-ticket')
                ->schema([
                    Forms\Components\TextInput::make('ticket_no')
                        ->label('Talep No')
                        ->disabled(),
                    Forms\Components\Select::make('status')
                        ->label('Durum')
                        ->required()
                        ->options([
                            'open' => '🟢 Açık',
                            'pending' => '⏳ Bekliyor',
                            'resolved' => '✅ Çözüldü',
                            'closed' => '🔒 Kapatıldı',
                        ]),
                    Forms\Components\Select::make('priority')
                        ->label('Öncelik')
                        ->required()
                        ->options([
                            'low' => '🟢 Düşük',
                            'normal' => '🔵 Normal',
                            'high' => '🟠 Yüksek',
                            'critical' => '🔴 Kritik',
                        ]),
                    Forms\Components\TextInput::make('subject')
                        ->label('Konu')
                        ->required()
                        ->maxLength(255),
                ])->columns(2),

            Forms\Components\Section::make('Detay')
                ->schema([
                    Forms\Components\Textarea::make('description')
                        ->label('Açıklama')
                        ->rows(4)
                        ->required()
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('İlişkili Kayıtlar')
                ->icon('heroicon-o-link')
                ->schema([
                    Forms\Components\TextInput::make('order_id')
                        ->label('Sipariş ID')
                        ->numeric(),
                    Forms\Components\TextInput::make('customer_id')
                        ->label('Müşteri ID')
                        ->numeric(),
                    Forms\Components\TextInput::make('courier_id')
                        ->label('Kurye ID')
                        ->numeric(),
                    Forms\Components\TextInput::make('assigned_to')
                        ->label('Atanan Kişi ID')
                        ->numeric(),
                ])->columns(2)
                ->collapsed(),

            Forms\Components\Section::make('Çözüm Bilgileri')
                ->schema([
                    Forms\Components\DateTimePicker::make('resolved_at')
                        ->label('Çözüm Tarihi'),
                    Forms\Components\DateTimePicker::make('closed_at')
                        ->label('Kapatılma Tarihi'),
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
                Tables\Columns\TextColumn::make('ticket_no')
                    ->label('Talep No')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Konu')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'success',
                        'pending' => 'warning',
                        'resolved' => 'info',
                        'closed' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => 'Açık',
                        'pending' => 'Bekliyor',
                        'resolved' => 'Çözüldü',
                        'closed' => 'Kapatıldı',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Öncelik')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'normal' => 'info',
                        'high' => 'warning',
                        'critical' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Düşük',
                        'normal' => 'Normal',
                        'high' => 'Yüksek',
                        'critical' => 'Kritik',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Sipariş')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->emptyStateHeading(static::emptyStateHeading())
            ->emptyStateDescription(static::emptyStateDescription())
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Talep Oluştur'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'open' => 'Açık',
                        'pending' => 'Bekliyor',
                        'resolved' => 'Çözüldü',
                        'closed' => 'Kapatıldı',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Öncelik')
                    ->options([
                        'low' => 'Düşük',
                        'normal' => 'Normal',
                        'high' => 'Yüksek',
                        'critical' => 'Kritik',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Görüntüle'),
                Tables\Actions\EditAction::make()->label('Düzenle'),
                Tables\Actions\DeleteAction::make()->label('Sil'),
                Tables\Actions\RestoreAction::make()->label('Geri Yükle'),
                Tables\Actions\ForceDeleteAction::make()->label('Kalıcı Sil'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Sil'),
                    Tables\Actions\RestoreBulkAction::make()->label('Geri Yükle'),
                    Tables\Actions\ForceDeleteBulkAction::make()->label('Kalıcı Sil'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupportTickets::route('/'),
            'create' => Pages\CreateSupportTicket::route('/create'),
            'edit' => Pages\EditSupportTicket::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            MessagesRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['ticket_no', 'subject', 'description'];
    }

    public static function emptyStateHeading(): string
    {
        return 'Henüz destek talebi yok';
    }

    public static function emptyStateDescription(): string
    {
        return 'Destek talepleri müşteri, kurye veya admin akışlarından oluşur. Gerekirse bu ekrandan manuel talep de açabilirsiniz.';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['open', 'pending'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
