<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        return User::factory()->manager()->create();
    }

    public function test_the_supplier_pages_render(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Metro Fresh']);
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create(['name' => 'Sourdough Loaf']);

        $supplier->products()->attach($product->id, [
            'unit_cost' => 5200,
            'supplier_sku' => 'MF-8821',
        ]);

        $this->actingAs($this->manager())
            ->get(route('admin.suppliers.index'))
            ->assertOk()
            ->assertSee('Metro Fresh');

        $this->actingAs($this->manager())
            ->get(route('admin.suppliers.edit', $supplier))
            ->assertOk()
            ->assertSee('Metro Fresh');

        $this->actingAs($this->manager())
            ->get(route('admin.suppliers.create'))
            ->assertOk();

        $this->actingAs($this->manager())
            ->get(route('catalog.product', $product))
            ->assertOk();
    }

    public function test_the_pivot_records_when_a_link_was_made(): void
    {
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create();

        $supplier->products()->attach($product->id);

        // The relation declares withTimestamps(), so the pivot must have the
        // columns — reading a supplier's goods used to fail on them.
        $pivot = $supplier->products()->firstOrFail()->pivot;

        $this->assertNotNull($pivot->created_at);
        $this->assertNotNull($pivot->updated_at);
    }

    public function test_a_supplier_can_be_created_and_edited(): void
    {
        $this->actingAs($this->manager())
            ->post(route('admin.suppliers.store'), [
                'name' => 'Davao Fresh',
                'contact_name' => 'Maria Santos',
                'phone' => '09 380 000 44',
                'email' => 'orders@dayavofresh.test',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.suppliers.index'));

        $supplier = Supplier::query()->firstOrFail();

        $this->actingAs($this->manager())
            ->put(route('admin.suppliers.update', $supplier), [
                'name' => 'Davao Seafood',
                'contact_name' => 'Maria Santos',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.suppliers.index'));

        $this->assertSame('Davao Seafood', $supplier->fresh()->name);
    }
}
