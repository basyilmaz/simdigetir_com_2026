<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Filament\Resources\PaymentTransactionResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentTransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'paymentTransactions';

    protected static ?string $recordTitleAttribute = 'provider_reference';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider')
                    ->label('Sağlayıcı')
                    ->badge(),
                Tables\Columns\TextColumn::make('provider_reference')
                    ->label('Referans')
                    ->copyable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'succeeded' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Tutar')
                    ->formatStateUsing(fn ($state, $record): string => PaymentTransactionResource::formatAmount($state, $record->currency)),
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('İşlem / Kayıt')
                    ->state(fn ($record) => $record->processed_at ?? $record->created_at)
                    ->dateTime('d.m.Y H:i'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Kayıt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
