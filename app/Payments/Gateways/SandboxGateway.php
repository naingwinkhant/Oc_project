<?php

namespace App\Payments\Gateways;

use App\Models\Order;
use App\Payments\Contracts\PaymentGateway as GatewayContract;

/**
 * Local payment simulator.
 *
 * Used whenever PAYMENTS_DRIVER=sandbox (the default), so the full
 * cart -> checkout -> payment -> order flow can be exercised without merchant
 * credentials. No money moves and nothing leaves the machine.
 */
class SandboxGateway implements GatewayContract
{
    public function code(): string
    {
        return 'sandbox';
    }

    public function label(): string
    {
        return 'Sandbox (test)';
    }

    public function isConfigured(): bool
    {
        return (bool) config('shop.payments.sandbox.enabled', true);
    }

    public function createPayment(Order $order, array $customer): array
    {
        return [
            'redirect_url' => route('payments.sandbox', $order),
            'qr_payload' => null,
            'reference' => $order->order_number,
            'instructions' => [
                'This is a simulated payment — no real money moves.',
                'Choose "Pay now" to settle the order, or "Simulate a decline" to test the failure path.',
            ],
            'payload' => [
                'sandbox' => true,
                'signature' => $this->sign($order),
            ],
        ];
    }

    public function verifyCallback(array $payload): ?string
    {
        $order = $payload['order'] ?? null;
        $signature = (string) ($payload['signature'] ?? '');
        $expected = is_string($order) ? $this->signFor($order) : '';

        if ($expected === '' || ! hash_equals($expected, $signature)) {
            return null;
        }

        return $order;
    }

    private function sign(Order $order): string
    {
        return $this->signFor($order->order_number);
    }

    private function signFor(string $orderNumber): string
    {
        return hash_hmac('sha256', $orderNumber, (string) config('app.key'));
    }
}
