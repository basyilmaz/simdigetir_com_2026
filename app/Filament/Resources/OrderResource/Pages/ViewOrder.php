<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manual_transition')
                ->label('Durum Degistir')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form(OrderResource::manualTransitionFormSchema())
                ->action(function (array $data): void {
                    OrderResource::handleManualTransition($this->record, $data);
                }),
        ];
    }
}
