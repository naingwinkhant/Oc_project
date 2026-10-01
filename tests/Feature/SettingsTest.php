<?php

namespace Tests\Feature;

use App\History\ViewHistoryService;
use App\Models\Notice;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Support\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_settings_page_is_open_to_everyone(): void
    {
        $this->get(route('settings'))
            ->assertOk()
            ->assertSee('Appearance')
            ->assertSee('Store rules')
            ->assertSee('Notifications')
            ->assertSee('History');

        // Settings is opened from the header, so it offers a way back.
        $this->get(route('settings'))->assertSee('Back')->assertSee('data-back', false);
    }

    public function test_the_theme_is_applied_before_the_page_paints(): void
    {
        $html = $this->get(route('settings'))->assertOk()->getContent();

        // The class has to land on <html> before the body renders, otherwise a
        // night-mode shopper gets a white flash first.
        $this->assertStringContainsString("classList.toggle('dark'", $html);
        $this->assertLessThan(
            strpos($html, '<body'),
            strpos($html, "classList.toggle('dark'"),
            'the theme script must come before the body'
        );
    }

    public function test_a_saved_theme_is_handed_to_the_browser(): void
    {
        $user = User::factory()->create(['theme' => Theme::DARK]);

        $html = $this->actingAs($user)->get(route('catalog.index'))->assertOk()->getContent();

        $this->assertStringContainsString('"dark"', $html);
    }

    public function test_a_theme_can_be_saved_and_invalid_values_are_refused(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('settings.theme'), ['theme' => Theme::DARK])
            ->assertRedirect();

        $this->assertSame(Theme::DARK, $user->fresh()->theme);

        $this->actingAs($user)
            ->postJson(route('settings.theme'), ['theme' => 'neon'])
            ->assertStatus(422);

        $this->assertSame(Theme::DARK, $user->fresh()->theme, 'a bad value must not be stored');
    }

    public function test_the_settings_theme_toggle_is_marked_as_chosen(): void
    {
        $this->actingAs(User::factory()->create(['theme' => Theme::SYSTEM]))
            ->get(route('settings'))
            ->assertOk()
            ->assertSee('data-theme-choice', escape: false)
            ->assertSee('value="system" data-theme-choice', escape: false);
    }

    public function test_viewing_a_product_records_it_in_the_history(): void
    {
        $product = Product::factory()->create();

        $this->get(route('catalog.product', $product))->assertOk();

        $this->assertSame(
            [$product->id],
            app(ViewHistoryService::class)->products()->pluck('id')->all()
        );
    }

    public function test_the_history_is_newest_first_without_duplicates(): void
    {
        $first = Product::factory()->create();
        $second = Product::factory()->create();

        $this->get(route('catalog.product', $first));
        $this->get(route('catalog.product', $second));
        $this->get(route('catalog.product', $first));

        $this->assertSame(
            [$first->id, $second->id],
            app(ViewHistoryService::class)->products()->pluck('id')->all()
        );

        $this->assertSame(2, DB::table('product_views')->count());
    }

    public function test_the_history_can_be_cleared(): void
    {
        $this->get(route('catalog.product', Product::factory()->create()));

        $this->delete(route('history.clear'))->assertRedirect();

        $this->assertSame(0, app(ViewHistoryService::class)->count());
    }

    public function test_a_guest_history_moves_to_the_account_on_sign_in(): void
    {
        $product = Product::factory()->create();
        $user = User::factory()->create(['email' => 'thein@goldengate.test']);

        $this->get(route('catalog.product', $product));

        $this->post(route('login'), ['email' => 'thein@goldengate.test', 'password' => 'password']);

        $this->assertAuthenticated();
        $this->assertSame(0, DB::table('product_views')->whereNull('user_id')->count());
        $this->assertSame(1, DB::table('product_views')->where('user_id', $user->id)->count());

        // And the shopper now sees it under their account, not the guest token.
        $this->actingAs($user)
            ->get(route('history.index'))
            ->assertOk()
            ->assertSee($product->name);
    }

    public function test_the_history_page_never_lists_anybody_elses_order(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->post(route('checkout.store'), [
            'customer_name' => 'Aung Kyaw',
            'phone' => '09 380 000 00',
            'email' => 'aung@example.com',
            'delivery_address' => 'No. 12, Baho Road, Kamayut',
            'township' => 'Kamayut',
            'payment_gateway' => 'cash',
        ]);

        $number = Order::query()->firstOrFail()->order_number;

        // Nobody signs in to order, so an order is not owned by an account and
        // must not turn up on somebody's history page.
        $this->actingAs($user)
            ->get(route('history.index'))
            ->assertOk()
            ->assertDontSee($number)
            ->assertSee('Every order is in the dashboard');
    }

    public function test_a_guest_is_told_orders_need_no_account(): void
    {
        $this->get(route('history.index'))
            ->assertOk()
            ->assertSee('Orders need no account')
            ->assertSee('Browse the goods');
    }

    public function test_notices_are_offered_in_the_bell_and_nowhere_else(): void
    {
        Notice::create(['title' => 'Shop is open', 'body' => 'Come in.', 'is_active' => true, 'show_on_shop' => true]);
        Notice::create(['title' => 'Quiet notice', 'body' => 'Staff only.', 'is_active' => true, 'show_on_shop' => false]);
        Notice::create(['title' => 'Draft notice', 'body' => 'Nobody sees this.', 'is_active' => false]);

        $settings = $this->get(route('settings'))->assertOk();

        // The settings page points at the bell rather than listing notices itself.
        $settings->assertSee('Notifications')
            ->assertSee('Store announcements appear in the bell')
            ->assertSee('waiting');

        // One bell row, the shopper-facing notice. The internal notice and the
        // draft never make it into a shopper's bell.
        $html = $settings->getContent();

        $this->assertSame(1, substr_count($html, 'data-notification-item'));
        $this->assertStringContainsString('Shop is open', $html);
        $this->assertStringNotContainsString('Quiet notice', $html);
        $this->assertStringNotContainsString('Draft notice', $html);

        // The shop carries the same notice through the bell, and still has no
        // announcement strip of its own.
        $shop = $this->get(route('catalog.index'))->assertOk()->getContent();

        $this->assertStringContainsString('Shop is open', $shop);
        $this->assertStringNotContainsString('Quiet notice', $shop);
        $this->assertStringNotContainsString('Draft notice', $shop);
    }

    public function test_a_notice_outside_its_date_window_is_hidden(): void
    {
        Notice::create([
            'title' => 'Expired campaign',
            'body' => 'Last month.',
            'is_active' => true,
            'starts_on' => now()->subMonth()->toDateString(),
            'ends_on' => now()->subDay()->toDateString(),
        ]);

        $this->get(route('settings'))->assertOk()->assertDontSee('Expired campaign');
    }

    public function test_a_manager_can_publish_edit_and_remove_a_notice(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('admin.notices.index'))
            ->assertOk()
            ->assertSee('No notices yet');

        $this->actingAs($manager)
            ->post(route('admin.notices.store'), [
                'title' => 'Delivery hours changed',
                'body' => 'We now close at 9pm.',
                'tone' => 'warning',
                'is_active' => '1',
                'show_on_shop' => '1',
            ])
            ->assertRedirect(route('admin.notices.index'))
            ->assertSessionHas('success');

        $notice = Notice::query()->firstOrFail();

        $this->assertSame($manager->id, $notice->user_id);
        $this->assertTrue($notice->show_on_shop);

        $this->actingAs($manager)
            ->put(route('admin.notices.update', $notice), [
                'title' => 'Delivery hours back to normal',
                'body' => 'We are closing at 8pm again.',
                'tone' => 'info',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.notices.index'));

        $this->assertSame('Delivery hours back to normal', $notice->fresh()->title);

        $this->actingAs($manager)
            ->delete(route('admin.notices.destroy', $notice))
            ->assertRedirect(route('admin.notices.index'));

        $this->assertSame(0, Notice::query()->count());
    }

    public function test_a_notice_must_be_complete_and_its_window_sane(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->post(route('admin.notices.store'), ['title' => '', 'body' => '', 'tone' => 'neon'])
            ->assertSessionHasErrors(['title', 'body', 'tone']);

        $this->actingAs($manager)
            ->post(route('admin.notices.store'), [
                'title' => 'Backwards dates',
                'body' => 'Ends before it starts.',
                'tone' => 'brand',
                'starts_on' => now()->addWeek()->toDateString(),
                'ends_on' => now()->toDateString(),
            ])
            ->assertSessionHasErrors('ends_on');
    }

    public function test_staff_can_read_notices_but_not_write_them(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get(route('admin.notices.index'))->assertOk();
        $this->actingAs($staff)->get(route('admin.notices.create'))->assertForbidden();
        $this->actingAs($staff)->get(route('admin.notices.create'))->assertForbidden();

        $this->actingAs($staff)
            ->post(route('admin.notices.store'), [
                'title' => 'Not allowed',
                'body' => 'Should be refused.',
                'tone' => 'brand',
            ])
            ->assertForbidden();
    }

    public function test_the_store_rules_read_from_the_delivery_config(): void
    {
        $this->get(route('settings'))
            ->assertOk()
            ->assertSee('1,500')
            ->assertSee('Same day')
            ->assertSee('150,000')
            ->assertSee('KBZPay')
            ->assertDontSee('Sandbox (test)');
    }
}
