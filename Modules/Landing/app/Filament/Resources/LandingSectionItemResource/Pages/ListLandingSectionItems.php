<?php

namespace Modules\Landing\Filament\Resources\LandingSectionItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Landing\Filament\Resources\LandingSectionItemResource;

class ListLandingSectionItems extends ListRecords
{
    protected static string $resource = LandingSectionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
