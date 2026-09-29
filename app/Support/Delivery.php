<?php

namespace App\Support;

/**
 * Zoned delivery pricing.
 *
 * The charge depends on which township the order goes to, so the cart can only
 * show a range; checkout resolves the exact zone once a township is chosen.
 */
class Delivery
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function zones(): array
    {
        return (array) config('shop.delivery.zones', []);
    }

    public static function zoneKeys(): array
    {
        return array_column(self::zones(), 'key');
    }

    /**
     * @return array<int, string>
     */
    public static function townships(): array
    {
        $all = [];

        foreach (self::zones() as $zone) {
            foreach ($zone['townships'] as $township) {
                $all[$township] = true;
            }
        }

        $townships = array_keys($all);
        sort($townships);

        return $townships;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function zoneFor(?string $township): ?array
    {
        if (! $township) {
            return null;
        }

        foreach (self::zones() as $zone) {
            if (in_array($township, $zone['townships'], true)) {
                return $zone;
            }
        }

        return null;
    }

    public static function feeFor(?string $township, int $subtotal = 0): int
    {
        $freeOver = (int) config('shop.delivery.free_over', 0);

        if ($freeOver > 0 && $subtotal >= $freeOver) {
            return 0;
        }

        $zone = self::zoneFor($township);

        return (int) ($zone['fee'] ?? config('shop.delivery.fee', 0));
    }

    public static function etaFor(?string $township): ?string
    {
        $zone = self::zoneFor($township);

        return $zone ? (string) $zone['eta'] : null;
    }

    public static function isFree(int $subtotal): bool
    {
        $freeOver = (int) config('shop.delivery.free_over', 0);

        return $freeOver > 0 && $subtotal >= $freeOver;
    }

    public static function amountUntilFree(int $subtotal): int
    {
        return max(0, (int) config('shop.delivery.free_over', 0) - $subtotal);
    }

    /**
     * Cheapest and dearest zone, used for the "from X to Y" hint in the cart.
     */
    public static function range(int $subtotal = 0): array
    {
        $fees = self::fees($subtotal);

        return [
            'min' => (int) min($fees),
            'max' => (int) max($fees),
            'min_formatted' => Money::format(min($fees)),
            'max_formatted' => Money::format(max($fees)),
        ];
    }

    /**
     * Fee per zone key, for the live checkout total.
     *
     * @return array<string, int>
     */
    public static function fees(int $subtotal = 0): array
    {
        $fees = [];

        foreach (self::zones() as $zone) {
            $fees[$zone['key']] = self::feeFor($zone['townships'][0] ?? null, $subtotal);
        }

        return $fees;
    }

    /**
     * Flat map the checkout page uses: township -> [zone key, fee].
     *
     * @return array<string, array{0: string, 1: int, 2: string}>
     */
    public static function townshipMap(int $subtotal = 0): array
    {
        $map = [];

        foreach (self::zones() as $zone) {
            foreach ($zone['townships'] as $township) {
                $map[$township] = [
                    $zone['key'],
                    self::feeFor($township, $subtotal),
                    (string) $zone['eta'],
                ];
            }
        }

        return $map;
    }
}
