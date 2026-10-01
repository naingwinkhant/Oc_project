<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Registering does not open the shop.
 *
 * An account waits in the queue until an administrator or manager accepts it.
 * Accepting lets it sign in; turning it down is final. Neither a pending nor a
 * turned-down account can get in, whatever password it holds.
 */
class AccountApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function register(): User
    {
        return User::factory()->pending()->staff()->create([
            'username' => 'jennac',
            'name' => 'Jenna Cruz',
            'email' => 'jenna@goldengate.test',
            'password' => bcrypt('chicken-fried-rice'),
        ]);
    }

    public function test_a_new_account_starts_out_waiting(): void
    {
        $user = $this->register();

        $this->assertTrue($user->isPending());
        $this->assertFalse($user->isApproved());
        $this->assertSame(AccountStatus::Pending, $user->status);
        $this->assertNull($user->approved_at);
    }

    public function test_a_pending_account_cannot_sign_in_even_with_the_right_password(): void
    {
        $this->register();

        $this->post(route('staff.login.php'), [
            'email' => 'jenna@goldengate.test',
            'password' => 'chicken-fried-rice',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_the_refusal_says_it_is_waiting_to_be_accepted(): void
    {
        $this->register();

        $this->post(route('staff.login.php'), [
            'email' => 'jenna@goldengate.test',
            'password' => 'chicken-fried-rice',
        ])->assertSessionHasErrors([
            'email' => AccountStatus::Pending->refusalMessage(),
        ]);
    }

    public function test_an_administrator_can_accept_it(): void
    {
        $user = $this->register();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.approvals.index'))
            ->patch(route('admin.approvals.accept', $user))
            ->assertRedirect(route('admin.approvals.index'))
            ->assertSessionHas('success');

        $user->refresh();

        $this->assertTrue($user->isApproved());
        $this->assertNotNull($user->approved_at);
        $this->assertNotNull($user->decided_at);
        $this->assertSame($admin->id, $user->decided_by);
        $this->assertDatabaseHas('activity_logs', ['action' => 'updated', 'subject_id' => $user->id]);
    }

    public function test_a_manager_can_accept_it_too(): void
    {
        $user = $this->register();
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)->patch(route('admin.approvals.accept', $user))->assertRedirect();

        $this->assertTrue($user->fresh()->isApproved());
        $this->assertSame($manager->id, $user->fresh()->decided_by);
    }

    public function test_an_administrator_can_turn_an_account_down(): void
    {
        $user = $this->register();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.approvals.index'))
            ->patch(route('admin.approvals.reject', $user))
            ->assertRedirect(route('admin.approvals.index'))
            ->assertSessionHas('success');

        $user->refresh();

        $this->assertTrue($user->isRejected());
        $this->assertFalse($user->isApproved());
        $this->assertSame(AccountStatus::Rejected, $user->status);
    }

    public function test_a_manager_can_turn_an_account_down_too(): void
    {
        $user = $this->register();

        $this->actingAs(User::factory()->manager()->create())
            ->patch(route('admin.approvals.reject', $user))
            ->assertRedirect();

        $this->assertTrue($user->fresh()->isRejected());
    }

    public function test_staff_cannot_accept_anybody(): void
    {
        $user = $this->register();

        $this->actingAs(User::factory()->staff()->create())
            ->patch(route('admin.approvals.accept', $user))
            ->assertForbidden();

        $this->assertTrue($user->fresh()->isPending());
    }

    public function test_staff_cannot_turn_anybody_down(): void
    {
        $user = $this->register();

        $this->actingAs(User::factory()->staff()->create())
            ->patch(route('admin.approvals.reject', $user))
            ->assertForbidden();

        $this->assertTrue($user->fresh()->isPending());
    }

    public function test_a_guest_cannot_accept_anybody(): void
    {
        $user = $this->register();

        $this->patch(route('admin.approvals.accept', $user))
            ->assertRedirect(route('admin.login.php'));

        $this->assertTrue($user->fresh()->isPending());
    }

    public function test_a_turned_down_account_cannot_sign_in_even_with_the_right_password(): void
    {
        $this->register()->reject(User::factory()->admin()->create());

        $this->post(route('staff.login.php'), [
            'email' => 'jenna@goldengate.test',
            'password' => 'chicken-fried-rice',
        ])->assertSessionHasErrors([
            'email' => AccountStatus::Rejected->refusalMessage(),
        ]);

        $this->assertGuest();
    }

    public function test_an_accepted_account_can_sign_in(): void
    {
        $user = $this->register();
        $user->approve(User::factory()->admin()->create());

        $this->post(route('staff.login.php'), [
            'email' => 'jenna@goldengate.test',
            'password' => 'chicken-fried-rice',
        ])->assertRedirect(route('admin.staff.home'));

        $this->assertAuthenticatedAs($user->fresh());
    }

    public function test_an_account_can_be_accepted_only_once(): void
    {
        $user = $this->register();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->patch(route('admin.approvals.accept', $user))->assertRedirect();

        $this->actingAs($admin)
            ->from(route('admin.approvals.index'))
            ->patch(route('admin.approvals.accept', $user))
            ->assertSessionHas('error');
    }

    public function test_a_decided_account_cannot_be_turned_down_after_the_fact(): void
    {
        $user = $this->register();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->patch(route('admin.approvals.accept', $user))->assertRedirect();

        $this->actingAs($admin)
            ->from(route('admin.approvals.index'))
            ->patch(route('admin.approvals.reject', $user))
            ->assertSessionHas('error');

        $this->assertTrue($user->fresh()->isApproved());
    }

    public function test_acceptance_can_be_withdrawn_by_an_administrator(): void
    {
        $user = $this->register();
        $user->approve(User::factory()->admin()->create());
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->patch(route('admin.users.revoke', $user))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue($user->isPending());
        $this->assertNull($user->decided_by);

        // And it really is locked out again.
        $this->app['auth']->forgetGuards();
        $this->post(route('staff.login.php'), [
            'email' => 'jenna@goldengate.test',
            'password' => 'chicken-fried-rice',
        ])->assertSessionHasErrors('email');
    }

    public function test_the_queue_lists_waiting_accounts_with_both_choices(): void
    {
        $user = $this->register();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.approvals.index'))
            ->assertOk()
            ->assertSee('Jenna Cruz')
            ->assertSee(route('admin.approvals.accept', $user), false)
            ->assertSee(route('admin.approvals.reject', $user), false);
    }

    public function test_the_queue_is_empty_once_everything_is_decided(): void
    {
        $user = $this->register();
        $user->approve(User::factory()->admin()->create());

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.approvals.index'))
            ->assertOk()
            ->assertSee('Nothing waiting');
    }

    public function test_staff_cannot_reach_the_queue(): void
    {
        $this->actingAs(User::factory()->staff()->create())
            ->get(route('admin.approvals.index'))
            ->assertForbidden();
    }

    public function test_accounts_that_existed_before_this_arrived_are_still_accepted(): void
    {
        // The factory stands in for the accounts the migration accepted.
        $this->assertTrue(User::factory()->create()->isApproved());
        $this->assertTrue(User::factory()->admin()->create()->isApproved());
        $this->assertTrue(User::factory()->manager()->create()->isApproved());
        $this->assertTrue(User::factory()->staff()->create()->isApproved());
    }
}
