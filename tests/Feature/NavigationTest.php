<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse as TestResponseish;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    private function trail(string $html): array
    {
        if (! preg_match('#<nav[^>]*aria-label="Breadcrumb".*?</nav>#s', $html, $nav)) {
            return [];
        }

        preg_match_all('#<a\b[^>]*>(.*?)</a>|<span\b[^>]*>(.*?)</span>#s', $nav[0], $matches, PREG_SET_ORDER);

        $crumbs = [];

        foreach ($matches as $match) {
            $label = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($match[1] !== '' ? $match[1] : ($match[2] ?? '')))));

            if ($label !== '') {
                $crumbs[] = $label;
            }
        }

        return $crumbs;
    }

    private function assertTrail(TestResponseish $response, array $expected): void
    {
        $response->assertOk();
        $response->assertSee('aria-label="Breadcrumb"', escape: false);

        $this->assertSame($expected, $this->trail($response->getContent()), 'breadcrumb trail');
    }

    public function test_the_storefront_has_no_breadcrumb_row(): void
    {
        $root = Category::factory()->create(['name' => 'Fresh Produce']);
        $child = Category::factory()->childOf($root)->create(['name' => 'Fruits']);
        $product = Product::factory()->for($child)->create(['stock' => 5]);

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

        // The classification bar and the header already say where you are, so
        // the storefront deliberately carries no trail under the nav bar.
        $beforeTheOrder = [
            route('catalog.index'),
            route('catalog.new-arrivals'),
            route('catalog.show', $root),
            route('catalog.show', $child),
            route('catalog.product', $product),
            route('cart.index'),
            route('checkout.create'),
            route('favourites.index'),
            route('history.index'),
            route('settings'),
            route('services'),
            route('information'),
            route('login'),
        ];

        $this->assertNoBreadcrumbs($beforeTheOrder);

        $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road, Kamayut',
            'township' => 'Kamayut',
            'payment_gateway' => 'sandbox',
        ]);

        $order = Order::query()->firstOrFail();

        $this->assertNoBreadcrumbs([
            route('checkout.show', $order),
            route('payments.sandbox', $order),
        ]);
    }

    /**
     * @param  array<int, string>  $urls
     */
    private function assertNoBreadcrumbs(array $urls): void
    {
        foreach ($urls as $url) {
            $response = $this->get($url);

            $this->assertSame(200, $response->getStatusCode(), $url.' should render directly');
            $this->assertStringNotContainsString(
                'aria-label="Breadcrumb"',
                $response->getContent(),
                $url.' should not render a breadcrumb'
            );

            // Settings is the one storefront page with a back button, because it
            // is reached from the header rather than from a list.
            if (! str_contains($url, '/settings')) {
                $this->assertStringNotContainsString('data-back', $response->getContent(), $url.' has no back arrow');
            }
        }
    }

    public function test_every_admin_page_has_a_trail(): void
    {
        $user = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Bakery']);
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create(['name' => 'Sourdough Loaf']);
        $other = User::factory()->create(['name' => 'Jenna Cruz']);

        $buyer = Product::factory()->create(['stock' => 10]);
        $this->post(route('cart.store'), ['product_id' => $buyer->id, 'quantity' => 1]);
        $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road, Kamayut',
            'township' => 'Kamayut',
            'payment_gateway' => 'cash',
        ]);
        $number = Order::query()->firstOrFail()->order_number;

        $this->actingAs($user);

        $this->assertTrail($this->get(route('admin.dashboard')), ['Dashboard']);
        $this->assertTrail($this->get(route('admin.products.index')), ['Dashboard', 'Goods']);
        $this->assertTrail($this->get(route('admin.products.create')), ['Dashboard', 'Goods', 'Add goods']);
        $this->assertTrail($this->get(route('admin.products.edit', $product)), ['Dashboard', 'Goods', 'Sourdough Loaf']);
        $this->assertTrail($this->get(route('admin.categories.index')), ['Dashboard', 'Classifications']);
        $this->assertTrail($this->get(route('admin.categories.create')), ['Dashboard', 'Classifications', 'New classification']);
        $this->assertTrail($this->get(route('admin.categories.edit', $category)), ['Dashboard', 'Classifications', 'Bakery']);
        $this->assertTrail($this->get(route('admin.stock.index')), ['Dashboard', 'Stock movements']);
        $this->assertTrail($this->get(route('admin.stock.low')), ['Dashboard', 'Stock movements', 'Low stock']);
        $this->assertTrail($this->get(route('admin.orders.index')), ['Dashboard', 'Orders']);
        $this->assertTrail($this->get(route('admin.orders.show', Order::query()->firstOrFail())), ['Dashboard', 'Orders', $number]);
        $this->assertTrail($this->get(route('admin.suppliers.index')), ['Dashboard', 'Suppliers']);
        $this->assertTrail($this->get(route('admin.suppliers.create')), ['Dashboard', 'Suppliers', 'Add supplier']);
        $this->assertTrail($this->get(route('admin.suppliers.edit', $supplier)), ['Dashboard', 'Suppliers', $supplier->name]);
        $this->assertTrail($this->get(route('admin.users.index')), ['Dashboard', 'Users']);
        $this->assertTrail($this->get(route('admin.users.create')), ['Dashboard', 'Users', 'Add user']);
        $this->assertTrail($this->get(route('admin.users.edit', $other)), ['Dashboard', 'Users', 'Jenna Cruz']);
        $this->assertTrail($this->get(route('admin.activity.index')), ['Dashboard', 'Activity log']);
    }

    public function test_the_current_page_is_never_a_link(): void
    {
        $product = Product::factory()->create();

        $html = $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.products.edit', $product))
            ->assertOk()
            ->getContent();

        preg_match('#<nav[^>]*aria-label="Breadcrumb".*?</nav>#s', $html, $nav);

        $this->assertNotEmpty($nav, 'the admin page has a trail');

        // The final crumb carries no anchor at all.
        $this->assertStringNotContainsString(
            '<a href="'.route('admin.products.edit', $product).'"',
            $nav[0],
            'the current page must not be a link in its own trail'
        );
    }

    public function test_every_admin_page_offers_a_back_arrow(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('data-back', escape: false)
            ->assertSee('Go back', escape: false);
    }

    public function test_the_back_arrow_points_at_the_parent_when_there_is_no_history(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.products.create'))
            ->assertOk()
            // The crumb before the current page is the parent, so the arrow is
            // a plain link there.
            ->assertSee('href="'.route('admin.products.index').'" data-back', escape: false);
    }
}
