<?php

namespace App\Enums;

/**
 * Where an order is in its life.
 *
 * Stock moves when a payment is confirmed, not when an order is completed, so
 * Completed means the goods have been handed over and nothing else needs to be
 * taken off the shelf.
 */
enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Awaiting payment',
            self::Paid => 'Paid',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
            self::Refunded => 'Refunded',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            self::Paid => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::Completed => 'bg-teal-50 text-teal-800 ring-teal-600/20',
            self::Cancelled => 'bg-ink-100 text-ink-500 ring-ink-500/10',
            self::Refunded => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        };
    }

    /**
     * Is this a finished state, with nothing left to decide?
     */
    public function isClosed(): bool
    {
        return in_array($this, [self::Cancelled, self::Refunded, self::Completed], true);
    }

    /**
     * Can the till move an order from here to there?
     *
     * Closed orders stay closed, so a completed or cancelled order cannot be
     * reopened by the click of a status menu.
     *
     * @return array<int, self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Pending, self::Paid, self::Cancelled],
            self::Paid => [self::Paid, self::Completed, self::Refunded],
            self::Completed, self::Cancelled, self::Refunded => [$this],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }

    /**
     * The states the till is allowed to choose from, for a status menu.
     *
     * @return array<int, self>
     */
    public function options(): array
    {
        return array_values(array_filter($this->allowedTransitions(), fn (self $status) => $status !== $this));
    }
}
