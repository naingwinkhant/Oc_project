<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'code' => Str::upper(Str::substr($name, 0, 3)).'-'.fake()->unique()->numberBetween(1000, 9999),
            'contact_name' => fake()->name(),
            'phone' => fake()->numerify('+63 917 ### ####'),
            'email' => fake()->unique()->companyEmail(),
            'address' => fake()->address(),
            'is_active' => true,
        ];
    }
}
