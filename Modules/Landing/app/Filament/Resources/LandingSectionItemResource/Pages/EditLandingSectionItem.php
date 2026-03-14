<?php

namespace Modules\Landing\Filament\Resources\LandingSectionItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Landing\Filament\Resources\LandingSectionItemResource;

class EditLandingSectionItem extends EditRecord
{
    protected static string $resource = LandingSectionItemResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return LandingSectionItemResource::injectTemplateFieldsFromPayload($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return LandingSectionItemResource::buildPayloadFromFormData($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
