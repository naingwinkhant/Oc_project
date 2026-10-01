<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'theme',
        // Written by the controllers, never by a form: a request must not be
        // able to make its own account accepted.
        'status',
        'approved_at',
        'approved_by',
        'decided_at',
        'decided_by',
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
            'approved_at' => 'datetime',
            'decided_at' => 'datetime',
            'role' => Role::class,
            'status' => AccountStatus::class,
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

    /**
     * Sign-in is by email address.
     */
    public static function findByEmail(string $email): ?self
    {
        return static::query()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower(trim($email))])
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

    /**
     * Anyone who works here, as opposed to a shopper. Staff also get the
     * notices that are marked as internal only.
     */
    public function isStaff(): bool
    {
        return in_array($this->role, [Role::Admin, Role::Manager, Role::Staff], true);
    }

    /**
     * Has this person chosen a password yet?
     *
     * An account is created with an email, a username and a role only, so a
     * null password is a normal state rather than a broken one — and it is the
     * reason an empty password can never be accepted at sign-in.
     */
    public function hasPassword(): bool
    {
        return $this->password !== null && $this->password !== '';
    }

    /**
     * Has an administrator or manager accepted this account yet?
     *
     * An account is created pending, so a username nobody has agreed to is not
     * a way into the shop. It can be signed into until then.
     */
    public function isApproved(): bool
    {
        return $this->status === AccountStatus::Approved;
    }

    public function isPending(): bool
    {
        return $this->status === AccountStatus::Pending;
    }

    public function isRejected(): bool
    {
        return $this->status === AccountStatus::Rejected;
    }

    /**
     * Accept the account, recording who did it and when.
     */
    public function approve(User $approver): void
    {
        $this->forceFill([
            'status' => AccountStatus::Approved,
            'approved_at' => now(),
            'approved_by' => $approver->id,
            'decided_by' => $approver->id,
            'decided_at' => now(),
        ])->save();
    }

    /**
     * Turn the account down. It stays on file so the history is not lost, but
     * it can never sign in.
     */
    public function reject(User $approver): void
    {
        $this->forceFill([
            'status' => AccountStatus::Rejected,
            'approved_at' => null,
            'approved_by' => null,
            'decided_by' => $approver->id,
            'decided_at' => now(),
        ])->save();
    }

    /**
     * Withdraw an acceptance, which locks the account out again and puts it
     * back in the queue for somebody to look at.
     */
    public function revokeApproval(): void
    {
        $this->forceFill([
            'status' => AccountStatus::Pending,
            'approved_at' => null,
            'approved_by' => null,
            'decided_by' => null,
            'decided_at' => null,
        ])->save();
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(self::class, 'approved_by');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(self::class, 'decided_by');
    }

    /**
     * Where this person lands after signing in.
     */
    public function landingUrl(): string
    {
        return route($this->role->homeRoute());
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

    /**
     * Accounts nobody has looked at yet.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', AccountStatus::Pending);
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
