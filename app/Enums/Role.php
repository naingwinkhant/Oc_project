<?php

namespace App\Enums;

/**
 * Who somebody is in the shop.
 *
 * Customers do not need one to order, but they may sign in to see their own
 * orders; the other three work here.
 */
enum Role: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Staff = 'staff';
    case Customer = 'customer';

    /**
     * Every role as plain strings, for validation rules and pickers.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Manager => 'Inventory Manager',
            self::Staff => 'Staff',
            self::Customer => 'Customer',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Admin => 'Full access including users, approvals, classifications and settings.',
            self::Manager => 'Manage goods, stock, suppliers and approve new accounts.',
            self::Staff => 'Browse goods and record stock movements.',
            self::Customer => 'Order as a guest or sign in to see your own orders.',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Admin => 'bg-violet-50 text-violet-700 ring-violet-600/20',
            self::Manager => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            self::Staff => 'bg-sky-50 text-sky-700 ring-sky-600/20',
            self::Customer => 'bg-ink-100 text-ink-700 ring-ink-500/10',
        };
    }

    /**
     * Where somebody lands after signing in.
     */
    public function homeRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.dashboard',
            self::Manager => 'admin.manager.home',
            self::Staff => 'admin.staff.home',
            self::Customer => 'account.home',
        };
    }
}
