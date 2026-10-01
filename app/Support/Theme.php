<?php

namespace App\Support;

/**
 * The shopper's day/night preference.
 *
 * The value lives in localStorage for everyone and on the user record for
 * signed-in shoppers. "system" follows the operating system, and a listener
 * keeps up if the system changes while the page is open.
 */
class Theme
{
    public const LIGHT = 'light';

    public const DARK = 'dark';

    public const SYSTEM = 'system';

    public const STORAGE_KEY = 'ggs.theme';

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return [self::LIGHT, self::DARK, self::SYSTEM];
    }

    public static function isValid(?string $value): bool
    {
        return $value !== null && in_array($value, self::options(), true);
    }

    public static function normalise(?string $value): string
    {
        return self::isValid($value) ? $value : self::SYSTEM;
    }

    /**
     * What the server should assume when rendering the first paint.
     *
     * A signed-in shopper's saved choice is authoritative. Everyone else gets
     * "system", which the head script resolves from the browser.
     */
    public static function forCurrentUser(): string
    {
        return self::normalise(auth()->user()?->theme);
    }
}
