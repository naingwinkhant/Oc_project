<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentGateway;
use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'phone',
        'email',
        'delivery_address',
        'township',
        'delivery_zone',
        'delivery_eta',
        'note',
        'subtotal',
        'delivery_fee',
        'total',
        'status',
        'payment_gateway',
        'payment_reference',
        'placed_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_gateway' => PaymentGateway::class,
            'subtotal' => 'integer',
            'delivery_fee' => 'integer',
            'total' => 'integer',
            'placed_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function isPaid(): bool
    {
        return $this->status === OrderStatus::Paid;
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    public function totalFormatted(): string
    {
        return Money::format($this->total);
    }

    public function itemCount(): int
    {
        return (int) $this->items->sum('quantity');
    }

    public static function generateNumber(): string
    {
        $prefix = (string) config('shop.orders.prefix', 'GGS');
        $date = now()->format('ymd');

        do {
            $number = $prefix.'-'.$date.'-'.strtoupper(Str::random(5));
        } while (self::query()->where('order_number', $number)->exists());

        return $number;
    }
}
