<?php

namespace Database\Seeders;

use App\Models\Favourite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavouriteSeeder extends Seeder
{
    public function run(): void
    {
        Favourite::query()->delete();

        $users = User::query()->whereIn('role', ['admin', 'manager'])->get();
        $staff = User::query()->where('role', 'staff')->get();
        $customers = $users->concat($staff);

        if ($customers->isEmpty()) {
            return;
        }

        // Everyone saves a handful of everyday staples; the manager also watches
        // the fresh produce that turns over fastest.
        $staples = Product::query()
            ->whereIn('name', [
                'Fresh Whole Milk 1L', 'Chicken Eggs (Large)', 'Baguette',
                'Cola 1.5L', 'Toothpaste 150g', 'Cavendish Bananas',
            ])
            ->get();

        foreach ($customers as $user) {
            foreach ($staples->take(random_int(2, 4)) as $product) {
                Favourite::firstOrCreate([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                ]);
            }
        }
    }
}
