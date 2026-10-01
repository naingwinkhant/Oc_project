<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Nobody can lock the shop out of its own user management.
 *
 * The shop must always keep at least one active administrator, and an
 * administrator must not be able to quietly step down and leave nobody behind.
 */
class LastAdministratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_only_administrator_cannot_step_down(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->put(route('admin.users.update', $admin), [
                'username' => $admin->username,
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => Role::Staff->value,
            ])
            // Refused on the role field itself, by the request's own rule.
            ->assertSessionHasErrors(['role' => 'You cannot remove your own administrator role.']);

        $this->assertSame(Role::Admin, $admin->fresh()->role);
    }

    public function test_the_only_administrator_cannot_disable_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->put(route('admin.users.update', $admin), [
                'username' => $admin->username,
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => Role::Admin->value,
                'is_active' => '0',
            ])
            ->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_the_only_administrator_cannot_delete_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->delete(route('admin.users.destroy', $admin))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_a_disabled_administrator_may_be_demoted(): void
    {
        // The one acting here is still an administrator afterwards, so this is
        // safe and allowed: a spare seat exists the whole time.
        $admin = User::factory()->admin()->create();
        $disabled = User::factory()->admin()->create(['is_active' => false]);

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $disabled))
            ->put(route('admin.users.update', $disabled), [
                'username' => $disabled->username,
                'name' => $disabled->name,
                'email' => $disabled->email,
                'role' => Role::Staff->value,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(Role::Staff, $disabled->fresh()->role);
    }

    public function test_an_administrator_can_still_change_their_own_details(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Before']);

        // The active box is ticked in the form; leaving it out would mean
        // "disable me", which is the one thing that is refused.
        $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->put(route('admin.users.update', $admin), [
                'username' => $admin->username,
                'name' => 'After',
                'email' => $admin->email,
                'role' => Role::Admin->value,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame('After', $admin->fresh()->name);
        $this->assertSame(Role::Admin, $admin->fresh()->role);
    }

    public function test_an_administrator_may_demote_another_while_one_remains(): void
    {
        $first = User::factory()->admin()->create();
        $second = User::factory()->admin()->create();

        $this->actingAs($first)
            ->put(route('admin.users.update', $second), [
                'username' => $second->username,
                'name' => $second->name,
                'email' => $second->email,
                'role' => Role::Staff->value,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(Role::Staff, $second->fresh()->role);
    }

    public function test_staff_and_managers_cannot_reach_user_management_at_all(): void
    {
        $subject = User::factory()->staff()->create();

        foreach ([Role::Staff, Role::Manager] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('admin.users.index'))
                ->assertForbidden();

            $this->actingAs(User::factory()->create(['role' => $role]))
                ->put(route('admin.users.update', $subject), [
                    'username' => $subject->username,
                    'name' => $subject->name,
                    'email' => $subject->email,
                    'role' => Role::Admin->value,
                ])
                ->assertForbidden();
        }

        $this->assertSame(Role::Staff, $subject->fresh()->role);
    }

    public function test_the_role_itself_is_validated_on_the_server(): void
    {
        $admin = User::factory()->admin()->create(['role' => Role::Admin]);
        $subject = User::factory()->staff()->create();

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $subject))
            ->put(route('admin.users.update', $subject), [
                'username' => $subject->username,
                'name' => $subject->name,
                'email' => $subject->email,
                'role' => 'superuser',
            ])
            ->assertSessionHasErrors('role');

        $this->assertSame(Role::Staff, $subject->fresh()->role);
    }
}
