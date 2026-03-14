<?php

namespace Modules\Landing\Filament\Resources\LandingPageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Landing\Filament\Resources\LandingPageResource;

class CreateLandingPage extends CreateRecord
{
    protected static string $resource = LandingPageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return LandingPageResource::normalizeMetaEditorFields($data);
    }
}
