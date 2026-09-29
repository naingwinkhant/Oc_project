<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Enums\StockMovementType;
use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gateway',
        'gateway_reference',
        'amount',
        'status',
        'payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'gateway' => PaymentGateway::class,
            'status' => PaymentStatus::class,
            'amount' => 'integer',
            'payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Paid;
    }

    public function amountFormatted(): string
    {
        return Money::format($this->amount);
    }

    /**
     * Where the customer has to be sent to finish this attempt. Kept on the row so
     * an order page reload can hand back the same link instead of minting a new
     * payment intent with the provider.
     */
    public function redirectUrl(): string
    {
        $payload = is_array($this->payload) ? $this->payload : [];

        return $payload['redirect_url'] ?? route('checkout.show', $this->order);
    }

    /**
     * Mark the payment (and its order) as settled. Safe to call twice — a provider
     * may deliver the same callback more than once.
     */
    public function markPaid(?string $reference = null): void
    {
        if ($this->isPaid()) {
            return;
        }

        $this->forceFill([
            'status' => PaymentStatus::Paid,
            'paid_at' => $this->paid_at ?? now(),
            'gateway_reference' => $reference ?: $this->gateway_reference,
        ])->save();

        $this->order()->update([
            'status' => OrderStatus::Paid,
            'paid_at' => $this->order->paid_at ?? now(),
            'payment_reference' => $reference ?: $this->order->payment_reference,
        ]);

        // Stock is only committed once money is confirmed.
        $this->order->items()->each(function (OrderItem $item) {
            $item->product?->recordMovement(
                type: StockMovementType::Out,
                quantity: $item->quantity,
                reason: 'Sold on order '.$this->order->order_number,
                reference: $this->order->order_number,
            );
        });
    }

    public function markFailed(?string $reference = null): void
    {
        if ($this->isPaid()) {
            return;
        }

        $this->forceFill([
            'status' => PaymentStatus::Failed,
            'gateway_reference' => $reference ?: $this->gateway_reference,
        ])->save();
    }
}
