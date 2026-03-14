<?php

namespace App\Filament\Resources\AdminAuditLogResource\Pages;

use App\Filament\Resources\AdminAuditLogResource;
use App\Models\AdminAuditLog;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewAdminAuditLog extends ViewRecord
{
    protected static string $resource = AdminAuditLogResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Kayıt Bilgileri')
                    ->schema([
                        TextEntry::make('id')
                            ->label('#'),
                        TextEntry::make('event')
                            ->label('Olay')
                            ->badge(),
                        TextEntry::make('auditable_type')
                            ->label('Model')
                            ->formatStateUsing(fn (string $state): string => class_basename($state)),
                        TextEntry::make('auditable_id')
                            ->label('Kayıt ID'),
                        TextEntry::make('changedBy.email')
                            ->label('Değiştiren')
                            ->placeholder('Sistem'),
                        TextEntry::make('request_ip')
                            ->label('IP')
                            ->placeholder('-'),
                        TextEntry::make('request_url')
                            ->label('URL')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('user_agent')
                            ->label('User Agent')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->label('Tarih')
                            ->dateTime('d.m.Y H:i:s'),
                    ])->columns(2),
                Section::make('Değişiklik Farkı')
                    ->schema([
                        TextEntry::make('old_values_pretty')
                            ->label('Eski Değerler')
                            ->state(fn (AdminAuditLog $record): string => static::toPrettyJson($record->old_values))
                            ->columnSpanFull(),
                        TextEntry::make('new_values_pretty')
                            ->label('Yeni Değerler')
                            ->state(fn (AdminAuditLog $record): string => static::toPrettyJson($record->new_values))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected static function toPrettyJson(?array $value): string
    {
        if (empty($value)) {
            return '{}';
        }

        return (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
