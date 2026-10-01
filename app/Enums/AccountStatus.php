<?php

namespace App\Enums;

/**
 * Where an account stands with the shop.
 *
 * Anything other than Accepted cannot sign in, which is what makes a freshly
 * registered account harmless until somebody looks at it.
 */
enum AccountStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Waiting to be accepted',
            self::Approved => 'Accepted',
            self::Rejected => 'Turned down',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Pending => 'bg-violet-50 text-violet-700 ring-violet-600/20',
            self::Approved => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::Rejected => 'bg-rose-50 text-rose-700 ring-rose-600/20',
        };
    }

    /**
     * The reason to give somebody at sign-in.
     */
    public function refusalMessage(): string
    {
        return match ($this) {
            self::Pending => 'This account is waiting to be accepted. Ask an administrator or manager to approve it.',
            self::Rejected => 'This account was turned down. Contact an administrator if you think that is a mistake.',
            self::Approved => '',
        };
    }
}
