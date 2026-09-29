<?php

namespace App\Payments;

use App\Enums\PaymentGateway;
use App\Payments\Contracts\PaymentGateway as GatewayContract;
use Illuminate\Support\Collection;

/**
 * Resolves the gateway a checkout should use.
 *
 * Live providers are only offered once they are both enabled in config and
 * actually have credentials, so a half-configured store never shows a broken
 * payment option at the till.
 */
class PaymentManager
{
    /**
     * @return Collection<int, PaymentGateway>
     */
    public function available(): Collection
    {
        $available = [];

        foreach (PaymentGateway::cases() as $gateway) {
            if ($this->isAvailable($gateway)) {
                $available[] = $gateway;
            }
        }

        return new Collection($available);
    }

    public function isAvailable(PaymentGateway $gateway): bool
    {
        if ($gateway === PaymentGateway::Cash) {
            return true;
        }

        $class = $gateway->gatewayClass();

        if (! $class) {
            return false;
        }

        /** @var GatewayContract $instance */
        $instance = app($class);

        return $instance->isConfigured();
    }

    public function resolve(PaymentGateway $gateway): GatewayContract
    {
        $class = $gateway->gatewayClass();

        if (! $class) {
            throw new \InvalidArgumentException($gateway->label().' does not use an online gateway.');
        }

        return app($class);
    }

    /**
     * The gateway that takes over when no live provider is configured, so the
     * checkout flow stays testable.
     */
    public function driver(): string
    {
        return (string) config('shop.payments.driver', 'sandbox');
    }
}
