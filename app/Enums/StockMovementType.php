<?php

namespace App\Enums;

enum StockMovementType: string
{
    case In = 'in';
    case Out = 'out';
    case Adjustment = 'adjustment';
    case Return = 'return';

    public function label(): string
    {
        return match ($this) {
            self::In => 'Stock in',
            self::Out => 'Stock out',
            self::Adjustment => 'Adjustment',
            self::Return => 'Return',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::In => 'M12 4.5v15m0 0l-6-6m6 6l6-6',
            self::Out => 'M12 19.5v-15m0 0l6 6m-6-6l-6 6',
            self::Adjustment => 'M12 4.5v15m7.5-7.5h-15',
            self::Return => 'M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::In, self::Return => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::Out => 'bg-rose-50 text-rose-700 ring-rose-600/20',
            self::Adjustment => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        };
    }

    public function sign(): int
    {
        return match ($this) {
            self::In, self::Return => 1,
            self::Out, self::Adjustment => -1,
        };
    }

    public function isPositive(): bool
    {
        return $this->sign() > 0;
    }
}
