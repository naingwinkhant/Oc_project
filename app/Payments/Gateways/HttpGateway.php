<?php

namespace App\Payments\Gateways;

use App\Enums\PaymentGateway;
use App\Models\Order;
use App\Payments\Contracts\PaymentGateway as GatewayContract;

/**
 * Shared plumbing for the HTTP providers: config access, request signing and
 * signature verification.
 *
 * Signing here is HMAC-SHA256 over the provider's field set. Every Myanmar
 * provider documents its own exact field list and ordering, so confirm the
 * canonical string against that provider's merchant documentation before going
 * live — the transport, order plumbing and callback routing are provider-agnostic.
 */
abstract class HttpGateway implements GatewayContract
{
    public function label(): string
    {
        return PaymentGateway::from($this->code())->label();
    }

    public function isConfigured(): bool
    {
        if (! (bool) config("shop.payments.{$this->code()}.enabled", false)) {
            return false;
        }

        foreach ($this->requiredKeys() as $key) {
            if (blank(config("shop.payments.{$this->code()}.{$key}"))) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int, string>
     */
    abstract protected function requiredKeys(): array;

    abstract public function createPayment(Order $order, array $customer): array;

    protected function secret(): string
    {
        return (string) config("shop.payments.{$this->code()}.secret", '');
    }

    /**
     * Providers commonly hand the signature back as base64 or as hex; accept both.
     */
    protected function sign(string $canonical, ?string $key = null): string
    {
        return hash_hmac('sha256', $canonical, $key ?: $this->secret());
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    protected function canonicalString(array $fields, array $ignore = ['sign', 'signature', 'Signature']): string
    {
        $filtered = array_diff_key($fields, array_flip($ignore));
        ksort($filtered);

        $parts = [];

        foreach ($filtered as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            $parts[] = $key.'='.$value;
        }

        return implode('&', $parts);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function verifySignature(array $payload, array $ignore = ['sign', 'signature', 'Signature']): bool
    {
        $secret = $this->secret();

        if ($secret === '') {
            return false;
        }

        $provided = $payload['sign'] ?? $payload['signature'] ?? $payload['Signature'] ?? null;

        if (! is_string($provided) || $provided === '') {
            return false;
        }

        $expected = $this->sign($this->canonicalString($payload, $ignore), $secret);

        return hash_equals($expected, $provided) || hash_equals($expected, base64_encode(base64_decode($provided, true) ?: ''));
    }

    /**
     * Providers deliver the callback body inside a "Request" envelope; unwrap it.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function unwrap(array $payload): array
    {
        $inner = $payload['Request'] ?? $payload['request'] ?? null;

        return is_array($inner) ? $inner : $payload;
    }

    /**
     * @return array{ok: bool, body: array<string, mixed>}
     */
    protected function postJson(string $url, array $body): array
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Accept: application/json'],
            CURLOPT_TIMEOUT => (int) config("shop.payments.{$this->code()}.timeout", 30),
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return ['ok' => false, 'body' => ['error' => $error]];
        }

        $decoded = json_decode((string) $response, true);

        return ['ok' => is_array($decoded), 'body' => is_array($decoded) ? $decoded : ['raw' => $response]];
    }

    protected function reference(Order $order): string
    {
        return $order->order_number;
    }

    /**
     * @return array<int, string>
     */
    protected function defaultInstructions(): array
    {
        return [
            'Open your '.strtolower($this->label()).' app and scan the code, or follow the link to pay.',
            'You will be returned to '.config('shop.name').' automatically when the payment finishes.',
        ];
    }

    protected function callbackUrl(): string
    {
        return route('payments.callback', ['gateway' => $this->code()]);
    }

    protected function returnUrl(): string
    {
        return route('payments.return', ['gateway' => $this->code()]);
    }
}
