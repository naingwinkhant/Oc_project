<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subjectLabel(): string
    {
        return class_basename((string) $this->subject_type);
    }

    public function tone(): string
    {
        return match ($this->action) {
            'created' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            'updated' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
            'deleted' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
            'stock_in', 'stock_out' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            default => 'bg-slate-100 text-slate-700 ring-slate-500/20',
        };
    }

    public function scopeRecent(Builder $query, int $limit = 12): Builder
    {
        return $query->latest()->limit($limit);
    }
}
