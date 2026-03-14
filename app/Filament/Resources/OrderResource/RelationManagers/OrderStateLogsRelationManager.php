<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrderStateLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'stateLogs';

    protected static ?string $recordTitleAttribute = 'to_state';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime('d.m.Y H:i:s')->sortable(),
                Tables\Columns\TextColumn::make('from_state')->label('Önceki Durum')->badge(),
                Tables\Columns\TextColumn::make('to_state')->label('Yeni Durum')->badge(),
                Tables\Columns\TextColumn::make('actor_type')->badge(),
                Tables\Columns\TextColumn::make('actor_id'),
                Tables\Columns\TextColumn::make('reason')->limit(50)->wrap(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
