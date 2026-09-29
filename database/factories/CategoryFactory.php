<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'parent_id' => null,
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'description' => fake()->sentence(),
            'color' => fake()->randomElement(['emerald', 'amber', 'sky', 'rose', 'violet', 'slate']),
            'position' => 0,
            'is_active' => true,
        ];
    }

    public function childOf(Category $parent): static
    {
        return $this->state(fn () => [
            'parent_id' => $parent->id,
        ]);
    }
}
