<?php

namespace App\Filament\Resources\PricingRuleResource\Pages;

use App\Filament\Resources\PricingRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPricingRule extends EditRecord
{
    protected static string $resource = PricingRuleResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return PricingRuleResource::mutateFormDataBeforeFill($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return PricingRuleResource::mutateFormDataBeforeSave($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

