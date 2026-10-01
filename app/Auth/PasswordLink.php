<?php

namespace App\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * One-time links that let somebody choose their own password.
 *
 * The plain token only ever exists in the URL that is handed over. What is
 * stored is its SHA-256 hash, so a leaked copy of the table cannot be turned
 * back into a usable link. The existing password_reset_tokens table is reused
 * rather than adding a second one.
 */
class PasswordLink
{
    /** A link is good for an hour. */
    public const EXPIRES_MINUTES = 60;

    /**
     * Issue a link for an account that has no password yet.
     */
    public function issue(User $user): string
    {
        $plain = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => hash('sha256', $plain), 'created_at' => now()]
        );

        return $plain;
    }

    /**
     * The account a token belongs to, or null if it is unknown or too old.
     */
    public function resolve(string $plain): ?User
    {
        if ($plain === '') {
            return null;
        }

        $row = DB::table('password_reset_tokens')
            ->where('token', hash('sha256', $plain))
            ->first();

        if (! $row || ! $this->isFresh($row->created_at)) {
            return null;
        }

        return User::query()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($row->email)])
            ->whereNull('password')
            ->first();
    }

    /**
     * Store the new password and burn the link.
     *
     * The password column is written through the query builder so the model's
     * `hashed` cast cannot be involved: the value is already a bcrypt hash, and
     * a null has to stay null rather than become the hash of an empty string.
     */
    public function complete(User $user, string $plain, string $password): void
    {
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'updated_at' => now(),
            ]);

        $this->forget($user);
    }

    public function forget(User $user): void
    {
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
    }

    /**
     * Drop any link that has aged out, so the table does not grow.
     */
    public function prune(): void
    {
        DB::table('password_reset_tokens')
            ->where('created_at', '<', now()->subMinutes(self::EXPIRES_MINUTES))
            ->delete();
    }

    private function isFresh(?string $createdAt): bool
    {
        return $createdAt !== null
            && strtotime($createdAt) >= strtotime('-'.self::EXPIRES_MINUTES.' minutes');
    }
}
