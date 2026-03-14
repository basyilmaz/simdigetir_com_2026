<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SitemapEntryResource\Pages;
use App\Models\SitemapEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SitemapEntryResource extends Resource
{
    protected static ?string $model = SitemapEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'Site Haritası';

    protected static ?string $modelLabel = 'Site Haritası Kaydı';

    protected static ?string $pluralModelLabel = 'Site Haritası';

    protected static ?string $navigationGroup = 'Büyüme';

    protected static ?int $navigationSort = 32;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Sayfa Bilgileri')
                ->icon('heroicon-o-globe-alt')
                ->schema([
                    Forms\Components\TextInput::make('path')
                        ->label('URL Yolu')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('/hizmetler')
                        ->unique(ignoreRecord: true),
                    Forms\Components\Select::make('changefreq')
                        ->label('Değişim Sıklığı')
                        ->required()
                        ->options([
                            'always' => 'Sürekli',
                            'hourly' => 'Saatlik',
                            'daily' => 'Günlük',
                            'weekly' => 'Haftalık',
                            'monthly' => 'Aylık',
                            'yearly' => 'Yıllık',
                            'never' => 'Hiçbir Zaman',
                        ])
                        ->default('monthly'),
                    Forms\Components\TextInput::make('priority')
                        ->label('Öncelik (0-1)')
                        ->numeric()
                        ->required()
                        ->default(0.5),
                    Forms\Components\DateTimePicker::make('lastmod_at')
                        ->label('Son Değişiklik Tarihi')
                        ->seconds(false),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true)
                        ->required(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('path')
                    ->label('URL Yolu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('changefreq')
                    ->label('Sıklık')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'always' => 'Sürekli',
                        'hourly' => 'Saatlik',
                        'daily' => 'Günlük',
                        'weekly' => 'Haftalık',
                        'monthly' => 'Aylık',
                        'yearly' => 'Yıllık',
                        'never' => 'Hiçbir Zaman',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Öncelik'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('lastmod_at')
                    ->label('Son Değişiklik')
                    ->dateTime('d.m.Y H:i'),
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
            'index' => Pages\ListSitemapEntries::route('/'),
            'create' => Pages\CreateSitemapEntry::route('/create'),
            'edit' => Pages\EditSitemapEntry::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['path', 'changefreq'];
    }
}
