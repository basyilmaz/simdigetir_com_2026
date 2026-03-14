<?php

namespace Modules\AdsCore\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\AdsCore\Filament\Resources\AdConversionResource\Pages;
use Modules\AdsCore\Models\AdConversion;

class AdConversionResource extends Resource
{
    protected static ?string $model = AdConversion::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Dönüşümler';

    protected static ?string $modelLabel = 'Reklam Dönüşümü';

    protected static ?string $pluralModelLabel = 'Dönüşümler';

    protected static ?string $navigationGroup = 'Reklam Platformu';

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
                Tables\Columns\TextColumn::make('event_name')
                    ->label('Etkinlik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'sent' => 'info',
                        'confirmed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Bekliyor',
                        'sent' => 'Gönderildi',
                        'confirmed' => 'Onaylandı',
                        'failed' => 'Başarısız',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Değer')
                    ->formatStateUsing(fn ($state) => $state ? number_format((int) $state) . ' ₺' : '-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('external_id')
                    ->label('Harici ID')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pushed_at')
                    ->label('Gönderim')
                    ->since()
                    ->sortable(),
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
                        'sent' => 'Gönderildi',
                        'confirmed' => 'Onaylandı',
                        'failed' => 'Başarısız',
                    ]),
                Tables\Filters\SelectFilter::make('platform')
                    ->label('Platform')
                    ->options([
                        'google' => 'Google',
                        'meta' => 'Meta',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Görüntüle'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdConversions::route('/'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['event_name', 'platform', 'status', 'external_id'];
    }
}
