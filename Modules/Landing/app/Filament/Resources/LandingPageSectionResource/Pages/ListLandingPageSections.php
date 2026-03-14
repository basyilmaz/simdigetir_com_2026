<?php

namespace Modules\Landing\Filament\Resources\LandingPageSectionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Landing\Filament\Resources\LandingPageSectionResource;

class ListLandingPageSections extends ListRecords
{
    protected static string $resource = LandingPageSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
