<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\User;
use App\Services\CategoryTreeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            'Fresh Produce' => [
                'description' => 'Fruits and vegetables delivered daily from local farms.',
                'color' => 'emerald',
                'children' => ['Fruits', 'Vegetables', 'Salad & Greens', 'Herbs'],
            ],
            'Meat & Seafood' => [
                'description' => 'Fresh cuts from the wet market and the cold chain.',
                'color' => 'rose',
                'children' => ['Beef', 'Pork', 'Chicken', 'Seafood'],
            ],
            'Dairy & Eggs' => [
                'description' => 'Chilled dairy, cheeses and farm eggs.',
                'color' => 'sky',
                'children' => ['Milk', 'Cheese', 'Yogurt', 'Eggs'],
            ],
            'Bakery' => [
                'description' => 'Baked on site every morning.',
                'color' => 'amber',
                'children' => ['Bread', 'Pastries', 'Cakes'],
            ],
            'Pantry & Dry Goods' => [
                'description' => 'Shelf-stable essentials and world pantry staples.',
                'color' => 'violet',
                'children' => ['Rice & Grains', 'Canned Goods', 'Pasta & Noodles', 'Oils & Vinegar', 'Spices'],
            ],
            'Beverages' => [
                'description' => 'Chilled drinks, water, juice and coffee.',
                'color' => 'sky',
                'children' => ['Water', 'Soft Drinks', 'Juice', 'Coffee & Tea', 'Beer & Wine'],
            ],
            'Snacks & Confectionery' => [
                'description' => 'Impulse aisle: chips, chocolate, biscuits.',
                'color' => 'amber',
                'children' => ['Chips & Crisps', 'Chocolate', 'Biscuits & Cookies', 'Nuts & Dried Fruit'],
            ],
            'Household & Cleaning' => [
                'description' => 'Cleaning, paper goods, kitchen and laundry.',
                'color' => 'slate',
                'children' => ['Cleaning Supplies', 'Paper Goods', 'Laundry', 'Kitchenware', 'Trash & Storage'],
            ],
            'Personal Care' => [
                'description' => 'Bath, body, hair and oral care.',
                'color' => 'rose',
                'children' => ['Bath & Body', 'Hair Care', 'Oral Care', 'Baby Care'],
            ],
            'Frozen Foods' => [
                'description' => 'Frozen ready meals, seafood and vegetables.',
                'color' => 'sky',
                'children' => ['Frozen Vegetables', 'Frozen Meats', 'Ice Cream', 'Ready Meals'],
            ],
        ];

        $position = 0;

        foreach ($tree as $name => $config) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => $config['description'],
                    'color' => $config['color'],
                    'position' => $position++,
                    'is_active' => true,
                ],
            );

            $childPosition = 0;

            foreach ($config['children'] as $childName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($name.' '.$childName)],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childName,
                        'position' => $childPosition++,
                        'is_active' => true,
                    ],
                );
            }
        }

        app(CategoryTreeService::class)->rebuildAll();

        $this->logActivity();
    }

    private function logActivity(): void
    {
        $admin = User::query()->where('role', Role::Admin)->value('id');
        $manager = User::query()->where('role', Role::Manager)->value('id');

        foreach (Category::query()->roots()->with('children')->get() as $index => $root) {
            ActivityLog::create([
                'user_id' => $index % 2 === 0 ? $admin : $manager,
                'action' => 'created',
                'subject_type' => Category::class,
                'subject_id' => $root->id,
                'description' => 'Created classification '.$root->name.' with '.$root->children->count().' aisles',
                'created_at' => now()->subDays(random_int(2, 45)),
            ]);
        }
    }
}
