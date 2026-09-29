<?php

namespace Tests\Feature;

use App\Cart\CartService;
use App\Enums\OrderStatus;
use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Payments\Gateways\SandboxGateway;
use App\Payments\PaymentManager;
use App\Support\Delivery;
use App\Support\Money;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CartAndCheckoutTest extends TestCase
{
    use RefreshDatabase;

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

    private function addToCart(Product $product, int $quantity = 1): TestResponse
    {
        return $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => $quantity,
        ])->assertSessionHasNoErrors();
    }

    public function test_money_formats_in_whole_kyat(): void
    {
        $this->assertSame('4,000 Ks', Money::format(4000));
        $this->assertSame('4,050 Ks', Money::format(4049));
        $this->assertSame('1.2M Ks', Money::compact(1_234_567));
        $this->assertSame('MMK', Money::code());
    }

    public function test_items_can_be_added_updated_and_removed(): void
    {
        $product = $this->sellable();

        $this->addToCart($product, 2)->assertSessionHas('status');

        $this->get(route('cart.index'))->assertOk()->assertSee($product->name);

        $this->patch(route('cart.update'), ['product_id' => $product->id, 'quantity' => 5]);
        $this->get(route('cart.index'))->assertSee('5');

        $this->delete(route('cart.destroy', $product->id));
        $this->get(route('cart.index'))->assertSee('Your cart is empty');
    }

    public function test_sale_price_is_used_for_line_totals(): void
    {
        $product = $this->sellable(['price' => 5000, 'sale_price' => 4000]);

        $this->addToCart($product, 2);

        $this->assertSame(8000, app(CartService::class)->subtotal());
    }

    public function test_expired_items_cannot_be_added(): void
    {
        $expired = $this->sellable(['produced_at' => now()->subMonths(2), 'expires_at' => now()->subDay()]);

        $this->post(route('cart.store'), ['product_id' => $expired->id, 'quantity' => 1])
            ->assertSessionHas('error');

        $this->assertSame(0, app(CartService::class)->count());
    }

    public function test_out_of_stock_items_cannot_be_added(): void
    {
        $empty = $this->sellable(['stock' => 0]);

        $this->post(route('cart.store'), ['product_id' => $empty->id, 'quantity' => 1])
            ->assertSessionHas('error');
    }

    public function test_an_expired_item_is_not_offered_for_purchase(): void
    {
        $expired = $this->sellable(['produced_at' => now()->subMonths(2), 'expires_at' => now()->subDay()]);

        $this->assertFalse($expired->isSellable());

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Expired')
            // No add-to-cart form anywhere on the card.
            ->assertDontSee('action="'.route('cart.store').'"', escape: false);

        $this->get(route('catalog.product', $expired))
            ->assertOk()
            ->assertSee('is no longer for sale')
            ->assertDontSee('action="'.route('cart.store').'"', escape: false);
    }

    public function test_an_item_that_expires_in_the_cart_blocks_checkout(): void
    {
        $product = $this->sellable();

        $this->addToCart($product);

        // The batch goes off while it sits in the basket.
        $product->forceFill(['expires_at' => now()->subDay()])->save();

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('can no longer be sold');

        $this->get(route('checkout.create'))->assertRedirect(route('cart.index'));

        $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road, Kamayut',
            'township' => 'Kamayut',
            'payment_gateway' => PaymentGateway::Cash->value,
        ])->assertRedirect(route('cart.index'));

        $this->assertSame(0, Order::query()->count());
    }

    public function test_expiring_soon_items_can_still_be_bought(): void
    {
        $product = $this->sellable(['expires_at' => now()->addDays(1)]);

        $this->assertTrue($product->isSellable());

        $this->addToCart($product)->assertSessionHas('status');

        $this->get(route('checkout.create'))->assertOk();
    }

    public function test_a_coming_soon_item_cannot_be_bought(): void
    {
        $product = $this->sellable(['available_from' => now()->addWeek()]);

        $this->assertTrue($product->isComingSoon());
        $this->assertFalse($product->isSellable());

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1])
            ->assertSessionHas('error');

        $this->assertSame(0, app(CartService::class)->count());
    }

    public function test_a_coming_soon_item_is_listed_but_has_no_buy_button(): void
    {
        $soon = $this->sellable(['name' => 'Mangoes (Carabao)', 'available_from' => now()->addDays(4)]);
        $today = $this->sellable(['name' => 'Fresh Basil', 'available_from' => now()]);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Mangoes (Carabao)')
            ->assertSee('Coming soon');

        $this->get(route('catalog.product', $soon))
            ->assertOk()
            ->assertSee('still on its way')
            ->assertDontSee('action="'.route('cart.store').'"', escape: false);

        // A batch that lands today is on sale straight away.
        $this->assertFalse($today->isComingSoon());
        $this->get(route('catalog.product', $today))
            ->assertOk()
            ->assertSee('action="'.route('cart.store').'"', escape: false);
    }

    public function test_a_batch_that_lands_while_it_sits_in_the_cart_blocks_checkout(): void
    {
        $product = $this->sellable(['available_from' => now()]);

        $this->addToCart($product);

        // Pushed back after the customer added it.
        $product->forceFill(['available_from' => now()->addDays(3)])->save();

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('can no longer be sold')
            ->assertSee('On the shelf from');

        $this->get(route('checkout.create'))->assertRedirect(route('cart.index'));
    }

    public function test_the_catalogue_can_filter_to_coming_soon_items(): void
    {
        $soon = $this->sellable(['name' => 'Durian Monthong', 'available_from' => now()->addDays(9)]);
        $this->sellable(['name' => 'Ordinary Item']);

        $this->get(route('catalog.index', ['coming_soon' => 1]))
            ->assertOk()
            ->assertSee('Durian Monthong')
            ->assertDontSee('Ordinary Item');
    }

    public function test_the_admin_can_filter_by_coming_soon(): void
    {
        $user = User::factory()->manager()->create();
        $this->sellable(['name' => 'Starfruit Boxes', 'available_from' => now()->addDays(6)]);
        $this->sellable(['name' => 'Shelf Stable Item']);

        $this->actingAs($user)
            ->get(route('admin.products.index', ['status' => 'coming_soon']))
            ->assertOk()
            ->assertSee('Starfruit Boxes')
            ->assertDontSee('Shelf Stable Item');
    }

    public function test_the_admin_can_save_an_available_from_date(): void
    {
        $user = User::factory()->manager()->create();

        $this->actingAs($user)
            ->post(route('admin.products.store'), [
                'name' => 'Mangoes (Carabao)',
                'unit' => 'kg',
                'price' => 6000,
                'stock' => 0,
                'available_from' => now()->addDays(10)->toDateString(),
                'is_active' => '1',
            ])
            ->assertSessionHasNoErrors();

        $product = Product::where('name', 'Mangoes (Carabao)')->firstOrFail();

        $this->assertTrue($product->isComingSoon());
        $this->assertFalse($product->isSellable());
    }

    public function test_checkout_redirects_when_the_cart_is_empty(): void
    {
        $this->get(route('checkout.create'))->assertRedirect(route('cart.index'));
    }

    public function test_delivery_fee_depends_on_the_township(): void
    {
        $this->assertSame(1500, Delivery::feeFor('Kamayut'));
        $this->assertSame(2500, Delivery::feeFor('Thanlyin'));
        $this->assertSame(5000, Delivery::feeFor('Mandalay'));

        $this->assertGreaterThan(Delivery::feeFor('Kamayut'), Delivery::feeFor('Mandalay'));
        $this->assertSame('Same day', Delivery::etaFor('Kamayut'));
        $this->assertSame('2–4 days', Delivery::etaFor('Mandalay'));
    }

    public function test_delivery_is_free_above_the_threshold(): void
    {
        $threshold = (int) config('shop.delivery.free_over');

        $this->assertSame(0, Delivery::feeFor('Mandalay', $threshold));
        $this->assertGreaterThan(0, Delivery::feeFor('Mandalay', $threshold - 1));
    }

    public function test_checkout_creates_an_order_with_the_zoned_delivery_fee(): void
    {
        $this->withoutExceptionHandling();

        $product = $this->sellable(['price' => 5000, 'stock' => 10]);
        $this->addToCart($product, 2);

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road, Kamayut',
            'township' => 'Mandalay',
            'payment_gateway' => PaymentGateway::Sandbox->value,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertCount(1, Order::query()->get(), 'status='.$response->getStatusCode().' location='.($response->headers->get('Location') ?? 'none').' errors='.json_encode(session('errors')?->toArray()));
        $order = Order::query()->firstOrFail();

        $response->assertRedirect(route('checkout.show', $order));

        $this->assertSame(10_000, $order->subtotal);
        $this->assertSame(5000, $order->delivery_fee);
        $this->assertSame(15_000, $order->total);
        $this->assertSame('upcountry', $order->delivery_zone);
        $this->assertSame('Aung Kyaw', $order->customer_name);
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertCount(1, $order->items);
        $this->assertSame(5000, $order->items->first()->unit_price);
    }

    public function test_checkout_rejects_a_township_we_do_not_deliver_to(): void
    {
        $this->addToCart($this->sellable());

        $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'Somewhere',
            'township' => 'Hpa-an',
            'payment_gateway' => PaymentGateway::Cash->value,
        ])->assertSessionHasErrors('township');

        $this->assertSame(0, Order::query()->count());
    }

    public function test_checkout_requires_a_username_signed_in_order(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->addToCart($this->sellable());

        $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road, Kamayut',
            'township' => 'Kamayut',
            'payment_gateway' => PaymentGateway::Sandbox->value,
        ])->assertRedirect();

        $this->assertSame($user->id, Order::query()->firstOrFail()->user_id);
    }

    public function test_sandbox_payment_settles_the_order_and_moves_stock(): void
    {
        $product = $this->sellable(['price' => 5000, 'stock' => 10]);
        $this->addToCart($product, 2);

        $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road, Kamayut',
            'township' => 'Kamayut',
            'payment_gateway' => PaymentGateway::Sandbox->value,
        ]);

        $order = Order::query()->firstOrFail();

        $this->get(route('payments.sandbox', $order))->assertOk()->assertSee('Simulated');

        $signature = hash_hmac('sha256', $order->order_number, (string) config('app.key'));

        $this->post(route('payments.sandbox.settle', $order), [
            'signature' => $signature,
            'outcome' => 'paid',
        ])->assertRedirect(route('checkout.show', $order));

        $order->refresh();

        $this->assertSame(OrderStatus::Paid, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame(PaymentStatus::Paid, $order->latestPayment->status);

        // Stock only moves once the money is confirmed.
        $this->assertSame(8, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => -2,
        ]);
    }

    public function test_reloading_the_order_page_does_not_mint_a_second_payment_intent(): void
    {
        $this->addToCart($this->sellable(['price' => 5000]));

        $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road, Kamayut',
            'township' => 'Kamayut',
            'payment_gateway' => PaymentGateway::Sandbox->value,
        ]);

        $order = Order::query()->firstOrFail();

        $this->get(route('checkout.show', $order))->assertOk();
        $this->get(route('checkout.show', $order))->assertOk();
        $this->get(route('checkout.show', $order))
            ->assertOk()
            ->assertSee('Continue to the payment page');

        $this->assertSame(1, $order->payments()->count());
        $this->assertSame(PaymentStatus::Pending, $order->latestPayment->status);
    }

    public function test_a_forged_sandbox_signature_is_rejected(): void
    {
        $product = $this->sellable();
        $this->addToCart($product);

        $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road, Kamayut',
            'township' => 'Kamayut',
            'payment_gateway' => PaymentGateway::Sandbox->value,
        ]);

        $order = Order::query()->firstOrFail();

        $this->post(route('payments.sandbox.settle', $order), [
            'signature' => 'forged',
            'outcome' => 'paid',
        ])->assertSessionHas('error');

        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
        $this->assertSame(20, $product->fresh()->stock);
    }

    public function test_a_declined_payment_leaves_stock_alone(): void
    {
        $product = $this->sellable();
        $this->addToCart($product);

        $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road, Kamayut',
            'township' => 'Kamayut',
            'payment_gateway' => PaymentGateway::Sandbox->value,
        ]);

        $order = Order::query()->firstOrFail();

        $this->post(route('payments.sandbox.settle', $order), [
            'signature' => hash_hmac('sha256', $order->order_number, (string) config('app.key')),
            'outcome' => 'failed',
        ]);

        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
        $this->assertSame(20, $product->fresh()->stock);
    }

    public function test_cash_orders_need_no_gateway(): void
    {
        $this->addToCart($this->sellable());

        $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road, Kamayut',
            'township' => 'Kamayut',
            'payment_gateway' => PaymentGateway::Cash->value,
        ]);

        $this->get(route('checkout.show', Order::query()->firstOrFail()))
            ->assertOk()
            ->assertSee('Pay the rider in cash');
    }

    public function test_a_customer_cannot_open_someone_elses_order(): void
    {
        $product = $this->sellable();
        $this->actingAs(User::factory()->create());
        $this->addToCart($product);

        $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road, Kamayut',
            'township' => 'Kamayut',
            'payment_gateway' => PaymentGateway::Sandbox->value,
        ]);

        $order = Order::query()->firstOrFail();

        $this->actingAs(User::factory()->create())
            ->get(route('checkout.show', $order))
            ->assertForbidden();
    }

    public function test_live_gateways_stay_hidden_until_they_have_credentials(): void
    {
        config()->set('shop.payments.kbzpay.enabled', true);
        config()->set('shop.payments.kbzpay.merchant_code', 'M1');
        config()->set('shop.payments.kbzpay.app_id', 'A1');
        config()->set('shop.payments.kbzpay.secret', '');

        $available = app(PaymentManager::class)->available();

        // Incomplete credentials must not surface a broken option at the till.
        $this->assertNotContains(PaymentGateway::KbzPay, $available);
        $this->assertContains(PaymentGateway::Cash, $available);
        $this->assertContains(PaymentGateway::Sandbox, $available);
    }

    public function test_the_checkout_page_renders_with_a_delivery_quote(): void
    {
        $this->addToCart(Product::factory()->create(['price' => 12000]));

        $this->get(route('checkout.create'))
            ->assertOk()
            ->assertSee('Delivery details')
            ->assertSee('Payment method')
            // The cheapest zone is preselected, so the JS can quote a range.
            ->assertSee('data-map=', escape: false)
            ->assertSee('Kamayut');
    }

    public function test_the_checkout_page_sends_an_empty_cart_back_to_the_cart(): void
    {
        $this->get(route('checkout.create'))->assertRedirect(route('cart.index'));
    }

    public function test_sandbox_gateway_signs_its_own_callbacks(): void
    {
        $gateway = app(SandboxGateway::class);

        $this->assertSame(
            'GGS-123',
            $gateway->verifyCallback([
                'order' => 'GGS-123',
                'signature' => hash_hmac('sha256', 'GGS-123', (string) config('app.key')),
            ])
        );

        $this->assertNull($gateway->verifyCallback(['order' => 'GGS-123', 'signature' => 'nope']));
    }
}
