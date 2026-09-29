<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Staff = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Manager => 'Inventory Manager',
            self::Staff => 'Staff',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Admin => 'Full access including users, categories and settings.',
            self::Manager => 'Manage goods, categories, stock and suppliers.',
            self::Staff => 'Browse goods and record stock movements.',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Admin => 'bg-rose-50 text-rose-700 ring-rose-600/20',
            self::Manager => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            self::Staff => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
