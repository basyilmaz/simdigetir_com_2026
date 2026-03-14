<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LegalDocumentResource\Pages;
use App\Models\LegalDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LegalDocumentResource extends Resource
{
    protected static ?string $model = LegalDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'Yasal Belgeler';

    protected static ?string $modelLabel = 'Yasal Belge';

    protected static ?string $pluralModelLabel = 'Yasal Belgeler';

    protected static ?string $navigationGroup = 'Büyüme';

    protected static ?int $navigationSort = 31;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Belge Bilgileri')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\TextInput::make('slug')
                        ->label('URL Slug')
                        ->required()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('title')
                        ->label('Başlık')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('summary')
                        ->label('Özet')
                        ->rows(3),
                    Forms\Components\Toggle::make('is_published')
                        ->label('Yayında')
                        ->default(false),
                ])->columns(2),

            Forms\Components\Section::make('İçerik')
                ->schema([
                    Forms\Components\RichEditor::make('content')
                        ->label('İçerik')
                        ->required()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable(),
                Tables\Columns\TextColumn::make('version')
                    ->label('Versiyon')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Yayında')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Yayın Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
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
            'index' => Pages\ListLegalDocuments::route('/'),
            'create' => Pages\CreateLegalDocument::route('/create'),
            'edit' => Pages\EditLegalDocument::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['slug', 'title', 'summary'];
    }
}
