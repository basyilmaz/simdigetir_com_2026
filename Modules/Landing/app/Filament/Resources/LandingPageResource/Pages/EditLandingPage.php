<?php

namespace Modules\Landing\Filament\Resources\LandingPageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Landing\Filament\Resources\LandingPageResource;

class EditLandingPage extends EditRecord
{
    protected static string $resource = LandingPageResource::class;

    public function getSubheading(): ?string
    {
        return 'Sayfa kimligi, SEO sinyalleri ve onizleme akisini ayni yerden guncelleyebilirsiniz.';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return LandingPageResource::fillMetaEditorFields($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return LandingPageResource::normalizeMetaEditorFields($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Siteyi Önizle')
                ->icon('heroicon-o-eye')
                ->url(fn (): string => LandingPageResource::previewUrl($this->getRecord()))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }
}
