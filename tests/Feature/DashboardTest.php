<?php

namespace Tests\Feature;

use App\Enums\StockMovementType;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_segments_add_up_to_the_whole_catalogue(): void
    {
        Product::factory()->count(6)->create(['stock' => 100, 'min_stock' => 10]);
        Product::factory()->count(3)->create(['stock' => 4, 'min_stock' => 10]);
        Product::factory()->outOfStock()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('10 goods items are above their reorder threshold')
            ->assertSee('Healthy')
            ->assertSee('Running low')
            ->assertSee('Out of stock');
    }

    public function test_healthy_store_reports_a_perfect_score(): void
    {
        Product::factory()->count(4)->create(['stock' => 80, 'min_stock' => 10]);

        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Every shelf is fully stocked')
            ->assertSee('100%');
    }

    public function test_empty_catalogue_does_not_divide_by_zero(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('No goods in the catalogue yet');
    }

    public function test_restock_queue_is_ordered_with_empty_shelves_first(): void
    {
        $empty = Product::factory()->outOfStock()->create(['name' => 'Empty Item']);
        $low = Product::factory()->lowStock()->create(['name' => 'Scarce Item']);
        Product::factory()->create(['name' => 'Plenty Item', 'stock' => 500, 'min_stock' => 10]);

        $response = $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.dashboard'))
            ->assertOk();

        $body = $response->getContent();

        $this->assertStringContainsString('Restock queue', $body);
        $this->assertLessThan(
            strpos($body, $low->name),
            strpos($body, $empty->name),
            'The empty item should be listed before the scarce one.'
        );
    }

    public function test_department_value_rolls_up_child_aisles(): void
    {
        $parent = Category::factory()->create(['name' => 'Bakery']);
        $child = Category::factory()->childOf($parent)->create(['name' => 'Bread']);

        Product::factory()->for($child)->create(['name' => 'Sourdough', 'price' => 100, 'stock' => 10]);

        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Bakery')
            ->assertSee('1,000');
    }

    public function test_movement_series_covers_thirty_days_and_separates_in_from_out(): void
    {
        $product = Product::factory()->create();

        StockMovement::create([
            'product_id' => $product->id,
            'type' => StockMovementType::In,
            'quantity' => 40,
            'balance_after' => 40,
            'created_at' => now()->subDays(2),
        ]);

        StockMovement::create([
            'product_id' => $product->id,
            'type' => StockMovementType::Out,
            'quantity' => -10,
            'balance_after' => 30,
            'created_at' => now()->subDays(1),
        ]);

        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Last 30 days')
            ->assertSee('+30 net');
    }

    public function test_login_events_are_hidden_from_the_dashboard_feed(): void
    {
        $user = User::factory()->create();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => 'Someone signed in',
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'created',
            'subject_type' => Product::class,
            'subject_id' => 1,
            'description' => 'Added goods item Sourdough',
        ]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Added goods item Sourdough')
            ->assertDontSee('Someone signed in');
    }

    public function test_guests_cannot_reach_the_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login.php'));
    }
}
