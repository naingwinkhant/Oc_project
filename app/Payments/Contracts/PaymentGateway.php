<?php

namespace App\Payments\Contracts;

use App\Models\Order;

interface PaymentGateway
{
    /**
     * Stable machine key, e.g. "kbzpay".
     */
    public function code(): string;

    public function label(): string;

    /**
     * True when the store has the credentials this gateway needs.
     */
    public function isConfigured(): bool;

    /**
     * Start a payment and return where the customer should be sent, plus any
     * scan-to-pay payload the provider supports.
     *
     * @return array{redirect_url: ?string, qr_payload: ?string, reference: string, instructions: array<int, string>, payload: array<string, mixed>}
     */
    public function createPayment(Order $order, array $customer): array;

    /**
     * Verify a provider callback and return the provider's own reference for
     * the transaction, or null when the signature does not check out.
     */
    public function verifyCallback(array $payload): ?string;
}
