<?php

namespace Modules\AdsCore\Filament\Resources\AdConnectionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\AdsCore\Filament\Resources\AdConnectionResource;

class ListAdConnections extends ListRecords
{
    protected static string $resource = AdConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
