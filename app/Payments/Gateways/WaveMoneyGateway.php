<?php

namespace App\Payments\Gateways;

use App\Models\Order;

/**
 * Wave Money — wallet push payment plus the Wave Money QR rail.
 *
 * Wave returns an "authenticate" URL that the customer opens in the app; for QR
 * trades that same URL is what gets encoded.
 */
class WaveMoneyGateway extends HttpGateway
{
    public function code(): string
    {
        return 'wavemoney';
    }

    protected function requiredKeys(): array
    {
        return ['merchant_id', 'api_key', 'api_secret'];
    }

    public function createPayment(Order $order, array $customer): array
    {
        $reference = $this->reference($order);

        $body = [
            'merchant_id' => config('shop.payments.wavemoney.merchant_id'),
            'api_key' => config('shop.payments.wavemoney.api_key'),
            'reference' => $reference,
            'amount' => $order->total,
            'currency' => 'MMK',
            'redirect_url' => $this->returnUrl(),
            'notify_url' => $this->callbackUrl(),
            'customer_name' => $customer['name'] ?? '',
            'customer_phone' => $customer['phone'] ?? '',
        ];

        $body['signature'] = $this->sign($this->canonicalString($body));

        $response = $this->postJson(
            (string) config('shop.payments.wavemoney.payment_url'),
            $body
        );

        $data = $response['body'];
        $authenticate = $data['authenticate_url'] ?? $data['redirect_url'] ?? null;

        return [
            'redirect_url' => $authenticate,
            'qr_payload' => $authenticate,
            'reference' => $reference,
            'instructions' => $this->defaultInstructions(),
            'payload' => $data,
        ];
    }

    public function verifyCallback(array $payload): ?string
    {
        $inner = $this->unwrap($payload);

        if (! $this->verifySignature($inner, ['sign', 'signature', 'Signature', 'hash'])) {
            return null;
        }

        $status = strtolower((string) ($inner['status'] ?? ''));

        if (! in_array($status, ['paid', 'success', 'completed'], true)) {
            return null;
        }

        return (string) ($inner['transaction_id'] ?? $inner['reference'] ?? '');
    }
}
