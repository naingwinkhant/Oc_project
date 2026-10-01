<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\CategoryTreeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_parent_and_child_builds_the_path(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->post(route('admin.categories.store'), [
                'name' => 'Fresh Produce',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $parent = Category::where('name', 'Fresh Produce')->firstOrFail();

        $this->actingAs($manager)
            ->post(route('admin.categories.store'), [
                'name' => 'Fruits',
                'parent_id' => $parent->id,
                'is_active' => '1',
            ]);

        $child = Category::where('name', 'Fruits')->firstOrFail();

        $this->assertSame(0, $parent->depth);
        $this->assertSame("/{$parent->id}/", $parent->path);

        $this->assertSame(1, $child->depth);
        $this->assertSame("/{$parent->id}/{$child->id}/", $child->path);
    }

    public function test_a_category_cannot_be_moved_inside_its_own_descendant(): void
    {
        $manager = User::factory()->manager()->create();
        $parent = Category::factory()->create();
        $child = Category::factory()->childOf($parent)->create();

        $this->actingAs($manager)
            ->put(route('admin.categories.update', $parent), [
                'name' => $parent->name,
                'parent_id' => $child->id,
            ])
            ->assertSessionHasErrors('parent_id');
    }

    public function test_a_category_cannot_be_its_own_parent(): void
    {
        $manager = User::factory()->manager()->create();
        $category = Category::factory()->create();

        $this->actingAs($manager)
            ->put(route('admin.categories.update', $category), [
                'name' => $category->name,
                'parent_id' => $category->id,
            ])
            ->assertSessionHasErrors('parent_id');
    }

    public function test_descendant_ids_cover_the_whole_subtree(): void
    {
        $root = Category::factory()->create();
        $mid = Category::factory()->childOf($root)->create();
        $leaf = Category::factory()->childOf($mid)->create();
        $sibling = Category::factory()->create();

        $ids = $root->descendantIds();

        $this->assertContains($root->id, $ids);
        $this->assertContains($mid->id, $ids);
        $this->assertContains($leaf->id, $ids);
        $this->assertNotContains($sibling->id, $ids);
    }

    public function test_deleting_a_parent_promotes_children_and_unclassifies_goods(): void
    {
        $manager = User::factory()->manager()->create();
        $parent = Category::factory()->create(['name' => 'Snacks']);
        $child = Category::factory()->childOf($parent)->create();
        $product = Product::factory()->for($parent)->create();

        $this->actingAs($manager)
            ->delete(route('admin.categories.destroy', $parent))
            ->assertSessionHas('success');

        $this->assertNull(Category::find($parent->id));
        $this->assertNull($child->fresh()->parent_id);
        $this->assertNull($product->fresh()->category_id);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_classification_tree_nests_children(): void
    {
        $root = Category::factory()->create(['name' => 'Dairy']);
        $child = Category::factory()->childOf($root)->create(['name' => 'Cheese']);
        Product::factory()->for($child)->create();

        app(CategoryTreeService::class)->rebuildAll();

        $tree = app(CategoryTreeService::class)->nest(Category::query()->withCount('products')->get());

        $this->assertCount(1, $tree);
        $this->assertSame('Dairy', $tree[0]['name']);
        $this->assertCount(1, $tree[0]['children']);
        $this->assertSame('Cheese', $tree[0]['children'][0]['name']);
        $this->assertSame(1, $tree[0]['total_products_count']);
    }

    public function test_toggle_switches_visibility(): void
    {
        $manager = User::factory()->manager()->create();
        $category = Category::factory()->create(['is_active' => true]);

        $this->actingAs($manager)
            ->post(route('admin.categories.toggle', $category))
            ->assertOk()
            ->assertJson(['is_active' => false]);

        $this->assertFalse($category->fresh()->is_active);
    }

    public function test_reorder_persists_positions(): void
    {
        $manager = User::factory()->manager()->create();
        $a = Category::factory()->create();
        $b = Category::factory()->create();

        $this->actingAs($manager)
            ->post(route('admin.categories.reorder'), [
                'order' => [
                    ['id' => $b->id, 'position' => 0],
                    ['id' => $a->id, 'position' => 1],
                ],
            ])
            ->assertOk();

        $this->assertSame(0, $b->fresh()->position);
        $this->assertSame(1, $a->fresh()->position);
    }

    public function test_staff_cannot_manage_classifications(): void
    {
        $category = Category::factory()->create();

        $this->actingAs(User::factory()->staff()->create())
            ->get(route('admin.categories.index'))
            ->assertForbidden();

        $this->actingAs(User::factory()->staff()->create())
            ->delete(route('admin.categories.destroy', $category))
            ->assertForbidden();
    }

    public function test_a_classification_gets_a_fitting_icon_from_its_name(): void
    {
        $expectations = [
            'Fruits' => 'apple',
            'Vegetables' => 'carrot',
            'Salad & Greens' => 'carrot',
            'Seafood' => 'fish',
            'Beef' => 'meat',
            'Milk' => 'bottle',
            'Eggs' => 'egg',
            'Bread' => 'wheat',
            'Coffee & Tea' => 'coffee',
            'Biscuits & Cookies' => 'candy',
            'Cleaning Supplies' => 'droplet',
            'Baby Care' => 'droplet',
            'Frozen Foods' => 'ice-cream',
        ];

        foreach ($expectations as $name => $icon) {
            $category = Category::factory()->make(['name' => $name]);

            $this->assertSame($icon, $category->iconName(), $name.' should use the '.$icon.' mark');
        }
    }

    public function test_an_unknown_classification_falls_back_to_a_generic_mark(): void
    {
        $this->assertSame('basket', Category::factory()->make(['name' => 'Zzz Novelties'])->iconName());
    }

    public function test_every_guess_is_a_mark_that_actually_exists(): void
    {
        $names = [
            'Fruits', 'Vegetables', 'Salad & Greens', 'Herbs', 'Beef', 'Pork', 'Chicken', 'Seafood',
            'Milk', 'Cheese', 'Yogurt', 'Eggs', 'Bread', 'Pastries', 'Cakes', 'Rice & Grains',
            'Canned Goods', 'Pasta & Noodles', 'Oils & Vinegar', 'Spices', 'Water', 'Soft Drinks',
            'Juice', 'Coffee & Tea', 'Beer & Wine', 'Chips & Crisps', 'Chocolate', 'Biscuits & Cookies',
            'Nuts & Dried Fruit', 'Cleaning Supplies', 'Paper Goods', 'Laundry', 'Kitchenware',
            'Trash & Storage', 'Bath & Body', 'Oral Care', 'Hair Care', 'Baby Care', 'Frozen Foods',
            'Ready Meals', 'Something Entirely New',
        ];

        foreach ($names as $name) {
            $icon = Category::factory()->make(['name' => $name])->iconName();

            $this->assertContains(
                $icon,
                Category::ICONS,
                $name.' resolved to "'.$icon.'", which is not in the icon set'
            );
        }
    }

    public function test_a_stored_icon_always_wins_over_the_guess(): void
    {
        $category = Category::factory()->make(['name' => 'Fruits', 'icon' => 'carrot']);

        $this->assertSame('carrot', $category->iconName());
    }

    public function test_the_icon_can_be_chosen_in_the_admin_and_must_be_real(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->post(route('admin.categories.store'), [
                'name' => 'Fresh Produce',
                'icon' => 'carrot',
                'is_active' => '1',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('carrot', Category::where('name', 'Fresh Produce')->firstOrFail()->icon);

        $this->actingAs($manager)
            ->post(route('admin.categories.store'), [
                'name' => 'Bogus',
                'icon' => 'definitely-not-an-icon',
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('icon');
    }

    public function test_the_phone_menu_lists_every_classification_with_its_mark(): void
    {
        $root = Category::factory()->create(['name' => 'Fresh Produce', 'icon' => 'carrot']);
        Category::factory()->childOf($root)->create(['name' => 'Fruits', 'icon' => 'apple']);

        $html = $this->get(route('catalog.index'))->assertOk()->getContent();

        // The drawer is the phone menu, so it ships on every page.
        $this->assertStringContainsString('id="mobile-menu"', $html);
        $this->assertMatchesRegularExpression(
            '#id="mobile-menu".*?Fresh Produce.*?Fruits#s',
            $html,
            'the phone menu lists the department and its aisle'
        );
    }
}
