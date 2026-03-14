<?php

namespace Modules\Landing\Filament\Resources\LandingPageSectionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Landing\Filament\Resources\LandingPageSectionResource;

class CreateLandingPageSection extends CreateRecord
{
    protected static string $resource = LandingPageSectionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return LandingPageSectionResource::buildPayloadFromFormData($data);
    }
}
