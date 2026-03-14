<?php

namespace Modules\AdsCore\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\AdsCore\Filament\Resources\AdCampaignResource\Pages;
use Modules\AdsCore\Models\AdCampaign;
use Modules\AdsCore\Models\AdConnection;

class AdCampaignResource extends Resource
{
    protected static ?string $model = AdCampaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Kampanyalar';

    protected static ?string $modelLabel = 'Reklam Kampanyası';

    protected static ?string $pluralModelLabel = 'Kampanyalar';

    protected static ?string $navigationGroup = 'Reklam Platformu';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('ad_connection_id')
                ->label('Bağlantı')
                ->options(fn () => AdConnection::query()->pluck('name', 'id')->all())
                ->required(),
            Forms\Components\Select::make('platform')
                ->label('Platform')
                ->options([
                    'google' => 'Google',
                    'meta' => 'Meta',
                    'mock' => 'Mock',
                ])
                ->required(),
            Forms\Components\TextInput::make('name')
                ->label('Kampanya Adı')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('objective')
                ->label('Hedef')
                ->maxLength(64),
            Forms\Components\TextInput::make('daily_budget')
                ->label('Günlük Bütçe')
                ->numeric()
                ->minValue(0),
            Forms\Components\TextInput::make('currency')
                ->label('Para Birimi')
                ->default('TRY')
                ->maxLength(8),
            Forms\Components\Select::make('status')
                ->label('Durum')
                ->options([
                    'draft' => 'Taslak',
                    'active' => 'Aktif',
                    'paused' => 'Duraklatıldı',
                    'error' => 'Hata',
                ])
                ->default('draft')
                ->required(),
            Forms\Components\TextInput::make('external_campaign_id')
                ->label('Harici Kampanya ID')
                ->maxLength(255),
            Forms\Components\KeyValue::make('targeting')
                ->label('Hedefleme')
                ->columnSpanFull(),
            Forms\Components\KeyValue::make('meta')
                ->label('Meta Veriler')
                ->columnSpanFull(),
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
                    ->label('Kampanya')
                    ->searchable(),
                Tables\Columns\TextColumn::make('platform')
                    ->label('Platform')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active' => 'success',
                        'paused' => 'warning',
                        'error' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Taslak',
                        'active' => 'Aktif',
                        'paused' => 'Duraklatıldı',
                        'error' => 'Hata',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('daily_budget')
                    ->label('Günlük Bütçe')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('external_campaign_id')
                    ->label('Harici ID')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Düzenle'),
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
            'index' => Pages\ListAdCampaigns::route('/'),
            'create' => Pages\CreateAdCampaign::route('/create'),
            'edit' => Pages\EditAdCampaign::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'platform', 'external_campaign_id'];
    }
}
