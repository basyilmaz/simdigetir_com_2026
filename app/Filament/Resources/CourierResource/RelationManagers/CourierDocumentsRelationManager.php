<?php

namespace App\Filament\Resources\CourierResource\RelationManagers;

use App\Models\CourierDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CourierDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $recordTitleAttribute = 'document_type';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('document_type')
                ->label('Belge Tipi')
                ->required()
                ->maxLength(40),
            Forms\Components\TextInput::make('file_url')
                ->label('Dosya URL')
                ->required()
                ->url()
                ->maxLength(2048),
            Forms\Components\Select::make('status')
                ->label('Durum')
                ->required()
                ->options([
                    'pending' => 'Bekliyor',
                    'approved' => 'Onaylandı',
                    'rejected' => 'Reddedildi',
                ])
                ->default('pending'),
            Forms\Components\Textarea::make('review_note')
                ->label('İnceleme Notu')
                ->rows(3)
                ->columnSpanFull(),
            Forms\Components\DateTimePicker::make('reviewed_at')
                ->label('İncelenme Tarihi'),
            Forms\Components\TextInput::make('reviewed_by')
                ->label('İnceleyen Kullanıcı ID')
                ->numeric()
                ->default(fn (): ?int => auth()->id()),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_type')
                    ->label('Belge Tipi')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Bekliyor',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('file_url')
                    ->label('Dosya')
                    ->url(fn (CourierDocument $record): string => $record->file_url, shouldOpenInNewTab: true)
                    ->limit(50)
                    ->tooltip(fn (CourierDocument $record): string => $record->file_url),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('İncelenme')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('reviewed_by')
                    ->label('İnceleyen')
                    ->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
