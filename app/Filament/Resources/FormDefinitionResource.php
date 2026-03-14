<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormDefinitionResource\Pages;
use App\Models\FormDefinition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FormDefinitionResource extends Resource
{
    protected static ?string $model = FormDefinition::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Form Tanımları';

    protected static ?string $modelLabel = 'Form Tanımı';

    protected static ?string $pluralModelLabel = 'Form Tanımları';

    protected static ?string $navigationGroup = 'Büyüme';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Form Bilgileri')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\TextInput::make('key')
                        ->label('Anahtar')
                        ->required()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('title')
                        ->label('Başlık')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->label('Açıklama')
                        ->rows(2),
                    Forms\Components\Select::make('target_type')
                        ->label('Hedef Tip')
                        ->options([
                            'store_only' => 'Sadece Kaydet',
                            'lead' => 'Talep Modülü',
                        ])
                        ->default('store_only')
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('Ayarlar')
                ->icon('heroicon-o-cog-6-tooth')
                ->schema([
                    Forms\Components\TextInput::make('success_message')
                        ->label('Başarı Mesajı')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('rate_limit_per_minute')
                        ->label('Dakikadaki İstek Limiti')
                        ->numeric()
                        ->default(10)
                        ->required(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true)
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('Şema')
                ->schema([
                    Forms\Components\Textarea::make('schema_json')
                        ->label('Şema (JSON)')
                        ->rows(10)
                        ->helperText('Format: {"fields":[{"name":"name","type":"string","required":true,"max":255}]}')
                        ->default('{"fields":[]}')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Anahtar')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable(),
                Tables\Columns\TextColumn::make('target_type')
                    ->label('Hedef')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'lead' => 'success',
                        'store_only' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'lead' => 'Talep',
                        'store_only' => 'Kaydet',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('rate_limit_per_minute')
                    ->label('Limit/dk'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
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

    public static function mutateFormDataBeforeFill(array $data): array
    {
        $data['schema_json'] = json_encode($data['schema'] ?? ['fields' => []], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $decoded = json_decode((string) ($data['schema_json'] ?? '{}'), true);
        $data['schema'] = is_array($decoded) ? $decoded : ['fields' => []];
        unset($data['schema_json']);
        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFormDefinitions::route('/'),
            'create' => Pages\CreateFormDefinition::route('/create'),
            'edit' => Pages\EditFormDefinition::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['key', 'title', 'description'];
    }
}
