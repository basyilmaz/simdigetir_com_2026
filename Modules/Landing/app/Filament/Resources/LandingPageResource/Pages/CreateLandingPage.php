<?php

namespace Modules\Landing\Filament\Resources\LandingPageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Landing\Filament\Resources\LandingPageResource;

class CreateLandingPage extends CreateRecord
{
    protected static string $resource = LandingPageResource::class;

    public function getSubheading(): ?string
    {
        return 'Once temel URL ve SEO bilgisini tanimlayin. Ardindan section ve item yonetimiyle sayfa icerigini zenginlestirin.';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return LandingPageResource::normalizeMetaEditorFields($data);
    }
}
