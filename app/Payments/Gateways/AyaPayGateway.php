<?php

namespace App\Payments\Gateways;

use App\Models\Order;

/**
 * AyaPay — Aya Bank's wallet push payment and QR rail.
 */
class AyaPayGateway extends HttpGateway
{
    public function code(): string
    {
        return 'ayapay';
    }

    protected function requiredKeys(): array
    {
        return ['merchant_id', 'merchant_key'];
    }

    public function createPayment(Order $order, array $customer): array
    {
        $reference = $this->reference($order);

        $body = [
            'merchantId' => config('shop.payments.ayapay.merchant_id'),
            'referenceNumber' => $reference,
            'amount' => $order->total,
            'currency' => 'MMK',
            'description' => 'Order '.$reference,
            'callbackUrl' => $this->callbackUrl(),
            'returnUrl' => $this->returnUrl(),
            'customerName' => $customer['name'] ?? '',
            'customerPhone' => $customer['phone'] ?? '',
            'txnTime' => now()->format('YmdHis'),
        ];

        $body['signature'] = $this->sign($this->canonicalString($body));

        $response = $this->postJson(
            (string) config('shop.payments.ayapay.payment_url'),
            $body
        );

        $data = $response['body'];

        return [
            'redirect_url' => $data['redirectUrl'] ?? $data['payment_url'] ?? null,
            'qr_payload' => $data['qrdata'] ?? $data['qrData'] ?? null,
            'reference' => $reference,
            'instructions' => $this->defaultInstructions(),
            'payload' => $data,
        ];
    }

    public function verifyCallback(array $payload): ?string
    {
        $inner = $this->unwrap($payload);

        $status = strtolower((string) ($inner['status'] ?? $inner['responseCode'] ?? ''));

        if (! in_array($status, ['success', 'paid', 'completed', '000'], true)) {
            return null;
        }

        // Aya's callback carries its own signature field; reuse the shared
        // verifier by normalising the field name it uses.
        $normalised = $inner;
        $normalised['sign'] = $normalised['signature'] ?? $normalised['Signature'] ?? null;

        if (! $this->verifySignature($normalised)) {
            return null;
        }

        return (string) ($inner['referenceNumber'] ?? $inner['transactionId'] ?? '');
    }
}
