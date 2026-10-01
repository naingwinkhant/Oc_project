<?php

namespace Tests\Feature;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private function notice(array $attributes = []): Notice
    {
        return Notice::create([
            'title' => 'Same-day delivery inside Yangon',
            'body' => 'Order before 2pm and your goods reach the inner township the same day.',
            'tone' => 'info',
            'is_active' => true,
            'show_on_shop' => true,
            ...$attributes,
        ]);
    }

    public function test_the_bell_sits_next_to_settings_and_shows_live_notices(): void
    {
        $this->notice();

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('data-notification-badge', false)
            ->assertSee('Same-day delivery inside Yangon')
            ->assertSee(route('settings'), false);
    }

    public function test_notices_are_only_in_the_bell_and_not_on_the_page(): void
    {
        $this->notice(['title' => 'Bell only announcement']);

        // One bell row, and the title turns up nowhere else: the shop itself
        // carries no announcement strip of its own.
        $shop = $this->get(route('catalog.index'))->assertOk();

        $this->assertSame(1, substr_count($shop->getContent(), 'data-notification-item'));
        $shop->assertSee('Bell only announcement')
            ->assertDontSee('Store announcements appear in the bell');
    }

    public function test_expired_notices_stay_out_of_the_bell(): void
    {
        $this->notice(['title' => 'Finished campaign', 'ends_on' => now()->subDay()->toDateString()]);

        $this->get(route('catalog.index'))->assertOk()->assertDontSee('Finished campaign');
    }

    public function test_a_shopper_can_dismiss_one_notice_and_it_stays_gone(): void
    {
        $keep = $this->notice(['title' => 'Keep me']);
        $drop = $this->notice(['title' => 'Clear me']);

        $this->post(route('notifications.dismiss', $drop))->assertRedirect();

        $this->assertDatabaseHas('notice_dismissals', ['notice_id' => $drop->id, 'user_id' => null]);
        $this->assertDatabaseMissing('notice_dismissals', ['notice_id' => $keep->id]);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertDontSee('Clear me')
            ->assertSee('Keep me');
    }

    public function test_clear_all_empties_the_bell(): void
    {
        $this->notice(['title' => 'One']);
        $this->notice(['title' => 'Two']);

        $this->delete(route('notifications.dismiss-all'))->assertRedirect();

        $this->get(route('catalog.index'))->assertOk()->assertDontSee('>One<')->assertSee('Nothing new');
    }

    public function test_the_bell_answers_fetch_with_the_new_badge(): void
    {
        $this->notice(['title' => 'One']);
        $drop = $this->notice(['title' => 'Two']);

        $this->postJson(route('notifications.dismiss', $drop))
            ->assertOk()
            ->assertJson(['ok' => true, 'badge' => '1']);
    }

    public function test_the_badge_says_99_plus_past_ninety_nine(): void
    {
        foreach (range(1, 101) as $index) {
            $this->notice(['title' => 'Notice '.$index]);
        }

        $this->get(route('catalog.index'))->assertOk()->assertSee('99+');
    }

    public function test_one_hundred_and_one_notices_do_not_grow_the_page_endlessly(): void
    {
        foreach (range(1, 250) as $index) {
            $this->notice(['title' => 'Notice '.$index]);
        }

        // The bell is capped, so a long backlog cannot slow a page down.
        $this->assertLessThanOrEqual(
            100,
            substr_count($this->get(route('catalog.index'))->assertOk()->getContent(), 'data-notification-item')
        );
    }

    public function test_a_guest_dismissal_follows_the_account_after_signing_in(): void
    {
        $drop = $this->notice(['title' => 'Already read']);
        $this->notice(['title' => 'Still unread']);

        $this->post(route('notifications.dismiss', $drop))->assertRedirect();

        $user = User::factory()->create(['email' => 'ma_ma@goldengate.test']);

        // Signed in the real way, so the merge that login performs actually runs.
        $this->post(route('login'), [
            'email' => 'ma_ma@goldengate.test',
            'password' => 'password',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('notice_dismissals', [
            'notice_id' => $drop->id,
            'user_id' => $user->id,
        ]);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertDontSee('Already read')
            ->assertSee('Still unread');
    }

    public function test_one_shopper_clearing_a_notice_does_not_clear_it_for_everybody(): void
    {
        $notice = $this->notice(['title' => 'Shared notice']);

        $this->post(route('notifications.dismiss', $notice))->assertRedirect();

        $this->actingAs(User::factory()->create())
            ->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Shared notice');
    }

    public function test_staff_also_see_internal_notices(): void
    {
        $this->notice(['title' => 'Internal only', 'show_on_shop' => false]);

        // Guest first: actingAs sticks for the rest of the test, so the shopper
        // view has to be the one that is already behind us.
        $this->get(route('catalog.index'))->assertOk()->assertDontSee('Internal only');

        $this->actingAs(User::factory()->staff()->create())
            ->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Internal only');
    }

    public function test_republishing_a_dismissed_notice_reaches_the_shopper_again(): void
    {
        $notice = $this->notice();
        $this->post(route('notifications.dismiss', $notice))->assertRedirect();

        // A new announcement is a new row, so clearing the old one cannot hide
        // what the store says afterwards.
        $this->notice(['title' => 'Back for another season']);

        $this->get(route('catalog.index'))->assertOk()->assertSee('Back for another season');
    }
}
