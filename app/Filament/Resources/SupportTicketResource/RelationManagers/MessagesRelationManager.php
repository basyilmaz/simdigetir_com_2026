<?php

namespace App\Filament\Resources\SupportTicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static ?string $recordTitleAttribute = 'message';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('author_type')
                ->label('Yazar Tipi')
                ->required()
                ->options([
                    'admin' => 'Admin',
                    'customer' => 'Müşteri',
                    'courier' => 'Kurye',
                    'system' => 'Sistem',
                ])
                ->default('admin'),
            Forms\Components\TextInput::make('author_id')
                ->label('Yazar ID')
                ->numeric()
                ->default(fn (): ?int => auth()->id()),
            Forms\Components\Toggle::make('is_internal')
                ->label('İç Not')
                ->default(false),
            Forms\Components\Textarea::make('message')
                ->label('Mesaj')
                ->required()
                ->rows(4)
                ->columnSpanFull(),
            Forms\Components\KeyValue::make('attachments')
                ->label('Ekler')
                ->keyLabel('Anahtar')
                ->valueLabel('Değer')
                ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('author_type')
                    ->label('Yazar')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'primary',
                        'customer' => 'success',
                        'courier' => 'warning',
                        'system' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('author_id')
                    ->label('Yazar ID')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_internal')
                    ->label('İç Not')
                    ->boolean(),
                Tables\Columns\TextColumn::make('message')
                    ->label('Mesaj')
                    ->limit(90)
                    ->wrap(),
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
