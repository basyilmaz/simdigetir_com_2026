<?php

namespace Modules\AdsCore\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\AdsCore\Filament\Resources\AdConnectionResource\Pages;
use Modules\AdsCore\Models\AdConnection;

class AdConnectionResource extends Resource
{
    protected static ?string $model = AdConnection::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationLabel = 'Baglantilar';

    protected static ?string $modelLabel = 'Reklam Baglantisi';

    protected static ?string $pluralModelLabel = 'Baglantilar';

    protected static ?string $navigationGroup = 'Reklam Platformu';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Baglanti Bilgileri')
                ->icon('heroicon-o-link')
                ->schema([
                    Forms\Components\Select::make('platform')
                        ->label('Platform')
                        ->options([
                            'google' => 'Google',
                            'meta' => 'Meta',
                            'mock' => 'Mock (Test)',
                        ])
                        ->default('meta')
                        ->live()
                        ->required(),
                    Forms\Components\TextInput::make('name')
                        ->label('Baglanti Adi')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('external_account_id')
                        ->label('Harici Hesap ID')
                        ->maxLength(255),
                    Forms\Components\Select::make('status')
                        ->label('Durum')
                        ->options([
                            'draft' => 'Taslak',
                            'connected' => 'Bagli',
                            'error' => 'Hata',
                        ])
                        ->default('draft')
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('Kimlik ve Yetki Bilgileri')
                ->schema([
                    Forms\Components\TextInput::make('access_token')
                        ->label('Access Token')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->helperText('Meta baglantisinda bos birakirsan mevcut token korunur.'),
                    Forms\Components\TextInput::make('refresh_token')
                        ->label('Refresh Token')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn (?string $state): bool => filled($state)),
                    Forms\Components\DateTimePicker::make('token_expires_at')
                        ->label('Token Gecerlilik Bitisi')
                        ->seconds(false),
                ])->columns(2),

            Forms\Components\Section::make('Meta CAPI Hizli Kurulum')
                ->visible(fn (Get $get): bool => strtolower(trim((string) $get('platform'))) === 'meta')
                ->schema([
                    Forms\Components\Placeholder::make('meta_setup_note')
                        ->label('Bilgi')
                        ->content('Bu alanlar kaydedildiginde Meta lead donusumleri admin panelden yonetilir.'),
                    Forms\Components\Toggle::make('meta.capi_enabled')
                        ->label('Meta CAPI aktif')
                        ->default(true)
                        ->inline(false),
                    Forms\Components\TextInput::make('meta.pixel_id')
                        ->label('Meta Pixel ID')
                        ->placeholder('1657531168735846')
                        ->helperText('Sadece rakam girin.')
                        ->dehydrateStateUsing(function (?string $state): ?string {
                            if ($state === null) {
                                return null;
                            }

                            return preg_replace('/\\D+/', '', $state) ?: null;
                        })
                        ->required(fn (Get $get): bool => $get('status') === 'connected'),
                    Forms\Components\TextInput::make('meta.test_event_code')
                        ->label('Test Event Code')
                        ->placeholder('TEST12345')
                        ->helperText('Canli oncesi test gonderimleri icin opsiyonel.'),
                    Forms\Components\Toggle::make('meta.auto_push')
                        ->label('Lead donusumlerini otomatik gonder')
                        ->default(true)
                        ->inline(false),
                    Forms\Components\Select::make('meta.auto_push_mode')
                        ->label('Auto Push Modu')
                        ->options([
                            'sync' => 'Anlik (sync)',
                            'queue' => 'Kuyruk (queue)',
                        ])
                        ->default('sync')
                        ->visible(fn (Get $get): bool => (bool) $get('meta.auto_push')),
                    Forms\Components\CheckboxList::make('meta.auto_push_platforms')
                        ->label('Auto Push Platformlari')
                        ->options([
                            'meta' => 'Meta',
                            'google' => 'Google',
                            'mock' => 'Mock',
                        ])
                        ->default(['meta'])
                        ->columns(3)
                        ->visible(fn (Get $get): bool => (bool) $get('meta.auto_push'))
                        ->helperText('Genelde sadece Meta secili kalmali.'),
                ])->columns(2),

            Forms\Components\Section::make('Gelismis Meta Veriler')
                ->visible(fn (Get $get): bool => strtolower(trim((string) $get('platform'))) !== 'meta')
                ->schema([
                    Forms\Components\KeyValue::make('meta')
                        ->label('Ek Bilgiler')
                        ->keyLabel('Anahtar')
                        ->valueLabel('Deger')
                        ->columnSpanFull(),
                ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('platform')
                    ->label('Platform')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Baglanti Adi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('external_account_id')
                    ->label('Harici Hesap ID')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('capi_enabled')
                    ->label('CAPI')
                    ->state(fn (AdConnection $record): bool => (bool) data_get($record->meta, 'capi_enabled', false))
                    ->boolean(),
                Tables\Columns\TextColumn::make('pixel_id')
                    ->label('Pixel ID')
                    ->state(fn (AdConnection $record): string => (string) data_get($record->meta, 'pixel_id', ''))
                    ->placeholder('-')
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('token_configured')
                    ->label('Token')
                    ->state(fn (AdConnection $record): bool => filled($record->access_token))
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'connected' => 'success',
                        'error' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Taslak',
                        'connected' => 'Bagli',
                        'error' => 'Hata',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Guncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()->label('Duzenle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Sil'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdConnections::route('/'),
            'create' => Pages\CreateAdConnection::route('/create'),
            'edit' => Pages\EditAdConnection::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'platform', 'external_account_id', 'status'];
    }
}
