<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourierResource\RelationManagers\CourierDocumentsRelationManager;
use App\Filament\Resources\CourierResource\Pages;
use App\Models\Courier;
use App\Support\BulkActionRateLimiter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourierResource extends Resource
{
    protected static ?string $model = Courier::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Kuryeler';

    protected static ?string $modelLabel = 'Kurye';

    protected static ?string $pluralModelLabel = 'Kuryeler';

    protected static ?string $navigationGroup = 'Operasyon';

    protected static ?int $navigationSort = 43;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Kişisel Bilgiler')
                ->icon('heroicon-o-user')
                ->schema([
                    Forms\Components\TextInput::make('full_name')
                        ->label('Ad Soyad')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('Telefon')
                        ->required()
                        ->maxLength(30),
                    Forms\Components\TextInput::make('email')
                        ->label('E-posta')
                        ->email()
                        ->maxLength(255),
                ])->columns(2),

            Forms\Components\Section::make('Araç & Durum')
                ->icon('heroicon-o-truck')
                ->schema([
                    Forms\Components\TextInput::make('vehicle_type')
                        ->label('Araç Tipi')
                        ->maxLength(40),
                    Forms\Components\Select::make('status')
                        ->label('Durum')
                        ->required()
                        ->options([
                            'pending' => '⏳ Başvuru Bekliyor',
                            'approved' => '✅ Onaylandı',
                            'rejected' => '❌ Reddedildi',
                            'suspended' => '⛔ Askıya Alındı',
                        ]),
                    Forms\Components\Textarea::make('application_notes')
                        ->label('Başvuru Notları')
                        ->rows(3),
                    Forms\Components\DateTimePicker::make('approved_at')
                        ->label('Onay Tarihi'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Ad Soyad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('vehicle_type')
                    ->label('Araç Tipi')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'suspended' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Bekliyor',
                        'approved' => 'Onaylı',
                        'rejected' => 'Reddedildi',
                        'suspended' => 'Askıda',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Onay Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Başvuru Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->emptyStateHeading(static::emptyStateHeading())
            ->emptyStateDescription(static::emptyStateDescription())
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Kurye Ekle'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Bekliyor',
                        'approved' => 'Onaylı',
                        'rejected' => 'Reddedildi',
                        'suspended' => 'Askıda',
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
                    Tables\Actions\BulkAction::make('update_status')
                        ->label('Durum Güncelle')
                        ->icon('heroicon-o-check-circle')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Yeni Durum')
                                ->required()
                                ->options([
                                    'pending' => 'Bekliyor',
                                    'approved' => 'Onaylı',
                                    'rejected' => 'Reddedildi',
                                    'suspended' => 'Askıda',
                                ]),
                        ])
                        ->action(function ($records, array $data): void {
                            if (! BulkActionRateLimiter::enforce('couriers.update-status')) {
                                return;
                            }

                            $records->each(fn (Courier $courier) => $courier->update([
                                'status' => $data['status'],
                            ]));
                        }),
                    Tables\Actions\DeleteBulkAction::make()->label('Sil'),
                    Tables\Actions\RestoreBulkAction::make()->label('Geri Yükle'),
                    Tables\Actions\ForceDeleteBulkAction::make()->label('Kalıcı Sil'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CourierDocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCouriers::route('/'),
            'create' => Pages\CreateCourier::route('/create'),
            'edit' => Pages\EditCourier::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'phone', 'email', 'vehicle_type'];
    }

    public static function emptyStateHeading(): string
    {
        return 'Henüz kurye kaydı yok';
    }

    public static function emptyStateDescription(): string
    {
        return 'Kurye kayıtları başvuru formundan, API başvurusundan veya admin tarafından manuel oluşturulabilir.';
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
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
