<?php

namespace Modules\Landing\Filament\Resources\LandingPageSectionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Landing\Filament\Resources\LandingPageSectionResource;
use Modules\Landing\Services\LandingRevisionService;

class EditLandingPageSection extends EditRecord
{
    protected static string $resource = LandingPageSectionResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return LandingPageSectionResource::injectTemplateFieldsFromPayload($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        app(LandingRevisionService::class)->snapshotSection($this->record, 'edit_before_save', auth()->id());
        return LandingPageSectionResource::buildPayloadFromFormData($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
