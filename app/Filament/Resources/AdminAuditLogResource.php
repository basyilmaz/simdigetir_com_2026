<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminAuditLogResource\Pages;
use App\Models\AdminAuditLog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdminAuditLogResource extends Resource
{
    protected static ?string $model = AdminAuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Audit Log';

    protected static ?string $modelLabel = 'Audit Kaydı';

    protected static ?string $pluralModelLabel = 'Audit Kayıtları';

    protected static ?string $navigationGroup = 'Operasyon';

    protected static ?int $navigationSort = 60;

    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('event')
                    ->label('Olay')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('Model')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('auditable_id')
                    ->label('Kayıt ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('changedBy.email')
                    ->label('Değiştiren')
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('request_ip')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('request_url')
                    ->label('URL')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label('Olay')
                    ->options([
                        'created' => 'Oluşturma',
                        'updated' => 'Güncelleme',
                        'deleted' => 'Silme',
                    ]),
                Tables\Filters\Filter::make('auditable_type')
                    ->label('Model')
                    ->form([
                        Forms\Components\TextInput::make('model')
                            ->label('Model Sınıfı')
                            ->placeholder('Order, Lead, SupportTicket...'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $model = trim((string) ($data['model'] ?? ''));

                        if ($model === '') {
                            return $query;
                        }

                        return $query->where('auditable_type', 'like', '%'.$model.'%');
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Detay'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminAuditLogs::route('/'),
            'view' => Pages\ViewAdminAuditLog::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['event', 'auditable_type', 'auditable_id'];
    }
}
