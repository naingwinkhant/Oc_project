<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Something that happened in the shop that the team needs to know about.
 *
 * Raised by the application rather than written by a person, so it cannot be
 * edited — only read, and cleared by whoever has dealt with it.
 */
class TeamAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'kind',
        'title',
        'body',
        'link',
        'order_id',
        'user_id',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * The account this alert is about, when it is about an account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clearedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_alert_dismissals')
            ->withPivot('dismissed_at');
    }

    public function toneClasses(): string
    {
        return match ($this->kind) {
            'order' => 'bg-brand-50 text-brand-900 ring-brand-600/20',
            'account' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
            default => 'bg-ink-100 text-ink-700 ring-ink-500/10',
        };
    }

    public function icon(): string
    {
        return match ($this->kind) {
            'order' => 'clipboard',
            'account' => 'users',
            default => 'bell',
        };
    }
}
