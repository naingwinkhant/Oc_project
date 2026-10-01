<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Sending a notice is an administrator or manager job.
 *
 * Staff can read the list so they know what has been announced, but the send,
 * edit and delete routes are refused for them and the controls are not offered
 * in the first place.
 */
class SendNoticeTest extends TestCase
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

    /**
     * @return array<int, array{0: string, 1: Role}>
     */
    public static function senders(): array
    {
        return [
            'admin' => [Role::Admin],
            'manager' => [Role::Manager],
        ];
    }

    /**
     * @dataProvider senders
     */
    public function test_an_admin_or_manager_can_send_a_notice(Role $role): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)->get(route('admin.notices.create'))->assertOk();

        $this->actingAs($user)
            ->post(route('admin.notices.store'), [
                'title' => 'Half price on household cleaning',
                'body' => 'This weekend only, while stocks last.',
                'tone' => 'brand',
                'is_active' => '1',
                'show_on_shop' => '1',
            ])
            ->assertRedirect(route('admin.notices.index'));

        $this->assertDatabaseHas('notices', [
            'title' => 'Half price on household cleaning',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Publishing redirects with a flash naming the notice, and actingAs() lasts
     * for the whole test, so both are cleared before a shopper's page can be
     * trusted to prove what the bell is showing.
     */
    private function shopperPage(): TestResponse
    {
        $this->app['auth']->forgetGuards();
        $this->get(route('settings'))->assertOk();

        $this->assertGuest();

        return $this->get(route('catalog.index'))->assertOk();
    }

    public function test_a_sent_notice_reaches_shoppers_in_the_bell(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->post(route('admin.notices.store'), [
                'title' => 'Delivery fee waived today',
                'body' => 'No delivery fee on any order placed before 6pm.',
                'tone' => 'brand',
                'is_active' => '1',
                'show_on_shop' => '1',
            ]);

        $this->shopperPage()
            ->assertSee('Delivery fee waived today')
            ->assertSee('No delivery fee on any order placed before 6pm.')
            ->assertSee('data-notification-item', false);
    }

    public function test_a_draft_is_not_shown_to_shoppers_or_to_staff(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->post(route('admin.notices.store'), [
                'title' => 'Not ready yet',
                'body' => 'Still being written.',
                'tone' => 'info',
                'is_active' => '0',
                'show_on_shop' => '1',
            ]);

        // Not published means not shown, whoever is looking.
        $this->shopperPage()->assertDontSee('Not ready yet');

        $this->actingAs(User::factory()->staff()->create())
            ->get(route('catalog.index'))
            ->assertOk()
            ->assertDontSee('Not ready yet');
    }

    public function test_an_expired_notice_is_not_shown_to_anyone(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->post(route('admin.notices.store'), [
                'title' => 'Last month campaign',
                'body' => 'Its window has closed.',
                'tone' => 'brand',
                'is_active' => '1',
                'show_on_shop' => '1',
                'ends_on' => now()->subDay()->toDateString(),
            ]);

        $this->shopperPage()->assertDontSee('Last month campaign');

        $this->actingAs(User::factory()->staff()->create())
            ->get(route('catalog.index'))
            ->assertOk()
            ->assertDontSee('Last month campaign');
    }

    public function test_an_internal_notice_is_withheld_from_shoppers(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->post(route('admin.notices.store'), [
                'title' => 'Stocktake on Thursday',
                'body' => 'The back room is closed for the count.',
                'tone' => 'warning',
                'is_active' => '1',
                'show_on_shop' => '0',
            ]);

        $this->shopperPage()->assertDontSee('Stocktake on Thursday');

        // Staff still need to see it.
        $this->actingAs(User::factory()->staff()->create())
            ->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Stocktake on Thursday');
    }

    public function test_staff_can_read_the_list_but_are_offered_no_send_controls(): void
    {
        $this->notice();

        $this->actingAs(User::factory()->staff()->create())
            ->get(route('admin.notices.index'))
            ->assertOk()
            ->assertSee('Same-day delivery inside Yangon')
            // The controls that would only bounce them into a 403.
            ->assertDontSee('New notice')
            ->assertDontSee('Write the first notice')
            ->assertDontSee(route('admin.notices.create'), false)
            ->assertDontSee(route('admin.notices.edit', Notice::query()->first()), false)
            ->assertSee('Read only');
    }

    public function test_staff_cannot_reach_the_send_routes(): void
    {
        $staff = User::factory()->staff()->create();
        $notice = $this->notice();

        $this->actingAs($staff)->get(route('admin.notices.create'))->assertForbidden();
        $this->actingAs($staff)->get(route('admin.notices.edit', $notice))->assertForbidden();

        $this->actingAs($staff)
            ->post(route('admin.notices.store'), [
                'title' => 'Staff should not manage this',
                'body' => 'No.',
                'tone' => 'info',
            ])
            ->assertForbidden();

        $this->actingAs($staff)
            ->put(route('admin.notices.update', $notice), [
                'title' => 'Hijacked',
                'body' => 'No.',
                'tone' => 'info',
            ])
            ->assertForbidden();

        $this->actingAs($staff)
            ->delete(route('admin.notices.destroy', $notice))
            ->assertForbidden();

        $this->assertDatabaseHas('notices', ['title' => 'Same-day delivery inside Yangon']);
    }

    public function test_a_manager_can_edit_and_delete_but_an_admin_only_page_stays_closed(): void
    {
        $manager = User::factory()->manager()->create();
        $notice = $this->notice();

        $this->actingAs($manager)
            ->put(route('admin.notices.update', $notice), [
                'title' => 'Free delivery over 150,000 Ks',
                'body' => 'Fill a big basket and we will carry it for nothing.',
                'tone' => 'brand',
            ])
            ->assertRedirect(route('admin.notices.index'));

        $this->assertDatabaseHas('notices', ['title' => 'Free delivery over 150,000 Ks']);

        $this->actingAs($manager)
            ->delete(route('admin.notices.destroy', $notice))
            ->assertRedirect(route('admin.notices.index'));

        $this->assertDatabaseMissing('notices', ['id' => $notice->id]);
    }

    public function test_a_guest_is_sent_to_the_sign_in_page(): void
    {
        $notice = $this->notice();

        $this->get(route('admin.notices.index'))->assertRedirect(route('admin.login.php'));
        $this->get(route('admin.notices.create'))->assertRedirect(route('admin.login.php'));
        $this->post(route('admin.notices.store'), ['title' => 'x', 'body' => 'y'])->assertRedirect(route('admin.login.php'));
        $this->delete(route('admin.notices.destroy', $notice))->assertRedirect(route('admin.login.php'));
    }
}
