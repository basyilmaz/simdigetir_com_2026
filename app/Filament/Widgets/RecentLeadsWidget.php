<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\Leads\Models\Lead;

class RecentLeadsWidget extends BaseWidget
{
    protected static ?int $sort = -2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Son Gelen Talepler';

    public function table(Table $table): Table
    {
        return $table
            ->poll('30s')
            ->query(
                Lead::query()->latest()->limit(5)
            )
            ->emptyStateHeading('Talep Yok')
            ->emptyStateDescription('Henüz görüntülenecek yeni bir talep bulunmuyor.')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'corporate_quote' => 'primary',
                        'courier_apply' => 'success',
                        'contact' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'corporate_quote' => 'Kurumsal',
                        'courier_apply' => 'Kurye',
                        'contact' => 'İletişim',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->label('İsim'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->copyable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'contacted' => 'info',
                        'qualified' => 'success',
                        'lost' => 'danger',
                        'won' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Yeni',
                        'contacted' => 'İletişimde',
                        'qualified' => 'Uygun',
                        'lost' => 'Kayıp',
                        'won' => 'Kazanıldı',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
