<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Awaiting payment',
            self::Paid => 'Paid',
            self::Cancelled => 'Cancelled',
            self::Refunded => 'Refunded',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            self::Paid => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::Cancelled => 'bg-ink-100 text-ink-500 ring-ink-500/10',
            self::Refunded => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        };
    }
}
