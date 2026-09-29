<?php

namespace App\Payments\Gateways;

use App\Models\Order;

/**
 * UAB Pay — United Asia Bank's TransactEase gateway, also used by the uabpay+
 * merchant QR app.
 *
 * UAB signs a sorted key=value string with HMAC-SHA256 using the merchant
 * secret, and delivers the same signature back on both the callback and the
 * browser return.
 */
class UabPayGateway extends HttpGateway
{
    public function code(): string
    {
        return 'uabpay';
    }

    protected function requiredKeys(): array
    {
        return ['merchant_id', 'access_key', 'secret_key'];
    }

    protected function secret(): string
    {
        return (string) config('shop.payments.uabpay.secret_key', '');
    }

    public function createPayment(Order $order, array $customer): array
    {
        $reference = $this->reference($order);
        $expire = now()->addSeconds((int) config('shop.payments.uabpay.expire', 300));

        $fields = [
            'merchantId' => config('shop.payments.uabpay.merchant_id'),
            'accessKey' => config('shop.payments.uabpay.access_key'),
            'channel' => config('shop.payments.uabpay.channel', 'WEB'),
            'orderNo' => $reference,
            'amount' => $order->total,
            'currency' => 'MMK',
            'description' => 'Order '.$reference,
            'expireTime' => $expire->format('YmdHis'),
            'callbackUrl' => $this->callbackUrl(),
            'successUrl' => $this->returnUrl(),
            'failUrl' => $this->returnUrl(),
            'customerName' => $customer['name'] ?? '',
            'customerPhone' => $customer['phone'] ?? '',
        ];

        $fields['signature'] = $this->sign($this->canonicalString($fields));

        $query = http_build_query($fields);

        return [
            'redirect_url' => config('shop.payments.uabpay.payment_url').'?'.$query,
            'qr_payload' => config('shop.payments.uabpay.payment_url').'?'.$query,
            'reference' => $reference,
            'instructions' => $this->defaultInstructions(),
            'payload' => $fields,
        ];
    }

    public function verifyCallback(array $payload): ?string
    {
        $inner = $this->unwrap($payload);

        $normalised = $inner;
        $normalised['sign'] = $normalised['signature'] ?? $normalised['Signature'] ?? null;

        if (! $this->verifySignature($normalised)) {
            return null;
        }

        $status = strtoupper((string) ($inner['respCode'] ?? $inner['status'] ?? ''));

        if (! in_array($status, ['000', 'SUCCESS', 'PAID', 'COMPLETED'], true)) {
            return null;
        }

        return (string) ($inner['orderNo'] ?? $inner['transactionId'] ?? '');
    }
}
