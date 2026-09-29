<?php

namespace Database\Factories;

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
            'remember_token' => Str::random(10),
        ];
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
