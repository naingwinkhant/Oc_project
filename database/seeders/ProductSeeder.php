<?php

namespace Database\Seeders;

use App\Enums\StockMovementType;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Support\Money;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catalogue = [
            'Fruits' => [
                ['Cavendish Bananas', 'Dole', 'kg', 89.00, 62.00, 140, 12],
                ['Royal Gala Apples', 'Fresh Farms', 'kg', 165.00, 118.00, 85, 20],
                ['Mangoes (Carabao)', 'Local', 'kg', 135.00, 95.00, 60, 25],
                ['Pineapple', 'Dole', 'pc', 110.00, 78.00, 40, 12],
                ['Watermelon', 'Local', 'kg', 42.00, 28.00, 95, 10],
                ['Valencia Oranges', 'Fresh Farms', 'kg', 128.00, 92.00, 70, 18],
            ],
            'Vegetables' => [
                ['Potatoes (Lady Finger)', 'Local', 'kg', 62.00, 44.00, 180, 30],
                ['Tomatoes (Roma)', 'Local', 'kg', 78.00, 55.00, 120, 25],
                ['Carrots', 'Fresh Farms', 'kg', 58.00, 40.00, 150, 20],
                ['Onions (Red)', 'Local', 'kg', 66.00, 47.00, 160, 25],
                ['Bitter Melon', 'Local', 'kg', 72.00, 52.00, 55, 10],
                ['Chinese Pechay', 'Fresh Farms', 'kg', 54.00, 38.00, 88, 15],
            ],
            'Salad & Greens' => [
                ['Romaine Lettuce', 'Fresh Farms', 'pc', 68.00, 46.00, 40, 10],
                ['Iceberg Lettuce', 'Fresh Farms', 'pc', 62.00, 42.00, 35, 8],
                ['Fresh Spinach', 'Fresh Farms', 'pack', 45.00, 30.00, 26, 8],
                ['Kalamata Olives', 'Gourmet', 'jar', 145.00, 105.00, 18, 6],
            ],
            'Herbs' => [
                ['Fresh Basil', 'Herbal House', 'pack', 35.00, 22.00, 15, 4],
                ['Coriander Leaves', 'Local', 'pack', 28.00, 18.00, 12, 3],
            ],
            'Beef' => [
                ['Beef Brisket', 'San Miguel', 'kg', 480.00, 400.00, 30, 10],
                ['Ground Beef (Pork-Free)', 'San Miguel', 'kg', 420.00, 350.00, 45, 15],
                ['Beef Ribeye', 'San Miguel', 'kg', 780.00, 660.00, 12, 5],
            ],
            'Pork' => [
                ['Pork Liempo', 'San Miguel', 'kg', 320.00, 265.00, 40, 12],
                ['Pork Belly', 'San Miguel', 'kg', 395.00, 330.00, 28, 10],
                ['Longganisa', 'Del Monte', 'pack', 245.00, 195.00, 22, 6],
            ],
            'Chicken' => [
                ['Whole Chicken', 'San Miguel', 'kg', 215.00, 172.00, 110, 20],
                ['Chicken Breast Fillets', 'San Miguel', 'kg', 340.00, 275.00, 65, 15],
                ['Chicken Wings', 'San Miguel', 'kg', 268.00, 215.00, 48, 12],
            ],
            'Seafood' => [
                ['Prawns (Medium)', 'Davao Fresh', 'kg', 620.00, 520.00, 24, 8],
                ['Milkfish (Bangus)', 'Local', 'kg', 235.00, 190.00, 45, 12],
                ['Tuna Loin', 'Davao Fresh', 'kg', 480.00, 400.00, 20, 6],
                ['Squid (Pusit)', 'Davao Fresh', 'kg', 350.00, 290.00, 26, 8],
            ],
            'Milk' => [
                ['Fresh Whole Milk 1L', 'Dairy Queen', 'bottle', 78.00, 62.00, 120, 24],
                ['Pasteurized Milk 1L', 'Dairy Queen', 'bottle', 72.00, 58.00, 140, 24],
                ['Chocolate Milk 1L', 'Dairy Queen', 'bottle', 85.00, 68.00, 90, 18],
            ],
            'Cheese' => [
                ['Cheddar Cheese Block', 'Dairy Queen', 'pack', 215.00, 172.00, 34, 10],
                ['Mozzarella (Whole)', 'Bella Italia', 'pack', 268.00, 215.00, 26, 8],
                ['Processed Cheese Singles', 'Dairy Queen', 'pack', 118.00, 92.00, 48, 12],
            ],
            'Eggs' => [
                ['Chicken Eggs (Large)', 'Local', 'tray', 195.00, 158.00, 85, 20],
                ['Duck Eggs', 'Local', 'tray', 235.00, 190.00, 30, 10],
            ],
            'Bread' => [
                ['Sourdough Loaf', 'Bakers Row', 'pc', 135.00, 92.00, 24, 8],
                ['Sandwich Loaf', 'Bakers Row', 'pc', 68.00, 48.00, 60, 15],
                ['Baguette', 'Bakers Row', 'pc', 55.00, 38.00, 30, 10],
                ['Whole Wheat Bread', 'Bakers Row', 'pc', 82.00, 56.00, 28, 8],
            ],
            'Pastries' => [
                ['Butter Croissant', 'Bakers Row', 'pc', 62.00, 40.00, 32, 10],
                ['Ensaymada', 'Bakers Row', 'pc', 55.00, 35.00, 40, 12],
                ['Cheese Danish', 'Bakers Row', 'pc', 78.00, 52.00, 18, 6],
            ],
            'Rice & Grains' => [
                ['Sinandomeng Rice 5kg', 'Golden Harvest', 'sack', 385.00, 320.00, 45, 12],
                ['Garlic Fryer Rice 5kg', 'Golden Harvest', 'sack', 425.00, 355.00, 38, 10],
                ['Oatmeal Groats 1kg', 'Sunrise', 'pack', 148.00, 112.00, 32, 8],
            ],
            'Canned Goods' => [
                ['Corned Tuna Flakes', 'Century Tuna', 'can', 68.00, 52.00, 120, 24],
                ['Tomato Paste', 'Del Monte', 'can', 42.00, 32.00, 140, 30],
                ['Tuna in Oil 185g', 'Century Tuna', 'can', 92.00, 72.00, 85, 20],
            ],
            'Pasta & Noodles' => [
                ['Spaghetti Penne 500g', 'De Cecco', 'pack', 128.00, 98.00, 46, 12],
                ['Instant Noodles (Case)', 'Indomie', 'case', 620.00, 540.00, 30, 8],
                ['Corned Beef (150g)', 'Star K', 'can', 58.00, 44.00, 95, 20],
            ],
            'Oils & Vinegar' => [
                ['Coconut Oil 1L', 'Golden Harvest', 'bottle', 178.00, 142.00, 38, 10],
                ['Olive Oil 500ml', 'Bertolli', 'bottle', 398.00, 320.00, 18, 6],
                ['Soy Sauce 500ml', 'Datu Puti', 'bottle', 62.00, 48.00, 55, 15],
            ],
            'Spices' => [
                ['Black Pepper Whole', 'Kamiseta', 'pack', 78.00, 58.00, 28, 8],
                ['Iodized Salt 1kg', 'Diamond', 'pack', 32.00, 24.00, 70, 20],
                ['Garlic Powder', 'Kamiseta', 'pack', 92.00, 68.00, 22, 6],
            ],
            'Water' => [
                ['Purified Water 500ml (Case)', 'Crystal', 'case', 215.00, 178.00, 60, 15],
                ['Mineral Water 1.5L', 'Crystal', 'bottle', 32.00, 24.00, 180, 40],
            ],
            'Soft Drinks' => [
                ['Cola 1.5L', 'Coca-Cola', 'bottle', 78.00, 62.00, 95, 24],
                ['Lemon Soda 1.5L', 'Coca-Cola', 'bottle', 78.00, 62.00, 88, 24],
                ['Energy Drink 250ml', 'Red Bull', 'can', 78.00, 64.00, 72, 18],
            ],
            'Juice' => [
                ['Orange Juice 1L', 'Tropicana', 'bottle', 138.00, 108.00, 42, 12],
                ['Mango Juice 1L', 'Tropicana', 'bottle', 125.00, 98.00, 38, 10],
            ],
            'Coffee & Tea' => [
                ['Instant Coffee 100g', 'Nescafe', 'jar', 245.00, 195.00, 28, 8],
                ['Ground Coffee 250g', 'Kopi Tito', 'pack', 185.00, 148.00, 24, 8],
                ['Tea Bags (25s)', 'Lipton', 'box', 118.00, 92.00, 35, 10],
            ],
            'Beer & Wine' => [
                ['Pale Pilsen 320ml (Case)', 'San Miguel', 'case', 720.00, 640.00, 22, 8],
                ['Red Wine 750ml', 'Fernando', 'bottle', 480.00, 395.00, 15, 5],
            ],
            'Chips & Crisps' => [
                ['Potato Crisps (Case)', 'Piattos', 'case', 385.00, 330.00, 32, 10],
                ['Bbq Flakes (Case)', 'Mab', 'case', 340.00, 292.00, 28, 10],
                ['Corn Chips (Case)', 'Cheese Corn', 'case', 320.00, 275.00, 26, 8],
            ],
            'Chocolate' => [
                ['Milk Chocolate Bar', 'Toblerone', 'bar', 128.00, 98.00, 48, 12],
                ['Dark Chocolate 70%', 'Hershey', 'bar', 115.00, 88.00, 36, 10],
            ],
            'Biscuits & Cookies' => [
                ['Sandwich Biscuits (Pack)', 'Chips Ahoy', 'pack', 62.00, 48.00, 52, 15],
                ['Cream-filled Cookies', 'Oreos', 'pack', 78.00, 60.00, 44, 12],
            ],
            'Nuts & Dried Fruit' => [
                ['Cashew Nuts 250g', 'Hershey', 'pack', 245.00, 195.00, 20, 6],
                ['Dried Mangoes 200g', 'Dried', 'pack', 135.00, 105.00, 28, 8],
            ],
            'Cleaning Supplies' => [
                ['Dishwashing Liquid 250ml', 'Joy', 'bottle', 78.00, 62.00, 65, 15],
                ['Floor Cleaner 1L', 'Champion', 'bottle', 128.00, 98.00, 42, 12],
                ['Bleach 1L', 'Cloroklax', 'bottle', 68.00, 52.00, 58, 15],
            ],
            'Paper Goods' => [
                ['Toilet Paper (10 rolls)', 'Scott', 'pack', 245.00, 198.00, 30, 10],
                ['Facial Tissue (Case)', 'Paseo', 'case', 320.00, 265.00, 26, 8],
            ],
            'Laundry' => [
                ['Powder Detergent 1kg', 'Tide', 'pack', 178.00, 142.00, 28, 8],
                ['Fabric Softener 900ml', 'Downy', 'bottle', 195.00, 158.00, 22, 6],
            ],
            'Kitchenware' => [
                ['Frying Pan 24cm', 'Cookmate', 'pc', 385.00, 295.00, 12, 4],
                ['Stainless Steel Pot 20cm', 'Cookmate', 'pc', 520.00, 410.00, 8, 3],
            ],
            'Trash & Storage' => [
                ['Garbage Bags (30s)', 'Handy', 'pack', 185.00, 148.00, 24, 8],
                ['Food Storage Container 3L', 'Lock&Lock', 'pc', 245.00, 190.00, 16, 5],
            ],
            'Bath & Body' => [
                ['Bath & Shower Soap 90g', 'Safeguard', 'bar', 42.00, 32.00, 120, 30],
                ['Body Lotion 250ml', 'Nivea', 'bottle', 198.00, 158.00, 34, 10],
            ],
            'Hair Care' => [
                ['Shampoo 350ml', 'Pantene', 'bottle', 215.00, 172.00, 30, 8],
                ['Conditioner 350ml', 'Pantene', 'bottle', 215.00, 172.00, 28, 8],
            ],
            'Oral Care' => [
                ['Toothpaste 150g', 'Colgate', 'tube', 92.00, 72.00, 75, 20],
                ['Toothbrush (2s)', 'Oral-B', 'pack', 128.00, 98.00, 40, 12],
            ],
            'Baby Care' => [
                ['Baby Diapers M (32s)', 'Pampers', 'pack', 585.00, 495.00, 18, 6],
                ['Baby Lotion 200ml', 'Johnson', 'bottle', 245.00, 198.00, 20, 6],
            ],
            'Frozen Vegetables' => [
                ['Mixed Vegetables 500g', 'Mama', 'pack', 88.00, 68.00, 55, 15],
                ['Green Peas 1kg', 'Mama', 'pack', 118.00, 92.00, 38, 10],
            ],
            'Frozen Meats' => [
                ['Chicken Nuggets 250g', 'Kenny Rogers', 'pack', 168.00, 132.00, 26, 8],
                ['Beef Patty (Case)', 'Magnolia', 'case', 780.00, 660.00, 12, 4],
            ],
            'Ice Cream' => [
                ['Vanilla Ice Cream 1.5L', 'Selecta', 'tub', 245.00, 195.00, 24, 8],
                ['Ube Ice Cream 1L', 'Selecta', 'tub', 225.00, 178.00, 20, 6],
            ],
            'Ready Meals' => [
                ['Chicken Fried Rice', 'Mama', 'pack', 78.00, 58.00, 45, 12],
                ['Beef Stew (Microwave)', 'Century', 'pack', 118.00, 92.00, 32, 10],
            ],
        ];

        $user = User::query()->where('role', 'manager')->first() ?? User::query()->first();
        $counter = 1;

        $palette = ['#047857', '#0f766e', '#4d7c0f', '#b45309', '#0369a1', '#7e22ce', '#be123c', '#334155'];

        foreach ($catalogue as $categoryName => $items) {
            $category = Category::where('name', $categoryName)->first();

            if (! $category) {
                continue;
            }

            foreach ($items as [$name, $brand, $unit, $price, $cost, $stock, $minStock]) {
                $sku = 'SKU-'.str_pad((string) $counter, 6, '0', STR_PAD_LEFT);
                $price = $this->toKyat($price);
                $cost = $this->toKyat($cost);

                $product = Product::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'slug' => Str::slug($name),
                        'barcode' => '48'.str_pad((string) (1000000000 + $counter * 7919), 10, '0', STR_PAD_LEFT),
                        'brand' => $brand,
                        'description' => $name.' — '.$brand.'. Stored and rotated according to shelf-life policy. Minimum reorder level is '.$minStock.' '.$unit.'.',
                        'unit' => $unit,
                        'price' => $price,
                        'cost_price' => $cost,
                        'stock' => $stock,
                        'min_stock' => $minStock,
                        'is_active' => true,
                        'is_featured' => $counter % 17 === 0,
                        'created_at' => now()->subDays(random_int(1, 240)),
                        'updated_at' => now()->subDays(random_int(0, 30)),
                    ],
                );

                $this->seedMovements($product, $user, $counter);
                $this->attachProductImage($product, $palette);
                $this->applyStockScenario($product, $counter);
                $this->applyDates($product, $categoryName, $counter);
                $this->markComingSoon($product, $counter);
                $this->applyPromotion($product, $counter);
                $this->markNewArrival($product, $counter);
                $this->logActivity($product, $user, $counter);
                $counter++;
            }
        }
    }

    /**
     * The catalogue below is written in a convenient round source currency; the
     * store trades in kyat, so convert once here rather than rewriting 109 rows.
     */
    private function toKyat(float $amount): int
    {
        return Money::round($amount * (float) env('SEED_PRICE_FACTOR', 45));
    }

    /**
     * Shelf life in days, by classification. Fresh produce goes off in a week;
     * canned and dry goods keep for a year or more.
     *
     * @return array<string, array{0: int, 1: int}> [min days, max days]
     */
    private function shelfLifeFor(string $categoryName): array
    {
        return match (true) {
            str_contains($categoryName, 'Fruits'),
            str_contains($categoryName, 'Vegetables'),
            str_contains($categoryName, 'Salad'),
            str_contains($categoryName, 'Herbs') => [3, 12],

            str_contains($categoryName, 'Meat'),
            str_contains($categoryName, 'Seafood'),
            str_contains($categoryName, 'Pork'),
            str_contains($categoryName, 'Chicken') => [4, 10],

            str_contains($categoryName, 'Milk'),
            str_contains($categoryName, 'Yogurt') => [7, 21],
            str_contains($categoryName, 'Cheese') => [45, 120],
            str_contains($categoryName, 'Eggs') => [14, 30],

            str_contains($categoryName, 'Pastries') => [1, 3],
            str_contains($categoryName, 'Bread'),
            str_contains($categoryName, 'Bakery') => [3, 7],

            str_contains($categoryName, 'Pasta'),
            str_contains($categoryName, 'Oils'),
            str_contains($categoryName, 'Spices'),
            str_contains($categoryName, 'Rice') => [365, 730],

            str_contains($categoryName, 'Canned'),
            str_contains($categoryName, 'Noodles') => [365, 730],

            str_contains($categoryName, 'Frozen') => [180, 365],
            str_contains($categoryName, 'Ice Cream') => [120, 240],

            str_contains($categoryName, 'Ready Meals') => [90, 180],

            str_contains($categoryName, 'Beverages'),
            str_contains($categoryName, 'Juice'),
            str_contains($categoryName, 'Water') => [120, 365],

            str_contains($categoryName, 'Coffee'),
            str_contains($categoryName, 'Tea') => [180, 365],

            str_contains($categoryName, 'Beer') => [180, 365],
            str_contains($categoryName, 'Wine') => [1095, 1825],

            str_contains($categoryName, 'Chips'),
            str_contains($categoryName, 'Chocolate'),
            str_contains($categoryName, 'Biscuits'),
            str_contains($categoryName, 'Cookies'),
            str_contains($categoryName, 'Nuts') => [150, 365],

            str_contains($categoryName, 'Household'),
            str_contains($categoryName, 'Cleaning'),
            str_contains($categoryName, 'Laundry'),
            str_contains($categoryName, 'Paper') => [730, 1825],

            str_contains($categoryName, 'Personal Care') => [365, 1095],
            str_contains($categoryName, 'Baby Care') => [365, 730],

            default => [180, 540],
        };
    }

    /**
     * Stamp a plausible production and expiry date, then deliberately push a few
     * items into the "expiring soon" and "expired" bands so the alerts have
     * something to show.
     */
    private function applyDates(Product $product, string $categoryName, int $index): void
    {
        [$minDays, $maxDays] = $this->shelfLifeFor($categoryName);
        $shelfLife = random_int($minDays, $maxDays);

        // Every 9th item is a clearance close to its date, every 17th is past it.
        $daysAgo = match (true) {
            $index % 17 === 0 => $shelfLife + random_int(2, 20),
            $index % 9 === 0 => $shelfLife - random_int(1, max(1, (int) round($shelfLife * 0.2))),
            default => random_int(0, max(0, (int) round($shelfLife * 0.6))),
        };

        $produced = now()->subDays(max(0, $daysAgo));

        $product->forceFill([
            'produced_at' => $produced->toDateString(),
            'expires_at' => $produced->copy()->addDays($shelfLife)->toDateString(),
        ])->save();
    }

    /**
     * Put roughly a quarter of the catalogue on promotion, so the blue sale
     * price and the struck-through red original both appear on the shelf.
     */
    private function applyPromotion(Product $product, int $index): void
    {
        // Nothing to promote before the batch is actually on the shelf.
        if ($product->isComingSoon() || $index % 4 !== 0) {
            return;
        }

        $percent = [10, 15, 20, 25, 30][$index % 5];
        $sale = (int) round($product->price * (1 - $percent / 100));

        // Keep the discount to shelf-label-looking round numbers.
        $sale = max(100, $sale - ($sale % 50));

        // Never promote below cost, otherwise the "featured" shelf quietly loses
        // money on every unit sold.
        $floor = (int) $product->cost_price;
        $sale = max($floor, $sale - ($sale % 50));

        if ($sale >= (int) $product->price) {
            return;
        }

        $product->forceFill([
            'sale_price' => $sale,
            'is_featured' => true,
        ])->save();
    }

    /**
     * Flag a slice of the catalogue as newly landed, and backdate its
     * created_at so "newest first" ordering is meaningful.
     */
    private function markNewArrival(Product $product, int $index): void
    {
        if ($index % 7 !== 0) {
            return;
        }

        $product->forceFill([
            'is_new' => true,
            'created_at' => now()->subDays(random_int(0, (int) config('shop.freshness.new_arrival_days', 30))),
        ])->save();
    }

    /**
     * A few batches are still on their way: listed in the catalogue with a
     * "coming soon" badge, but blocked from the cart until they land.
     */
    private function markComingSoon(Product $product, int $index): void
    {
        if ($index % 23 !== 0) {
            return;
        }

        $availableFrom = now()->addDays(random_int(2, 21))->startOfDay();
        $shelfLife = max(3, (int) $product->shelfLifeDays());

        // A batch that has not landed cannot already be going off, so move the
        // whole window forward: packed the day before it arrives.
        $product->forceFill([
            'available_from' => $availableFrom->toDateString(),
            'produced_at' => $availableFrom->copy()->subDay()->toDateString(),
            'expires_at' => $availableFrom->copy()->addDays($shelfLife)->toDateString(),
        ])->save();
    }

    private function logActivity(Product $product, ?User $user, int $index): void
    {
        if ($index % 3 !== 0) {
            return;
        }

        ActivityLog::create([
            'user_id' => $user?->id,
            'action' => 'created',
            'subject_type' => Product::class,
            'subject_id' => $product->id,
            'description' => 'Added goods item '.$product->name,
            'created_at' => now()->subDays(random_int(0, 20))->subMinutes(random_int(0, 900)),
        ]);
    }

    private function applyStockScenario(Product $product, int $index): void
    {
        $scenario = $index % 11;

        $stock = match (true) {
            $scenario === 0 => 0,
            $scenario === 3 => max(0, (int) round($product->min_stock * 0.4)),
            $scenario === 7 => (int) $product->min_stock,
            default => $product->stock,
        };

        if ($stock !== $product->stock) {
            $product->forceFill(['stock' => $stock])->save();
        }
    }

    /**
     * Give the product a real photograph when scripts/fetch-product-images.php has
     * downloaded one, otherwise fall back to a generated SVG so seeding still
     * works with no network and no downloaded assets.
     */
    private function attachProductImage(Product $product, array $palette): void
    {
        if ($product->image) {
            return;
        }

        $photo = 'products/'.$product->slug.'.jpg';

        if (Storage::disk('public')->exists($photo)) {
            $product->forceFill(['image' => $photo])->save();

            return;
        }

        $bg = $palette[$product->category_id % count($palette)];

        $initials = mb_strtoupper(collect(preg_split('/\s+/', trim($product->name)))
            ->filter(fn (string $word) => preg_match('/[\p{L}\p{N}]/u', $word) === 1)
            ->take(2)
            ->map(fn (string $word) => mb_substr(preg_replace('/[^\p{L}\p{N}]/u', '', $word), 0, 1))
            ->implode(''));

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300" role="img" aria-label="{$product->name}">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$bg}"/>
      <stop offset="100%" stop-color="{$this->shade($bg, -18)}"/>
    </linearGradient>
  </defs>
  <rect width="400" height="300" fill="url(#g)"/>
  <circle cx="200" cy="132" r="62" fill="rgba(255,255,255,0.14)"/>
  <text x="200" y="152" text-anchor="middle" font-family="Plus Jakarta Sans, Segoe UI, sans-serif"
        font-size="52" font-weight="700" fill="rgba(255,255,255,0.92)">{$initials}</text>
  <text x="200" y="240" text-anchor="middle" font-family="Plus Jakarta Sans, Segoe UI, sans-serif"
        font-size="19" font-weight="600" fill="rgba(255,255,255,0.85)">{$this->escape($product->brand ?? $product->unit)}</text>
</svg>
SVG;

        $path = 'products/'.$product->slug.'.svg';

        Storage::disk('public')->put($path, $svg);
        $product->forceFill(['image' => $path])->save();
    }

    private function shade(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');
        $out = '#';

        foreach (str_split($hex, 2) as $pair) {
            $value = max(0, min(255, (int) hexdec($pair) + (int) round(255 * $percent / 100)));
            $out .= str_pad(dechex($value), 2, '0', STR_PAD_LEFT);
        }

        return $out;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars(mb_substr($value, 0, 28), ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function seedMovements(Product $product, ?User $user, int $index): void
    {
        if ($product->stockMovements()->exists()) {
            return;
        }

        $received = $product->stock + random_int(6, 30);
        $type = $index % 6 === 0 ? StockMovementType::Return : StockMovementType::In;
        $daysAgo = random_int(1, 21);

        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'type' => $type,
            'quantity' => $received,
            'balance_after' => $received,
            'reason' => 'Supplier delivery',
            'reference' => 'PO-'.str_pad((string) ($index * 13 % 9000 + 1000), 4, '0', STR_PAD_LEFT),
            'created_at' => now()->subDays($daysAgo),
        ]);

        if ($index % 3 === 0) {
            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $user?->id,
                'type' => StockMovementType::Out,
                'quantity' => -min($received, random_int(1, 8)),
                'balance_after' => $product->stock,
                'reason' => 'Shrinkage / spoilage',
                'created_at' => now()->subDays(max(0, $daysAgo - 1)),
            ]);
        }
    }
}
