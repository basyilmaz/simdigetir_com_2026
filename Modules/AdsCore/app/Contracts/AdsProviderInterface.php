<?php

namespace Modules\AdsCore\Contracts;

use Modules\AdsCore\Models\AdCampaign;
use Modules\AdsCore\Models\AdConnection;
use Modules\AdsCore\Models\AdConversion;

interface AdsProviderInterface
{
    public function platform(): string;

    public function connect(AdConnection $connection): array;

    public function refreshToken(AdConnection $connection): array;

    public function createCampaign(AdCampaign $campaign): array;

    public function updateCampaign(AdCampaign $campaign): array;

    public function pauseCampaign(AdCampaign $campaign): array;

    public function pushConversion(AdConversion $conversion): array;

    public function fetchInsights(array $filters = []): array;
}
