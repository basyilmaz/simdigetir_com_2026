<?php

namespace Modules\AdsCore\Services;

use Illuminate\Contracts\Container\Container;
use Modules\AdsCore\Contracts\AdsProviderInterface;
use Modules\AdsCore\Services\Providers\MockAdsProvider;

class AdsProviderManager
{
    public function __construct(private readonly Container $container)
    {
    }

    public function forPlatform(?string $platform = null): AdsProviderInterface
    {
        $selected = strtolower(trim((string) $platform));
        if ($selected === '') {
            $selected = (string) config('adscore.default_provider', 'mock');
        }

        $className = config("adscore.providers.{$selected}");
        if (is_string($className) && class_exists($className)) {
            $instance = $this->container->make($className);
            if ($instance instanceof AdsProviderInterface) {
                return $instance;
            }
        }

        return $this->container->make(MockAdsProvider::class);
    }
}
