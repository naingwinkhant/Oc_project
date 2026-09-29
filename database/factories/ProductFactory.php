<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $price = fake()->randomFloat(2, 20, 800);

        return [
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'slug' => str($name)->slug()->append('-'.fake()->unique()->numberBetween(1, 99999))->value(),
            'sku' => 'SKU-'.fake()->unique()->numerify('########'),
            'barcode' => fake()->unique()->numerify('48##########'),
            'brand' => fake()->randomElement(['Dole', 'San Miguel', 'Del Monte', 'Nestle', 'Nescafe']),
            'description' => fake()->sentence(12),
            'unit' => fake()->randomElement(['pcs', 'kg', 'pack', 'bottle']),
            'weight' => fake()->randomFloat(3, 0.1, 5),
            'price' => $price,
            'cost_price' => round($price * 0.78, 2),
            'stock' => fake()->numberBetween(0, 200),
            'min_stock' => fake()->numberBetween(5, 30),
            'is_active' => true,
            'is_featured' => false,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock' => 0]);
    }

    public function lowStock(): static
    {
        return $this->state(fn () => ['stock' => 3, 'min_stock' => 10]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function newArrival(): static
    {
        return $this->state(fn () => ['is_new' => true]);
    }

    public function onSale(int $salePrice): static
    {
        return $this->state(fn () => ['sale_price' => $salePrice]);
    }
}
