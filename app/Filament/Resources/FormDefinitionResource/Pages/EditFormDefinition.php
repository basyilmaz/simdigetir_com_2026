<?php

namespace App\Filament\Resources\FormDefinitionResource\Pages;

use App\Filament\Resources\FormDefinitionResource;
use Filament\Resources\Pages\EditRecord;

class EditFormDefinition extends EditRecord
{
    protected static string $resource = FormDefinitionResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return FormDefinitionResource::mutateFormDataBeforeFill($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return FormDefinitionResource::mutateFormDataBeforeSave($data);
    }
}

