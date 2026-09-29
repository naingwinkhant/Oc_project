<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'role' => Role::class,
        ];
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    /**
     * Usernames are compared case-insensitively, so "Aung" and "aung" are the
     * same account, but the stored value keeps its original casing for display.
     */
    public function scopeWhereUsername(Builder $query, string $username): Builder
    {
        return $query->whereRaw('LOWER(username) = ?', [mb_strtolower($username)]);
    }

    public static function findForLogin(string $identifier): ?self
    {
        return static::query()
            ->where('email', $identifier)
            ->orWhereRaw('LOWER(username) = ?', [mb_strtolower($identifier)])
            ->first();
    }

    public function canManageCatalog(): bool
    {
        return in_array($this->role, [Role::Admin, Role::Manager], true);
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');
    }

    public function avatarUrl(): ?string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }

        return null;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';

        return $query->where(function (Builder $query) use ($like) {
            $query->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('username', 'like', $like)
                ->orWhere('phone', 'like', $like);
        });
    }
}
