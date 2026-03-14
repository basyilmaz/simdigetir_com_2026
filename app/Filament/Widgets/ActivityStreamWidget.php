<?php

namespace App\Filament\Widgets;

use App\Models\AdminAuditLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActivityStreamWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Canlı Aktivite Akışı';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user?->can('viewAny', AdminAuditLog::class) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->poll('30s')
            ->query(
                AdminAuditLog::query()->latest('id')->limit(12)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Zaman')
                    ->dateTime('d.m.Y H:i:s')
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
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                Tables\Columns\TextColumn::make('auditable_id')
                    ->label('Kayıt'),
                Tables\Columns\TextColumn::make('changedBy.email')
                    ->label('Kullanıcı')
                    ->placeholder('Sistem'),
            ])
            ->paginated(false);
    }
}
