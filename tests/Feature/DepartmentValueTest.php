<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Support\Money;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * "Where your money sits" — stock value by department, aisles included.
 *
 * The list is what a manager uses to decide where the value is sitting, so the
 * arithmetic is pinned down here: aisles fold into their department, the shares
 * add up to 100, and the long tail is collected into "Other departments".
 */
class DepartmentValueTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: TestResponse, 1: Collection}
     */
    private function breakdown(callable $seed): array
    {
        $seed();

        $response = $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Where your money sits')
            ->assertSee('Stock value by department, aisles included');

        $rows = $response->viewData('departments');

        return [$response, $rows];
    }

    public function test_an_aisle_folds_into_its_department(): void
    {
        $meat = Category::factory()->create(['name' => 'Meat & Seafood']);
        $beef = Category::factory()->childOf($meat)->create(['name' => 'Beef']);
        $other = Category::factory()->create(['name' => 'Bakery']);

        Product::factory()->for($meat)->create(['stock' => 10, 'price' => 5_000, 'sale_price' => null]);
        Product::factory()->for($beef)->create(['stock' => 4, 'price' => 2_500, 'sale_price' => null]);
        Product::factory()->for($other)->create(['stock' => 10, 'price' => 1_000, 'sale_price' => null]);

        [, $rows] = $this->breakdown(fn () => null);

        $row = $rows->firstWhere('name', 'Meat & Seafood');

        $this->assertNotNull($row, 'the department should appear in the list');
        // 10 x 5,000 + 4 x 2,500 = 60,000, and both items are counted.
        $this->assertSame(60_000.0, round($row['value'], 2));
        $this->assertSame(2, $row['items']);

        $bakery = $rows->firstWhere('name', 'Bakery');
        $this->assertSame(10_000.0, round($bakery['value'], 2));
    }

    public function test_a_promotion_is_valued_at_the_price_the_shopper_pays(): void
    {
        $root = Category::factory()->create(['name' => 'Bakery']);

        Product::factory()->for($root)->create(['stock' => 10, 'price' => 5_000, 'sale_price' => 4_000]);

        [, $rows] = $this->breakdown(fn () => null);

        // 10 x 4,000, not 10 x 5,000.
        $this->assertSame(40_000.0, round($rows->firstWhere('name', 'Bakery')['value'], 2));
    }

    public function test_the_departments_are_ordered_by_value_largest_first(): void
    {
        $small = Category::factory()->create(['name' => 'Small']);
        $large = Category::factory()->create(['name' => 'Large']);
        $middle = Category::factory()->create(['name' => 'Middle']);

        Product::factory()->for($small)->create(['stock' => 10, 'price' => 1_000, 'sale_price' => null]);
        Product::factory()->for($large)->create(['stock' => 10, 'price' => 9_000, 'sale_price' => null]);
        Product::factory()->for($middle)->create(['stock' => 10, 'price' => 5_000, 'sale_price' => null]);

        [, $rows] = $this->breakdown(fn () => null);

        $this->assertSame(
            ['Large', 'Middle', 'Small'],
            $rows->pluck('name')->all()
        );
    }

    public function test_the_shares_add_up_to_one_hundred_percent(): void
    {
        $seed = function () {
            foreach (range(1, 5) as $i) {
                $root = Category::factory()->create(['name' => 'Department '.$i]);
                Product::factory()->for($root)->create([
                    'stock' => $i * 10,
                    'price' => $i * 1_000,
                    'sale_price' => null,
                ]);
            }
        };

        [, $rows] = $this->breakdown($seed);

        $this->assertEqualsWithDelta(100.0, (float) $rows->sum('share'), 0.5);
    }

    public function test_a_long_tail_is_collected_into_other_departments(): void
    {
        $seed = function () {
            // Nine departments: eight named, and a ninth that tips into the tail.
            foreach (range(1, 9) as $i) {
                $root = Category::factory()->create(['name' => 'Department '.$i]);
                Product::factory()->for($root)->create([
                    'stock' => 10,
                    // Falling values, so the ninth is the smallest.
                    'price' => (10 - $i) * 1_000,
                    'sale_price' => null,
                ]);
            }
        };

        [, $rows] = $this->breakdown($seed);

        $other = $rows->firstWhere('name', 'Other departments');

        $this->assertNotNull($other, 'the ninth department should be folded into the tail');
        $this->assertTrue($other['isOther']);
        $this->assertSame(1, $other['items']);
        // The smallest department: 1,000 a unit on 10 of them.
        $this->assertSame(10_000.0, round($other['value'], 2));

        // Eight named departments plus the single tail row.
        $this->assertCount(9, $rows);
        $this->assertEqualsWithDelta(100.0, (float) $rows->sum('share'), 0.5);
    }

    public function test_the_tail_row_sums_the_shares_it_replaces(): void
    {
        $seed = function () {
            foreach (range(1, 9) as $i) {
                $root = Category::factory()->create(['name' => 'Department '.$i]);
                Product::factory()->for($root)->create([
                    'stock' => 10,
                    'price' => (10 - $i) * 1_000,
                    'sale_price' => null,
                ]);
            }
        };

        [, $rows] = $this->breakdown($seed);

        $named = $rows->reject(fn (array $row) => $row['isOther']);
        $other = $rows->firstWhere('isOther', true);

        // The tail row's share is the sum of what it swallowed, so the column
        // still totals 100 without quietly losing a rounding step.
        $this->assertEqualsWithDelta(100.0, (float) $named->sum('share') + $other['share'], 1.0);
    }

    public function test_goods_outside_any_department_do_not_break_the_totals(): void
    {
        $root = Category::factory()->create(['name' => 'Bakery']);

        Product::factory()->for($root)->create(['stock' => 10, 'price' => 1_000, 'sale_price' => null]);
        Product::factory()->create(['category_id' => null, 'stock' => 5, 'price' => 99_000, 'sale_price' => null]);

        [, $rows] = $this->breakdown(fn () => null);

        // Uncategorized stock is not silently folded into a department it is not in.
        $this->assertSame(10_000.0, round($rows->firstWhere('name', 'Bakery')['value'], 2));
        $this->assertEqualsWithDelta(100.0, (float) $rows->sum('share'), 0.5);
    }

    public function test_an_empty_catalogue_says_so_instead_of_dividing_by_zero(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('No classifications yet');
    }

    public function test_the_totals_are_shown_in_the_shop_currency(): void
    {
        $root = Category::factory()->create(['name' => 'Meat & Seafood']);
        Product::factory()->for($root)->create(['stock' => 12, 'price' => 250_000, 'sale_price' => null]);

        $this->actingAs(User::factory()->manager()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(Money::format(3_000_000))
            ->assertSee('3,000,000 '.Money::symbol());
    }
}
