<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\Role;
use App\Models\Notice;
use App\Models\Order;
use App\Models\Product;
use App\Models\TeamAlert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A new order reaches the team through the bell, and an order can be moved
 * along, corrected and — only while nothing has been charged for it — removed.
 */
class OrderTeamAlertsTest extends TestCase
{
    use RefreshDatabase;

    private function placeOrder(array $attributes = []): Order
    {
        $product = Product::factory()->create(['stock' => 20, 'price' => 2_000, 'sale_price' => null]);

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2])->assertRedirect();

        $this->post(route('checkout.store'), [
            'customer_name' => $attributes['customer_name'] ?? 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road, Kamayut',
            'township' => 'Kamayut',
            'payment_gateway' => 'cash',
            ...$attributes,
        ])->assertSessionHasNoErrors();

        return Order::query()->latest('id')->firstOrFail();
    }

    /*
    |--------------------------------------------------------------------------
    | The team hears about a new order
    |--------------------------------------------------------------------------
    */

    public function test_placing_an_order_raises_an_alert(): void
    {
        $order = $this->placeOrder();

        $this->assertDatabaseHas('team_alerts', [
            'kind' => 'order',
            'order_id' => $order->id,
            'title' => 'New order '.$order->order_number,
        ]);
    }

    /**
     * @dataProvider roles
     */
    public function test_the_alert_reaches_every_member_of_the_team(string $role): void
    {
        $this->placeOrder();

        $this->actingAs(User::factory()->create(['role' => $role]))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-notification-item="alert-', false)
            ->assertSee('New order');
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function roles(): array
    {
        return [
            'staff' => [Role::Staff->value],
            'manager' => [Role::Manager->value],
            'admin' => [Role::Admin->value],
        ];
    }

    public function test_a_shopper_is_never_shown_the_alert(): void
    {
        $this->placeOrder();

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertDontSee('data-notification-item="alert-', false)
            ->assertDontSee('New order');
    }

    public function test_one_person_clearing_an_alert_leaves_it_for_the_others(): void
    {
        $this->placeOrder();
        $alert = TeamAlert::query()->firstOrFail();

        $this->actingAs(User::factory()->staff()->create())
            ->post(route('team-alerts.dismiss', $alert))
            ->assertRedirect();

        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('New order');
    }

    public function test_a_guest_cannot_clear_an_alert(): void
    {
        $this->placeOrder();
        $alert = TeamAlert::query()->firstOrFail();

        $this->post(route('team-alerts.dismiss', $alert))->assertRedirect(route('login'));
    }

    public function test_the_bell_counts_notices_and_order_alerts_together(): void
    {
        $this->placeOrder();

        Notice::create([
            'title' => 'Free delivery over 150,000 Ks',
            'body' => 'Fill a big basket and we will carry it for nothing.',
            'is_active' => true,
            'show_on_shop' => true,
        ]);

        $staff = User::factory()->staff()->create();

        $html = $this->actingAs($staff)->get(route('admin.dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('data-notification-item="alert-', $html);
        $this->assertStringContainsString('data-notification-item="notice-', $html);
        // One notice plus one order alert.
        $this->assertSame(2, substr_count($html, 'data-notification-item='));
        $this->assertStringContainsString('>2<', $html);
    }

    public function test_clear_all_clears_both_halves_of_the_bell(): void
    {
        $this->placeOrder();

        Notice::create([
            'title' => 'Free delivery over 150,000 Ks',
            'body' => 'Fill a big basket and we will carry it for nothing.',
            'is_active' => true,
            'show_on_shop' => true,
        ]);

        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->delete(route('notifications.dismiss-all'))->assertRedirect();

        $this->assertDatabaseHas('notice_dismissals', ['user_id' => $staff->id]);
        $this->assertDatabaseHas('team_alert_dismissals', ['user_id' => $staff->id]);

        $this->actingAs($staff)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Nothing new');
    }

    public function test_a_shopper_clearing_the_bell_does_not_touch_team_alerts(): void
    {
        $this->placeOrder();

        // Guests only ever hold shopper notices, so there is nothing of theirs
        // to clear and the team's alert must be untouched.
        $this->delete(route('notifications.dismiss-all'))->assertRedirect();

        $this->assertDatabaseMissing('team_alert_dismissals', ['user_id' => null]);
    }

    /*
    |--------------------------------------------------------------------------
    | Completing an order
    |--------------------------------------------------------------------------
    */

    public function test_a_paid_order_can_be_completed(): void
    {
        $order = $this->placeOrder();
        $order->forceFill(['status' => OrderStatus::Paid, 'paid_at' => now()])->save();

        $this->actingAs(User::factory()->manager()->create())
            ->post(route('admin.orders.status', $order), ['status' => 'completed'])
            ->assertRedirect();

        $this->assertSame(OrderStatus::Completed, $order->fresh()->status);
    }

    public function test_a_closed_order_cannot_be_reopened(): void
    {
        $order = $this->placeOrder();

        foreach ([OrderStatus::Completed, OrderStatus::Cancelled, OrderStatus::Refunded] as $closed) {
            $order->forceFill(['status' => $closed])->save();

            $this->actingAs(User::factory()->manager()->create())
                ->from(route('admin.orders.show', $order))
                ->post(route('admin.orders.status', $order), ['status' => 'pending'])
                ->assertRedirect(route('admin.orders.show', $order))
                ->assertSessionHas('error');

            $this->assertSame($closed, $order->fresh()->status);
        }
    }

    public function test_an_unpaid_order_cannot_be_completed(): void
    {
        $order = $this->placeOrder();

        $this->actingAs(User::factory()->manager()->create())
            ->from(route('admin.orders.show', $order))
            ->post(route('admin.orders.status', $order), ['status' => 'completed'])
            ->assertSessionHas('error');

        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }

    public function test_completing_an_order_does_not_touch_stock_again(): void
    {
        $product = Product::factory()->create(['stock' => 10, 'price' => 2_000, 'sale_price' => null]);

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 3]);
        $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road',
            'township' => 'Kamayut',
            'payment_gateway' => 'sandbox',
        ])->assertSessionHasNoErrors();

        $order = Order::query()->latest('id')->firstOrFail();
        $order->forceFill(['status' => OrderStatus::Paid, 'paid_at' => now()])->save();

        $afterPayment = $product->fresh()->stock;

        $this->actingAs(User::factory()->manager()->create())
            ->post(route('admin.orders.status', $order), ['status' => 'completed']);

        // Stock already moved when the payment cleared, so completing is only a
        // handover: it must not deduct a second time.
        $this->assertSame($afterPayment, $product->fresh()->stock);
    }

    /*
    |--------------------------------------------------------------------------
    | Editing
    |--------------------------------------------------------------------------
    */

    public function test_delivery_details_can_be_corrected(): void
    {
        $order = $this->placeOrder();

        $this->actingAs(User::factory()->manager()->create())
            ->put(route('admin.orders.update', $order), [
                'customer_name' => 'Aung Kyaw Oo',
                'phone' => '09 512 345 67',
                'delivery_address' => 'No. 40, Baho Road, Kamayut',
                'township' => 'Hlaing',
            ])
            ->assertRedirect(route('admin.orders.show', $order));

        $order->refresh();

        $this->assertSame('Aung Kyaw Oo', $order->customer_name);
        $this->assertSame('Hlaing', $order->township);
    }

    public function test_a_township_outside_the_delivery_zones_is_refused(): void
    {
        $order = $this->placeOrder();

        $this->actingAs(User::factory()->manager()->create())
            ->put(route('admin.orders.update', $order), [
                'customer_name' => 'Aung Kyaw',
                'phone' => '09 380 000 00',
                'delivery_address' => 'No. 12, Baho Road',
                'township' => 'Nowhere',
            ])
            ->assertSessionHasErrors('township');
    }

    public function test_a_closed_order_cannot_be_edited(): void
    {
        $order = $this->placeOrder();
        $order->forceFill(['status' => OrderStatus::Completed])->save();

        $this->actingAs(User::factory()->manager()->create())
            ->from(route('admin.orders.show', $order))
            ->put(route('admin.orders.update', $order), [
                'customer_name' => 'Someone Else',
                'phone' => '09 380 000 00',
                'delivery_address' => 'Elsewhere',
                'township' => 'Hlaing',
            ])
            ->assertSessionHas('error');

        $this->assertSame('Aung Kyaw', $order->fresh()->customer_name);
    }

    public function test_the_edit_page_renders(): void
    {
        $order = $this->placeOrder();

        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.orders.edit', $order))
            ->assertOk()
            ->assertSee('Edit delivery details')
            ->assertSee('Kamayut');
    }

    /*
    |--------------------------------------------------------------------------
    | Deleting, with the guard that matters
    |--------------------------------------------------------------------------
    */

    public function test_an_unpaid_order_can_be_deleted(): void
    {
        $order = $this->placeOrder();

        $this->actingAs(User::factory()->manager()->create())
            ->delete(route('admin.orders.destroy', $order))
            ->assertRedirect();

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_a_paid_order_cannot_be_deleted(): void
    {
        $order = $this->placeOrder();
        $order->forceFill(['status' => OrderStatus::Paid, 'paid_at' => now()])->save();

        $this->actingAs(User::factory()->admin()->create())
            ->from(route('admin.orders.show', $order))
            ->delete(route('admin.orders.destroy', $order))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    public function test_a_completed_order_cannot_be_deleted(): void
    {
        $order = $this->placeOrder();
        $order->forceFill(['status' => OrderStatus::Completed, 'paid_at' => now()])->save();

        $this->actingAs(User::factory()->admin()->create())
            ->from(route('admin.orders.show', $order))
            ->delete(route('admin.orders.destroy', $order))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    public function test_staff_cannot_delete_an_order(): void
    {
        $order = $this->placeOrder();

        $this->actingAs(User::factory()->staff()->create())
            ->delete(route('admin.orders.destroy', $order))
            ->assertForbidden();

        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    public function test_the_delete_button_is_only_offered_to_those_who_may_use_it(): void
    {
        $order = $this->placeOrder();

        $this->actingAs(User::factory()->staff()->create())
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertDontSee('Delete order');

        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Delete order');
    }

    public function test_the_orders_list_offers_complete_and_delete_where_they_apply(): void
    {
        $order = $this->placeOrder();

        // Unpaid: it can be deleted, but not completed.
        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('Delete '.$order->order_number)
            ->assertDontSee('Mark '.$order->order_number.' as completed');

        $order->forceFill(['status' => OrderStatus::Paid, 'paid_at' => now()])->save();

        // Paid: now it can be completed, but never deleted.
        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('Mark '.$order->order_number.' as completed')
            ->assertDontSee('Delete '.$order->order_number);
    }

    public function test_the_orders_list_shows_no_actions_to_staff(): void
    {
        $this->placeOrder();

        $this->actingAs(User::factory()->staff()->create())
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertDontSee('Mark GGS-')
            ->assertDontSee('Delete GGS-');
    }

    /*
    |--------------------------------------------------------------------------
    | Acting on an order clears its alert
    |--------------------------------------------------------------------------
    */

    public function test_completing_an_order_clears_its_alert(): void
    {
        $order = $this->placeOrder();
        $order->forceFill(['status' => OrderStatus::Paid, 'paid_at' => now()])->save();

        $this->assertDatabaseHas('team_alerts', ['order_id' => $order->id]);

        $this->actingAs(User::factory()->manager()->create())
            ->post(route('admin.orders.status', $order), ['status' => 'completed'])
            ->assertRedirect();

        $this->assertDatabaseMissing('team_alerts', ['order_id' => $order->id]);

        // And the bell no longer carries it.
        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('New order '.$order->order_number);
    }

    public function test_cancelling_an_order_clears_its_alert(): void
    {
        $order = $this->placeOrder();

        $this->actingAs(User::factory()->manager()->create())
            ->post(route('admin.orders.status', $order), ['status' => 'cancelled'])
            ->assertRedirect();

        $this->assertDatabaseMissing('team_alerts', ['order_id' => $order->id]);
    }

    public function test_deleting_an_order_takes_its_alert_with_it(): void
    {
        $order = $this->placeOrder();
        $alertId = TeamAlert::query()->where('order_id', $order->id)->value('id');

        $this->assertNotNull($alertId);

        $this->actingAs(User::factory()->manager()->create())
            ->delete(route('admin.orders.destroy', $order))
            ->assertRedirect();

        // No orphaned alert left holding a link to an order that is gone.
        $this->assertDatabaseMissing('team_alerts', ['id' => $alertId]);
        $this->assertDatabaseMissing('team_alerts', ['order_id' => $order->id]);
    }

    public function test_clearing_an_orders_alert_clears_it_for_the_whole_team(): void
    {
        $order = $this->placeOrder();
        $order->forceFill(['status' => OrderStatus::Paid, 'paid_at' => now()])->save();

        $this->actingAs(User::factory()->manager()->create())
            ->post(route('admin.orders.status', $order), ['status' => 'completed']);

        // Whoever did it, nobody is still being told about it.
        foreach (['staff', 'manager', 'admin'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('admin.dashboard'))
                ->assertOk()
                ->assertDontSee('New order '.$order->order_number);
        }
    }

    public function test_the_orders_pill_drops_when_the_order_is_dealt_with(): void
    {
        $order = $this->placeOrder();
        $order->forceFill(['status' => OrderStatus::Paid, 'paid_at' => now()])->save();

        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-order-pill', false);

        $this->actingAs($manager)
            ->post(route('admin.orders.status', $order), ['status' => 'completed'])
            ->assertRedirect();

        $this->actingAs($manager)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('data-order-pill', false);
    }

    public function test_an_unhandled_order_keeps_its_alert(): void
    {
        $order = $this->placeOrder();

        // Marking it paid is not the same as dealing with it: it still needs
        // picking and packing, so the bell entry stays.
        $order->forceFill(['status' => OrderStatus::Paid, 'paid_at' => now()])->save();

        $this->assertDatabaseHas('team_alerts', ['order_id' => $order->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | The sidebar pill
    |--------------------------------------------------------------------------
    */

    public function test_the_orders_item_carries_a_count_of_unread_orders(): void
    {
        $this->placeOrder();
        $this->placeOrder();

        $this->actingAs(User::factory()->staff()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-order-pill', false)
            ->assertSee('>2<', false);
    }

    public function test_the_pill_caps_at_ninety_nine_plus(): void
    {
        // Raise 101 alerts without going near checkout, which is slower.
        TeamAlert::query()->count() >= 0 && TeamAlert::create([
            'kind' => 'order',
            'title' => 'x',
            'created_at' => now(),
        ]);
        $first = TeamAlert::query()->firstOrFail();

        foreach (range(1, 100) as $i) {
            TeamAlert::query()->create([
                'kind' => 'order',
                'title' => 'Order '.$i,
                'created_at' => now(),
            ]);
        }

        $this->assertGreaterThan(99, TeamAlert::query()->count());

        $html = $this->actingAs(User::factory()->staff()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('99+', $html);
        $this->assertNotSame('', $first->title);
    }

    public function test_the_pill_is_gone_once_the_orders_are_cleared(): void
    {
        $this->placeOrder();

        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get(route('admin.dashboard'))->assertOk()->assertSee('data-order-pill', false);

        $this->actingAs($staff)->delete(route('notifications.dismiss-all'))->assertRedirect();

        $this->actingAs($staff)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('data-order-pill', false);
    }
}
