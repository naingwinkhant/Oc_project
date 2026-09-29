<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        return User::factory()->manager()->create();
    }

    public function test_manager_sees_the_product_list(): void
    {
        Product::factory()->count(3)->create();

        $this->actingAs($this->manager())
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Goods');
    }

    public function test_products_can_be_filtered_by_status_and_classification(): void
    {
        $category = Category::factory()->create(['name' => 'Beverages']);
        Product::factory()->for($category)->create(['name' => 'Cola Bottle']);
        Product::factory()->outOfStock()->create(['name' => 'Empty Jar']);

        $this->actingAs($this->manager())
            ->get(route('admin.products.index', ['status' => 'out']))
            ->assertOk()
            ->assertSee('Empty Jar')
            ->assertDontSee('Cola Bottle');

        $this->actingAs($this->manager())
            ->get(route('admin.products.index', ['category' => $category->id]))
            ->assertOk()
            ->assertSee('Cola Bottle');
    }

    public function test_staff_cannot_create_products(): void
    {
        $this->actingAs(User::factory()->staff()->create())
            ->get(route('admin.products.create'))
            ->assertForbidden();
    }

    public function test_product_can_be_created_with_generated_sku_and_slug(): void
    {
        $response = $this->actingAs($this->manager())
            ->post(route('admin.products.store'), [
                'name' => 'Organic Carrots 1kg',
                'unit' => 'kg',
                'price' => 5850,
                'cost_price' => 4000,
                'stock' => 120,
                'min_stock' => 20,
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Organic Carrots 1kg')->firstOrFail();

        $this->assertSame('organic-carrots-1kg', $product->slug);
        $this->assertStringStartsWith('SKU-', $product->sku);
        $this->assertTrue($product->is_active);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'created',
            'subject_id' => $product->id,
        ]);
    }

    public function test_product_creation_validates_required_fields(): void
    {
        $this->actingAs($this->manager())
            ->post(route('admin.products.store'), ['name' => '', 'unit' => '', 'price' => -1])
            ->assertSessionHasErrors(['name', 'unit', 'price']);
    }

    public function test_duplicate_sku_is_rejected(): void
    {
        Product::factory()->create(['sku' => 'SKU-DUPE']);

        $this->actingAs($this->manager())
            ->post(route('admin.products.store'), [
                'name' => 'Another Item',
                'sku' => 'SKU-DUPE',
                'unit' => 'pcs',
                'price' => 1000,
            ])
            ->assertSessionHasErrors('sku');
    }

    public function test_product_can_be_updated_with_image_replacement(): void
    {
        Storage::fake('public');
        $product = Product::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->manager())
            ->put(route('admin.products.update', $product), [
                'name' => 'New Name',
                'sku' => $product->sku,
                'unit' => 'kg',
                'price' => 9900,
                'cost_price' => 6200,
                'is_active' => '1',
                'image' => UploadedFile::fake()->create('goods.jpg', 64, 'image/jpeg'),
            ])
            ->assertRedirect(route('admin.products.index'));

        $product->refresh();

        $this->assertSame('New Name', $product->name);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_product_can_be_soft_deleted(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->manager())
            ->delete(route('admin.products.destroy', $product))
            ->assertSessionHas('success');

        $this->assertSoftDeleted($product);
    }

    public function test_a_sale_price_above_the_list_price_is_rejected(): void
    {
        $this->actingAs($this->manager())
            ->post(route('admin.products.store'), [
                'name' => 'Bad Promotion',
                'unit' => 'pcs',
                'price' => 5000,
                'cost_price' => 3000,
                'sale_price' => 6000,
            ])
            ->assertSessionHasErrors('sale_price');
    }

    public function test_a_sale_price_below_the_cost_price_is_rejected(): void
    {
        $this->actingAs($this->manager())
            ->post(route('admin.products.store'), [
                'name' => 'Loss Leader',
                'unit' => 'pcs',
                'price' => 5000,
                'cost_price' => 4000,
                'sale_price' => 3500,
            ])
            ->assertSessionHasErrors('sale_price');

        $this->assertDatabaseMissing('products', ['name' => 'Loss Leader']);
    }

    public function test_a_sale_price_above_cost_is_accepted(): void
    {
        $this->actingAs($this->manager())
            ->post(route('admin.products.store'), [
                'name' => 'Weekly Special',
                'unit' => 'pcs',
                'price' => 5000,
                'cost_price' => 4000,
                'sale_price' => 4500,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', [
            'name' => 'Weekly Special',
            'sale_price' => 4500,
        ]);
    }
}
