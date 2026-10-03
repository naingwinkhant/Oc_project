<?php

namespace Database\Factories;

use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Advertisement>
 */
class AdvertisementFactory extends Factory
{
    protected $model = Advertisement::class;

    public function definition(): array
    {
        $title = fake()->unique()->catchPhrase();

        return [
            'title' => $title,
            'eyebrow' => Str::upper(fake()->randomElement(['This week', 'Fresh in', 'New arrival', 'Offer'])),
            'body' => fake()->sentence(8),
            // No media by default: a worded slide over the store's own colours
            // is what an advertisement looks like before a picture is uploaded.
            'image' => null,
            'video' => null,
            'poster' => null,
            'link' => null,
            'button_label' => null,
            'position' => 0,
            'starts_on' => null,
            'ends_on' => null,
            'is_active' => true,
            'user_id' => User::factory(),
        ];
    }

    /** A slide with no date window, so it is always showing. */
    public function alwaysOn(): static
    {
        return $this->state(fn () => [
            'starts_on' => null,
            'ends_on' => null,
        ]);
    }

    /** Switched off, so it is kept but not shown. */
    public function switchedOff(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    /** Waiting for its start date. */
    public function scheduled(): static
    {
        return $this->state(fn () => [
            'starts_on' => now()->addWeek(),
            'ends_on' => now()->addMonth(),
        ]);
    }

    /** Its window has closed. */
    public function finished(): static
    {
        return $this->state(fn () => [
            'starts_on' => now()->subMonth(),
            'ends_on' => now()->subDay(),
        ]);
    }

    public function atPosition(int $position): static
    {
        return $this->state(fn () => ['position' => $position]);
    }
}
