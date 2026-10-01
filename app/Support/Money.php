<?php

namespace App\Support;

use NumberFormatter;

/**
 * Myanmar kyat formatting.
 *
 * The kyat has no minor unit in circulation, so amounts are whole numbers and are
 * rounded to the nearest 50 kyat the way a shelf label would be.
 */
class Money
{
    public static function symbol(): string
    {
        return (string) config('shop.currency_symbol', 'Ks');
    }

    public static function code(): string
    {
        return (string) config('shop.currency.code', 'MMK');
    }

    /**
     * Round a price the way a supermarket would print it on a shelf label.
     */
    public static function round(float|int|string $amount): int
    {
        $step = (int) config('shop.currency.round_to', 50);

        if ($step < 1) {
            return (int) round((float) $amount);
        }

        return (int) (round((float) $amount / $step) * $step);
    }

    /**
     * "4,000 Ks"
     */
    public static function format(float|int|string|null $amount, bool $withSymbol = true): string
    {
        $value = self::round($amount ?? 0);
        $formatted = number_format($value);

        return $withSymbol ? $formatted.' '.self::symbol() : $formatted;
    }

    /**
     * "Ks 4,000" — for price tags where the symbol leads.
     */
    public static function prefix(float|int|string|null $amount): string
    {
        return self::symbol().' '.number_format(self::round($amount ?? 0));
    }

    /**
     * "1.2M Ks" / "850K Ks" — for dashboard tiles where space is tight.
     */
    public static function compact(float|int|string|null $amount): string
    {
        $value = (float) ($amount ?? 0);

        if ($value >= 1_000_000_000) {
            return round($value / 1_000_000_000, 1).'B '.self::symbol();
        }

        if ($value >= 1_000_000) {
            return round($value / 1_000_000, 1).'M '.self::symbol();
        }

        if ($value >= 10_000) {
            return round($value / 1_000).'K '.self::symbol();
        }

        return self::format($value);
    }

    /**
     * Locale-aware formatting for the few places that need a real Intl formatter
     * (the animated dashboard counters, in JavaScript, mirror this).
     */
    public static function localized(float|int|string|null $amount): string
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::DECIMAL);

        return $formatter->format((float) ($amount ?? 0));
    }
}
