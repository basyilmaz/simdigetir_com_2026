<?php

namespace Modules\Landing\Filament\Resources\LandingSectionItemResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Landing\Filament\Resources\LandingSectionItemResource;

class CreateLandingSectionItem extends CreateRecord
{
    protected static string $resource = LandingSectionItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return LandingSectionItemResource::buildPayloadFromFormData($data);
    }
}
