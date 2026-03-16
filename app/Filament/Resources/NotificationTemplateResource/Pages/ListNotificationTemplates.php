<?php

namespace App\Filament\Resources\NotificationTemplateResource\Pages;

use App\Filament\Resources\NotificationTemplateResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListNotificationTemplates extends ListRecords
{
    protected static string $resource = NotificationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('bootstrap_catalog')
                ->label('Varsayılan Şablonları Hazırla')
                ->icon('heroicon-o-bolt')
                ->color('gray')
                ->action(function (): void {
                    $count = NotificationTemplateResource::bootstrapCatalogTemplates();

                    Notification::make()
                        ->title('Şablonlar hazırlandı')
                        ->body("Katalogdan {$count} adet varsayılan bildirim şablonu oluşturuldu veya güncellendi.")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
