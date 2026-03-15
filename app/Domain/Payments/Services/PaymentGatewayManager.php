<?php

namespace App\Domain\Payments\Services;

use App\Domain\Payments\Contracts\PaymentGateway;
use App\Domain\Payments\Gateways\IyzicoGateway;
use App\Domain\Payments\Gateways\MockPayGateway;
use App\Domain\Payments\Gateways\PaytrGateway;
use InvalidArgumentException;

class PaymentGatewayManager
{
    public function resolve(string $provider): PaymentGateway
    {
        return match (strtolower(trim($provider))) {
            'mockpay' => app(MockPayGateway::class),
            'iyzico' => app(IyzicoGateway::class),
            'paytr' => app(PaytrGateway::class),
            default => throw new InvalidArgumentException('Unsupported payment provider: '.$provider),
        };
    }
}
