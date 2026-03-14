<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettlementBatchResource\Pages;
use App\Models\SettlementBatch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettlementBatchResource extends Resource
{
    protected static ?string $model = SettlementBatch::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Mutabakat';

    protected static ?string $modelLabel = 'Mutabakat Grubu';

    protected static ?string $pluralModelLabel = 'Mutabakat';

    protected static ?string $navigationGroup = 'Finans';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Mutabakat Bilgileri')
                ->icon('heroicon-o-banknotes')
                ->schema([
                    Forms\Components\TextInput::make('batch_no')
                        ->label('Grup No')
                        ->disabled(),
                    Forms\Components\TextInput::make('status')
                        ->label('Durum')
                        ->disabled(),
                    Forms\Components\TextInput::make('net_amount')
                        ->label('Net Tutar')
                        ->disabled(),
                    Forms\Components\TextInput::make('currency')
                        ->label('Para Birimi')
                        ->disabled(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notlar')
                        ->disabled()
                        ->rows(3),
                    Forms\Components\DateTimePicker::make('closed_at')
                        ->label('Kapatılma Tarihi')
                        ->disabled(),
                    Forms\Components\DateTimePicker::make('paid_at')
                        ->label('Ödeme Tarihi')
                        ->disabled(),
                    Forms\Components\DateTimePicker::make('created_at')
                        ->label('Oluşturulma Tarihi')
                        ->disabled(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('batch_no')
                    ->label('Grup No')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'info',
                        'closed' => 'warning',
                        'paid' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => 'Açık',
                        'closed' => 'Kapatıldı',
                        'paid' => 'Ödendi',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Net Tutar')
                    ->formatStateUsing(fn ($state) => number_format((int) $state) . ' ₺'),
                Tables\Columns\TextColumn::make('closed_at')
                    ->label('Kapatılma')
                    ->dateTime('d.m.Y H:i')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Ödeme')
                    ->dateTime('d.m.Y H:i')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()->label('Görüntüle'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettlementBatches::route('/'),
            'view' => Pages\ViewSettlementBatch::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['batch_no', 'status'];
    }
}
