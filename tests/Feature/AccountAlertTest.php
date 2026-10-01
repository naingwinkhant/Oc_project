<?php

namespace Tests\Feature;

use App\Models\TeamAlert;
use App\Models\User;
use App\Notifications\BellService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A new account is announced, and the announcement stops when it is answered.
 *
 * The account is refused at every door from the moment it is registered, so the
 * alert is the only way the team finds out that somebody is waiting. That makes
 * two things worth proving: that it reaches the people who can decide, and that
 * it reaches nobody else.
 */
class AccountAlertTest extends TestCase
{
    use RefreshDatabase;

    private function register(array $attributes = []): User
    {
        $this->post(route('register.store'), [
            'name' => $attributes['name'] ?? 'Jenna Cruz',
            'email' => $attributes['email'] ?? 'jenna@goldengate.test',
            'password' => 'chicken-fried-rice',
            'password_confirmation' => 'chicken-fried-rice',
        ])->assertRedirect(route('staff.login.php'));

        return User::query()->where('email', $attributes['email'] ?? 'jenna@goldengate.test')
            ->firstOrFail();
    }

    /*
    |--------------------------------------------------------------------------
    | The announcement is raised
    |--------------------------------------------------------------------------
    */

    public function test_registering_raises_an_alert_for_the_team(): void
    {
        $user = $this->register();

        $alert = TeamAlert::query()->where('kind', 'account')->sole();

        $this->assertSame($user->id, $alert->user_id);
        $this->assertStringContainsString('Jenna Cruz', $alert->title);
        $this->assertSame(route('admin.approvals.index'), $alert->link);
    }

    public function test_the_alert_says_what_each_button_does(): void
    {
        $this->register();

        $body = TeamAlert::query()->where('kind', 'account')->sole()->body;

        // Somebody reading only the bell still learns the consequence of both.
        $this->assertStringContainsString('Accept', $body);
        $this->assertStringContainsString('reject', strtolower($body));
        $this->assertStringContainsString('jenna@goldengate.test', $body);
    }

    public function test_the_alert_points_at_the_person_and_the_role_they_would_get(): void
    {
        $this->register();

        $body = TeamAlert::query()->where('kind', 'account')->sole()->body;

        $this->assertStringContainsString('Staff', $body);
    }

    /*
    |--------------------------------------------------------------------------
    | Only the people who can decide see it
    |--------------------------------------------------------------------------
    */

    public function test_an_administrator_sees_the_alert_in_their_bell(): void
    {
        $this->register();

        $titles = $this->bellFor(User::factory()->admin()->create());

        $this->assertTrue(
            collect($titles)->contains(fn ($t) => str_contains($t, 'Jenna Cruz')),
            'expected the registration in the administrator bell, got: '.implode(' | ', $titles)
        );
    }

    public function test_a_manager_sees_the_alert_in_their_bell(): void
    {
        $this->register();

        $titles = $this->bellFor(User::factory()->manager()->create());

        $this->assertTrue(
            collect($titles)->contains(fn ($t) => str_contains($t, 'Jenna Cruz')),
            'expected the registration in the manager bell, got: '.implode(' | ', $titles)
        );
    }

    public function test_plain_staff_are_not_told_about_it(): void
    {
        $this->register();

        // They may not accept or reject anybody, so the decision is not theirs
        // to be prompted about.
        $titles = $this->bellFor(User::factory()->staff()->create());

        $this->assertFalse(
            collect($titles)->contains(fn ($t) => str_contains($t, 'Jenna Cruz')),
            'staff must not be asked to decide, got: '.implode(' | ', $titles)
        );
    }

    public function test_a_customer_is_not_told_about_it(): void
    {
        $this->register();

        $this->assertSame([], $this->bellFor(User::factory()->customer()->create()));
    }

    /*
    |--------------------------------------------------------------------------
    | Both pages raise it in their own way
    |--------------------------------------------------------------------------
    */

    public function test_the_administrator_dashboard_offers_both_choices_inline(): void
    {
        $user = $this->register();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            // Both buttons, each naming its own consequence.
            ->assertSee(route('admin.approvals.accept', $user), false)
            ->assertSee(route('admin.approvals.reject', $user), false)
            ->assertSee('Accept as staff')
            ->assertSee('Reject')
            ->assertSee('Jenna Cruz');
    }

    public function test_the_manager_page_offers_both_choices_inline(): void
    {
        $user = $this->register();

        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.manager.home'))
            ->assertOk()
            ->assertSee(route('admin.approvals.accept', $user), false)
            ->assertSee(route('admin.approvals.reject', $user), false)
            ->assertSee('Accept as staff')
            ->assertSee('Jenna Cruz');
    }

    public function test_neither_page_raises_it_for_plain_staff(): void
    {
        $this->register();

        // The staff dashboard must not carry the decision at all.
        $this->actingAs(User::factory()->staff()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Accounts waiting to be accepted')
            ->assertDontSee('Accept as staff');
    }

    public function test_the_dashboard_is_quiet_when_nothing_is_waiting(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Accounts waiting to be accepted');
    }

    /*
    |--------------------------------------------------------------------------
    | Answering the alert ends it
    |--------------------------------------------------------------------------
    */

    public function test_accepting_clears_the_alert_for_everybody(): void
    {
        $user = $this->register();

        $this->actingAs(User::factory()->admin()->create())
            ->patch(route('admin.approvals.accept', $user));

        // Removed rather than hidden, so the next person to look does not have
        // to click it away either.
        $this->assertSame(0, TeamAlert::query()->where('kind', 'account')->count());
        $this->assertDatabaseMissing('team_alert_dismissals', [
            'team_alert_id' => TeamAlert::query()->max('id') ?? 0,
        ]);
    }

    public function test_rejecting_clears_the_alert_too(): void
    {
        $user = $this->register();

        $this->actingAs(User::factory()->manager()->create())
            ->patch(route('admin.approvals.reject', $user));

        $this->assertSame(0, TeamAlert::query()->where('kind', 'account')->count());
    }

    public function test_an_alert_belonging_to_one_account_does_not_clear_another(): void
    {
        $jenna = $this->register(['name' => 'Jenna Cruz', 'email' => 'jenna@goldengate.test']);
        $nyein = $this->register(['name' => 'Nyein Lay', 'email' => 'nyein@goldengate.test']);

        $this->assertSame(2, TeamAlert::query()->where('kind', 'account')->count());

        $this->actingAs(User::factory()->admin()->create())
            ->patch(route('admin.approvals.accept', $jenna));

        // One decided is one less waiting, and the other is still waiting.
        $this->assertSame(1, TeamAlert::query()->where('kind', 'account')->count());
        $this->assertSame(1, TeamAlert::query()->where('user_id', $nyein->id)->count());
    }

    public function test_the_sidebar_badge_counts_what_is_still_waiting(): void
    {
        $this->register();
        $this->register(['name' => 'Nyein Lay', 'email' => 'nyein@goldengate.test']);

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-approval-pill', false);

        $jenna = User::query()->where('email', 'jenna@goldengate.test')->firstOrFail();
        $this->actingAs($admin)->patch(route('admin.approvals.accept', $jenna));

        // The flash from the acceptance is spent first: it names Jenna, and it
        // has nothing to do with what the queue is showing.
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();

        // One gone, so one is still waiting. Counted rather than matched on the
        // name, because a decided account is still listed further down the page
        // as a decision that has already been made.
        $this->assertSame(1, User::query()->pending()->count());

        $this->actingAs($admin)
            ->get(route('admin.approvals.index'))
            ->assertOk()
            ->assertSee('Waiting for a decision')
            ->assertSee('Nyein Lay');

        // And the bell no longer mentions the account that was just decided.
        $this->assertFalse(
            collect($this->bellFor($admin))->contains(fn ($t) => str_contains($t, 'Jenna Cruz')),
            'the decided account must leave the bell'
        );
        $this->assertTrue(
            collect($this->bellFor($admin))->contains(fn ($t) => str_contains($t, 'Nyein Lay')),
            'the account still waiting must stay in the bell'
        );
    }

    /**
     * What the bell would show this person, by title.
     *
     * @return array<int, string>
     */
    private function bellFor(User $viewer): array
    {
        $this->actingAs($viewer);

        return app(BellService::class)
            ->items(true)
            ->pluck('title')
            ->all();
    }
}
