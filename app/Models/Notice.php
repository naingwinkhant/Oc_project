<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A short announcement written by a manager and shown to shoppers.
 */
class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'tone',
        'is_active',
        'show_on_shop',
        'starts_on',
        'ends_on',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_on_shop' => 'boolean',
            'starts_on' => 'date',
            'ends_on' => 'date',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Live notices: switched on, and inside their date window if they have one.
     */
    public function scopeLive(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('starts_on')->orWhereDate('starts_on', '<=', $today))
            ->where(fn (Builder $q) => $q->whereNull('ends_on')->orWhereDate('ends_on', '>=', $today));
    }

    public function scopeForShop(Builder $query): Builder
    {
        return $query->live()->where('show_on_shop', true);
    }

    public function isLive(): bool
    {
        return $this->scopeLive(static::query()->whereKey($this->id))->exists();
    }

    public function toneClasses(): string
    {
        return match ($this->tone) {
            'info' => 'bg-sky-50 text-sky-900 ring-sky-600/20',
            'warning' => 'bg-amber-50 text-amber-900 ring-amber-600/20',
            'danger' => 'bg-rose-50 text-rose-900 ring-rose-600/20',
            default => 'bg-brand-50 text-brand-900 ring-brand-600/20',
        };
    }
}
