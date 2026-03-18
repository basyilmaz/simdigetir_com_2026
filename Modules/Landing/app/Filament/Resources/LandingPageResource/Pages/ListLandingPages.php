<?php

namespace Modules\Landing\Filament\Resources\LandingPageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Landing\Filament\Resources\LandingPageResource;

class ListLandingPages extends ListRecords
{
    protected static string $resource = LandingPageResource::class;

    public function getSubheading(): ?string
    {
        return 'URL, SEO ve sayfa yayini bu ekrandan; section ve item detaylari ise ilgili kaynaklardan yonetilir.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
