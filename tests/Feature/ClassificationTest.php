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
}
