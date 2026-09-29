<?php

namespace App\Payments\Gateways;

use App\Models\Order;

/**
 * KBZPay — KBZ Bank's merchant gateway (also the KBZ DirectPay product and the
 * MMQR scan-to-pay rail).
 *
 * Precreate returns a redirect URL for the hosted page and, for QR trades, the
 * raw EMVCo payload to encode.
 */
class KbzPayGateway extends HttpGateway
{
    public function code(): string
    {
        return 'kbzpay';
    }

    protected function requiredKeys(): array
    {
        return ['merchant_code', 'app_id', 'secret'];
    }

    public function createPayment(Order $order, array $customer): array
    {
        $reference = $this->reference($order);

        $body = [
            'merch_code' => config('shop.payments.kbzpay.merchant_code'),
            'app_id' => config('shop.payments.kbzpay.app_id'),
            'trade_type' => config('shop.payments.kbzpay.trade_type', 'APP'),
            'order_id' => $reference,
            'currency' => 'MMK',
            'amount' => $order->total,
            'notify_url' => $this->callbackUrl(),
            'return_url' => $this->returnUrl(),
            'payer_name' => $customer['name'] ?? '',
            'payer_phone' => $customer['phone'] ?? '',
            'nonce_str' => bin2hex(random_bytes(8)),
        ];

        $body['sign'] = $this->sign($this->canonicalString($body));

        $response = $this->postJson(
            (string) config('shop.payments.kbzpay.precreate_url'),
            $body
        );

        $data = $response['body'];

        return [
            'redirect_url' => $data['redirect_url'] ?? $data['precreateUrl'] ?? null,
            'qr_payload' => $data['qrCode'] ?? $data['qr_code'] ?? null,
            'reference' => $reference,
            'instructions' => $this->defaultInstructions(),
            'payload' => $data,
        ];
    }

    public function verifyCallback(array $payload): ?string
    {
        $inner = $this->unwrap($payload);

        if (! $this->verifySignature($inner)) {
            return null;
        }

        $status = strtoupper((string) ($inner['status'] ?? ''));

        if (! in_array($status, ['SUCCESS', 'PAID', 'COMPLETED'], true)) {
            return null;
        }

        return (string) ($inner['order_id'] ?? $inner['transaction_id'] ?? '');
    }
}
