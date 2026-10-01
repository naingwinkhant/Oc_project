<?php

namespace Database\Factories;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= bcrypt('password'),
            'role' => Role::Staff,
            'phone' => fake()->numerify('09 ### ### ##'),
            'is_active' => true,
            // Accepted by default; the pending state is opt-in below.
            'approved_at' => now(),
            'status' => AccountStatus::Approved,
            'remember_token' => Str::random(10),
        ];
    }

    /** An account that has been registered but nobody has looked at yet. */
    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => AccountStatus::Pending,
            'approved_at' => null,
            'approved_by' => null,
        ]);
    }

    /** An account that was turned down. */
    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => AccountStatus::Rejected,
            'approved_at' => null,
            'approved_by' => null,
        ]);
    }

    /** A shopper's own account. */
    public function customer(): static
    {
        return $this->state(fn () => ['role' => Role::Customer]);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => Role::Admin]);
    }

    public function manager(): static
    {
        return $this->state(fn () => ['role' => Role::Manager]);
    }

    public function staff(): static
    {
        return $this->state(fn () => ['role' => Role::Staff]);
    }

    public function disabled(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
