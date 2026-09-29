<?php

namespace Tests\Feature;

use App\Enums\PaymentGateway;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFreshnessTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        return User::factory()->manager()->create();
    }

    private function store(array $overrides = [])
    {
        return $this->actingAs($this->manager())->post(route('admin.products.store'), array_merge([
            'name' => 'Seasonal Item',
            'unit' => 'kg',
            'price' => 4000,
            'stock' => 10,
            'is_active' => '1',
        ], $overrides));
    }

    private function sellable(array $overrides = []): Product
    {
        return Product::factory()->create(array_merge([
            'price' => 4000,
            'stock' => 20,
            'is_active' => true,
            'produced_at' => now()->subDay(),
            'expires_at' => now()->addMonths(3),
        ], $overrides));
    }

    public function test_dates_can_be_saved_and_updated(): void
    {
        $this->store([
            'produced_at' => now()->subDays(10)->toDateString(),
            'expires_at' => now()->addDays(20)->toDateString(),
        ])->assertSessionHasNoErrors();

        $product = Product::query()->firstOrFail();

        $this->assertSame(now()->subDays(10)->toDateString(), $product->produced_at->toDateString());
        $this->assertSame(now()->addDays(20)->toDateString(), $product->expires_at->toDateString());
        $this->assertSame(30, $product->shelfLifeDays());

        $this->actingAs($this->manager())
            ->put(route('admin.products.update', $product), [
                'username' => $product->sku,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit' => $product->unit,
                'price' => 4000,
                'produced_at' => now()->subDays(2)->toDateString(),
                'expires_at' => now()->addDays(60)->toDateString(),
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(now()->subDays(2)->toDateString(), $product->fresh()->produced_at->toDateString());
    }

    public function test_expiry_must_not_precede_the_production_date(): void
    {
        $this->store([
            'produced_at' => now()->toDateString(),
            'expires_at' => now()->subDay()->toDateString(),
        ])->assertSessionHasErrors('expires_at');

        $this->assertSame(0, Product::query()->count());
    }

    public function test_production_date_cannot_be_in_the_future(): void
    {
        $this->store([
            'produced_at' => now()->addWeek()->toDateString(),
            'expires_at' => now()->addMonth()->toDateString(),
        ])->assertSessionHasErrors('produced_at');
    }

    public function test_freshness_status_is_derived_from_the_expiry_date(): void
    {
        $expired = Product::factory()->create(['produced_at' => now()->subMonths(2), 'expires_at' => now()->subDay()]);
        $expiring = Product::factory()->create(['produced_at' => now()->subMonth(), 'expires_at' => now()->addDays(10)]);
        $fresh = Product::factory()->create(['produced_at' => now()->subDay(), 'expires_at' => now()->addMonths(6)]);
        $undated = Product::factory()->create(['produced_at' => null, 'expires_at' => null]);

        $this->assertTrue($expired->isExpired());
        $this->assertSame('expired', $expired->expiryStatus());

        $this->assertFalse($expiring->isExpired());
        $this->assertSame('expiring', $expiring->expiryStatus());
        $this->assertSame(10, $expiring->daysUntilExpiry());

        $this->assertSame('fresh', $fresh->expiryStatus());
        $this->assertSame('unknown', $undated->expiryStatus());
        $this->assertNull($undated->daysUntilExpiry());
    }

    public function test_expiry_scopes_separate_expired_from_expiring(): void
    {
        Product::factory()->create(['expires_at' => now()->subDay()]);
        Product::factory()->create(['expires_at' => now()->addDays(5)]);
        Product::factory()->create(['expires_at' => now()->addYears(2)]);
        Product::factory()->create(['expires_at' => null]);

        $this->assertSame(1, Product::query()->expired()->count());
        $this->assertSame(1, Product::query()->expiringSoon()->count());
    }

    public function test_the_goods_list_can_be_filtered_by_freshness(): void
    {
        Product::factory()->create(['name' => 'Gone Bad', 'expires_at' => now()->subDay()]);
        Product::factory()->create(['name' => 'Use Soon', 'expires_at' => now()->addDays(4)]);
        Product::factory()->create(['name' => 'Still Fine', 'expires_at' => now()->addYear()]);

        $this->actingAs($this->manager())
            ->get(route('admin.products.index', ['status' => 'expired']))
            ->assertOk()
            ->assertSee('Gone Bad')
            ->assertDontSee('Still Fine');

        $this->actingAs($this->manager())
            ->get(route('admin.products.index', ['status' => 'expiring']))
            ->assertOk()
            ->assertSee('Use Soon');
    }

    public function test_the_product_page_shows_the_dates(): void
    {
        $product = Product::factory()->create([
            'name' => 'Whole Milk',
            'produced_at' => now()->subDays(3),
            'expires_at' => now()->addDays(11),
        ]);

        $this->get(route('catalog.product', $product))
            ->assertOk()
            ->assertSee('Freshness')
            ->assertSee('Packed')
            ->assertSee($product->produced_at->format('j M Y'))
            ->assertSee('Best before')
            ->assertSee($product->expires_at->format('j M Y'))
            ->assertSee('14 days shelf life')
            ->assertSee('11 days left');
    }

    public function test_an_expired_batch_is_described_in_words(): void
    {
        $product = Product::factory()->create([
            'produced_at' => now()->subDays(40),
            'expires_at' => now()->subDays(3),
        ]);

        $this->get(route('catalog.product', $product))
            ->assertOk()
            ->assertSee('Packed')
            ->assertSee('Expired 3 days ago');

        $this->assertSame('Expired 3 days ago', $product->freshness()['remaining']);
    }

    public function test_a_batch_that_expires_today_says_so(): void
    {
        $product = Product::factory()->create(['expires_at' => now()]);

        $this->assertSame('Expires today', $product->freshness()['remaining']);
        $this->get(route('catalog.product', $product))->assertOk()->assertSee('Expires today');
    }

    public function test_one_day_and_one_year_read_naturally(): void
    {
        $single = Product::factory()->make([
            'produced_at' => now(),
            'expires_at' => now()->addDay(),
        ]);

        $this->assertSame('1 day shelf life', $single->freshness()['shelf_life']);
        $this->assertSame('1 day left', $single->freshness()['remaining']);

        $year = Product::factory()->make([
            'produced_at' => now()->subDays(200),
            'expires_at' => now()->addDays(165),
        ]);

        $this->assertSame('365 days shelf life', $year->freshness()['shelf_life']);
    }

    public function test_a_batch_with_no_dates_says_so_instead_of_guessing(): void
    {
        $product = Product::factory()->make(['produced_at' => null, 'expires_at' => null]);

        $this->assertSame('No expiry date recorded', $product->freshness()['note']);
        $this->assertNull($product->freshnessLine());
    }

    public function test_a_coming_soon_batch_leads_with_its_shelf_date(): void
    {
        $product = Product::factory()->make(['available_from' => now()->addDays(5)]);

        $this->assertSame('On the shelf '.$product->available_from->format('j M Y'), $product->freshness()['note']);
        $this->assertSame($product->freshness()['note'], $product->freshnessLine());
    }

    public function test_the_wording_reaches_every_page_that_lists_an_item(): void
    {
        $product = $this->sellable(['name' => 'Yoghurt 1kg', 'expires_at' => now()->addDays(9)]);
        $line = $product->freshnessLine();

        $this->get(route('catalog.index'))->assertOk()->assertSee($line);
        $this->get(route('catalog.product', $product))
            ->assertOk()
            ->assertSee('Best before '.$product->expires_at->format('j M Y'))
            ->assertSee('9 days left');

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->get(route('cart.index'))->assertOk()->assertSee($line);
        $this->get(route('checkout.create'))->assertOk()->assertSee($line);

        $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road, Kamayut',
            'township' => 'Kamayut',
            'payment_gateway' => PaymentGateway::Cash->value,
        ])->assertRedirect();

        $order = Order::query()->firstOrFail();

        $this->get(route('checkout.show', $order))->assertOk()->assertSee($line);

        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee($line);

        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee($line);
    }

    public function test_a_sale_price_must_be_below_the_list_price(): void
    {
        $this->store(['price' => 4000, 'sale_price' => 4500])
            ->assertSessionHasErrors('sale_price');

        $this->assertSame(0, Product::query()->count());
    }

    public function test_a_sale_price_changes_what_the_customer_pays(): void
    {
        $this->store(['price' => 5000, 'sale_price' => 4000])->assertSessionHasNoErrors();

        $product = Product::query()->firstOrFail();

        $this->assertTrue($product->hasDiscount());
        $this->assertSame(4000, $product->effectivePrice());
        $this->assertSame(5000, $product->listPrice());
        $this->assertSame(20, $product->discountPercent());
        $this->assertSame(1000, $product->discountAmount());
    }

    public function test_a_sale_price_above_the_list_price_is_ignored(): void
    {
        $product = Product::factory()->create(['price' => 3000, 'sale_price' => 9000]);

        $this->assertFalse($product->hasDiscount());
        $this->assertSame(3000, $product->effectivePrice());
        $this->assertSame(0, $product->discountPercent());
    }

    public function test_the_catalogue_renders_the_discounted_price(): void
    {
        Product::factory()->create([
            'name' => 'Promo Bag',
            'price' => 5000,
            'sale_price' => 4000,
            'category_id' => Category::factory()->create()->id,
        ]);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('4,000 Ks')
            ->assertSee('5,000 Ks')
            ->assertSee('Promo Bag');
    }

    public function test_new_arrivals_page_lists_flagged_products_newest_first(): void
    {
        $old = Product::factory()->newArrival()->create([
            'name' => 'Older Arrival',
            'created_at' => now()->subDays(20),
        ]);

        $fresh = Product::factory()->newArrival()->create([
            'name' => 'Brand New',
            'created_at' => now()->subDay(),
        ]);

        Product::factory()->create(['name' => 'Regular Stock']);

        $response = $this->get(route('catalog.new-arrivals'))->assertOk();

        $response->assertSee('New arrivals');
        $response->assertSee('Brand New');
        $response->assertSee('Older Arrival');
        $response->assertDontSee('Regular Stock');

        // Newest first, so the fresh arrival appears before the older one.
        $this->assertLessThan(strpos($response->getContent(), 'Older Arrival'), strpos($response->getContent(), 'Brand New'));
    }

    public function test_new_arrival_flag_can_be_toggled(): void
    {
        $this->store(['is_new' => '1'])->assertSessionHasNoErrors();

        $this->assertTrue(Product::query()->firstOrFail()->isNewArrival());

        $product = Product::query()->firstOrFail();

        $this->actingAs($this->manager())
            ->put(route('admin.products.update', $product), [
                'name' => $product->name,
                'sku' => $product->sku,
                'unit' => $product->unit,
                'price' => 4000,
            ])
            ->assertSessionHasNoErrors();

        $this->assertFalse($product->fresh()->isNewArrival());
    }
}
