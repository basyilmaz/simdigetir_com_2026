<?php

namespace App\Observers;

use App\Domain\Pricing\Services\PricingQuoteResolver;
use App\Models\PricingRule;
use Illuminate\Support\Facades\Cache;

class PricingRuleCacheObserver
{
    public function created(PricingRule $rule): void
    {
        $this->flush();
    }

    public function updated(PricingRule $rule): void
    {
        $this->flush();
    }

    public function deleted(PricingRule $rule): void
    {
        $this->flush();
    }

    private function flush(): void
    {
        Cache::forget(PricingQuoteResolver::ACTIVE_RULES_CACHE_KEY);
    }
}

