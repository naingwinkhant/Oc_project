<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The two front doors.
 *
 * /admin and /staff each open their own sign-in page, and a deep link into an
 * area opens that area's page rather than the generic one.
 */
class AreaEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_admin_area_opens_the_admin_door(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login.php'));
        $this->get('/admin/')->assertRedirect(route('admin.login.php'));
    }

    public function test_the_staff_area_opens_the_staff_door(): void
    {
        $this->get('/staff')->assertRedirect(route('staff.login.php'));
        $this->get('/staff/')->assertRedirect(route('staff.login.php'));
    }

    public function test_a_deep_link_into_an_area_opens_that_area_s_door(): void
    {
        $this->get('/admin/products')->assertRedirect(route('admin.login.php'));
        $this->get('/admin/orders')->assertRedirect(route('admin.login.php'));
    }

    public function test_the_public_area_still_uses_the_generic_door(): void
    {
        $this->get('/settings')->assertOk();
        $this->get('/cart')->assertOk();
    }

    public function test_a_member_of_the_team_passes_straight_through(): void
    {
        $this->actingAs(User::factory()->staff()->create())
            ->get('/staff')
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs(User::factory()->admin()->create())
            ->get('/staff')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_the_doors_still_render_for_a_guest(): void
    {
        $this->get('/admin/login.php')
            ->assertOk()
            ->assertSee('Administrator sign in');

        $this->get('/staff/login.php')
            ->assertOk()
            ->assertSee('Staff sign in');
    }

    public function test_the_create_account_link_is_offered_on_the_doors_that_matter(): void
    {
        // A new person arrives at a door with no account, so both doors must
        // offer the public registration form rather than only sending them back.
        $this->get('/staff/login.php')
            ->assertOk()
            ->assertSee(route('register'), false)
            ->assertSee('Create one');

        $this->get('/admin/login.php')
            ->assertOk()
            ->assertSee(route('register'), false);

        $this->get('/login')
            ->assertOk()
            ->assertSee(route('register'), false);
    }

    public function test_the_public_registration_page_is_open_to_anyone(): void
    {
        $this->get(route('register'))->assertOk()->assertSee('Create account');

        // A signed-in person has no business registering again.
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('register'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_the_create_account_page_is_still_admin_only(): void
    {
        // The link is on the door, but the page behind it does not open for a
        // visitor or for a non-admin: it has to be signed in as an administrator.
        $this->get('/admin/users/create')->assertRedirect(route('admin.login.php'));

        $this->actingAs(User::factory()->staff()->create())
            ->get('/admin/users/create')
            ->assertForbidden();

        $this->actingAs(User::factory()->manager()->create())
            ->get('/admin/users/create')
            ->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/users/create')
            ->assertOk();
    }

    public function test_an_admin_following_the_link_lands_on_the_add_user_page(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->from('/admin/login.php')
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('Add user');
    }
}
