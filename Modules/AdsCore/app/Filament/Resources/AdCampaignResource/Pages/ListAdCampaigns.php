<?php

namespace Modules\AdsCore\Filament\Resources\AdCampaignResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\AdsCore\Filament\Resources\AdCampaignResource;

class ListAdCampaigns extends ListRecords
{
    protected static string $resource = AdCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
