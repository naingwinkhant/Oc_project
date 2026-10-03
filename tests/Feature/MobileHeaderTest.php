<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The search field on a phone, and the classifications around it.
 *
 * A phone has not got the width for a field sitting between the logo and five
 * icons, so below the small breakpoint the search takes a row of its own. These
 * checks read the classes rather than a screenshot, because a screenshot of the
 * wrong width would pass just as happily.
 */
class MobileHeaderTest extends TestCase
{
    use RefreshDatabase;

    private function header(string $path = '/catalog'): string
    {
        return (string) $this->get($path)
            ->assertOk()
            ->getContent();
    }

    /**
     * @return array<int, string>
     */
    private function searchForms(string $html): array
    {
        preg_match_all('#<form[^>]*role="search"[^>]*class="([^"]*)"[^>]*>#', $html, $forms);
        preg_match_all('#<div[^>]*class="([^"]*sm:hidden[^"]*)"[^>]*>\s*<form[^>]*role="search"#', $html, $wrappers);

        return array_merge($forms[1], $wrappers[1]);
    }

    public function test_the_search_field_is_reachable_and_labelled(): void
    {
        // Two of them: one for the shared top row, one for the phone row.
        $this->assertStringContainsString('role="search"', $this->header());
        $this->assertSame(
            2,
            substr_count($this->header(), 'aria-label="Search goods"'),
            'the search field must be labelled, once per row it appears in'
        );
    }

    public function test_on_a_phone_the_search_takes_a_row_of_its_own(): void
    {
        $html = $this->header();

        // sm:hidden puts it below the small breakpoint, where it is the only
        // thing in the row and so runs the full width of the screen.
        $this->assertStringContainsString(
            'border-t border-ink-200 bg-surface px-4 py-2 sm:hidden',
            $html,
            'the phone search row is missing'
        );

        $this->assertStringContainsString('data-live-search', $html);
    }

    public function test_the_shared_top_row_hides_the_search_on_a_phone(): void
    {
        $html = $this->header();

        // Otherwise both would show at once and the phone would have two fields.
        $this->assertStringContainsString(
            'ml-auto hidden min-w-0 flex-1 sm:ml-6 sm:block sm:max-w-md',
            $html,
            'the desktop search is not being hidden on small screens'
        );

        // One hidden and one shown: not two stacked fields, and not none.
        $this->assertStringContainsString('sm:hidden', $html);
        $this->assertStringContainsString('sm:block', $html);
    }

    public function test_the_phone_row_is_outside_the_max_width_bar(): void
    {
        $html = $this->header();

        // The bar above is max-w-7xl. A field inside it would be capped on a
        // wide phone and stop short of the screen edge on a narrow one.
        $phoneRow = strpos($html, 'px-4 py-2 sm:hidden');

        $this->assertNotFalse($phoneRow);

        // The bar closes before the phone row starts, so the row is a sibling.
        $barEnd = strpos($html, '</div>');

        $this->assertLessThan($phoneRow, $barEnd);
    }

    public function test_the_classifications_still_reach_a_phone(): void
    {
        $html = $this->header();

        $this->assertStringContainsString('id="mobile-menu"', $html);
        $this->assertStringContainsString('aria-controls="mobile-menu"', $html);
        $this->assertStringContainsString('Classifications', $html);
    }

    public function test_the_phone_search_row_cannot_overflow(): void
    {
        // The phone row is a plain padded block with no width of its own, so it
        // fills whatever the screen gives it. A fixed width, or a min-width
        // borrowed from the desktop row, would push the page sideways on a
        // narrow phone.
        $html = $this->header();

        $start = strpos($html, 'px-4 py-2 sm:hidden');
        $this->assertNotFalse($start);

        // The row and everything inside it. The search icon is a long inline
        // SVG, so the window has to be wide enough to reach past it.
        $closing = strpos($html, '</div>', $start + 2000);

        $this->assertNotFalse($closing, 'the phone row never closes');

        $row = substr($html, $start, $closing - $start);

        $this->assertStringNotContainsString('w-[', $row, 'a fixed width can overflow a phone');
        $this->assertStringNotContainsString('min-w-', $row, 'a min-width can overflow a phone');
        $this->assertStringNotContainsString('max-w-', $row, 'the phone row should not be capped');
        $this->assertStringNotContainsString('flex-1', $row, 'the phone row should fill the width, not share it');
    }

    public function test_the_search_still_works_from_the_phone_row(): void
    {
        // Both rows post to the same place, so whichever one is used gives the
        // same results. The markup puts the attributes in a fixed order, so
        // this matches on the two halves rather than the whole string.
        $html = $this->header();

        $this->assertSame(
            2,
            substr_count($html, 'method="GET" role="search"'),
            'both search rows must be search forms'
        );

        // Matched on the path rather than the whole address, so this holds whether
        // the page is served over http locally or https on the platform.
        $this->assertSame(
            2,
            preg_match_all('#<form action="[^"]*/catalog" method="GET" role="search"#', $html),
            'both search rows must submit to the catalogue'
        );

        // And a query from either row reaches the catalogue and filters it. A
        // goods item is created first so the result is this test's own rather
        // than whatever happens to be seeded.
        Product::factory()->create([
            'name' => 'Cavendish Bananas',
            'stock' => 10,
        ]);

        $results = $this->get('/catalog?q=Bananas')
            ->assertOk()
            ->assertSee('Cavendish Bananas');

        $this->assertStringContainsString('value="Bananas"', $results->getContent());
    }

    /*
    |--------------------------------------------------------------------------
    | Classifications added through the admin area
    |--------------------------------------------------------------------------
    */

    public function test_a_manager_can_add_a_classification_and_it_appears(): void
    {
        $before = Category::query()->count();

        $this->actingAs(User::factory()->manager()->create())
            ->post(route('admin.categories.store'), [
                'name' => 'Frozen Foods',
                'description' => 'Kept frozen the whole way.',
                'icon' => 'ice-cream',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertSame($before + 1, Category::query()->count());

        // The slug is worked out from the name rather than submitted, so a form
        // cannot put a clashing or unreadable one in the address bar.
        $this->assertDatabaseHas('categories', ['slug' => 'frozen-foods']);

        // And it is on the storefront, not only in the admin list.
        $this->get('/catalog')->assertOk()->assertSee('Frozen Foods');
    }

    public function test_a_classification_can_be_nested_under_another(): void
    {
        $parent = Category::query()->create([
            'name' => 'Chilled',
            'slug' => 'chilled',
            'icon' => 'bottle',
            'position' => 1,
        ]);

        $this->actingAs(User::factory()->manager()->create())
            ->post(route('admin.categories.store'), [
                'name' => 'Ice Cream',
                'parent_id' => $parent->id,
                'icon' => 'ice-cream',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $child = Category::query()->where('slug', 'ice-cream')->firstOrFail();

        $this->assertSame($parent->id, $child->parent_id);
    }

    public function test_plain_staff_cannot_add_a_classification(): void
    {
        $before = Category::query()->count();

        $this->actingAs(User::factory()->staff()->create())
            ->get(route('admin.categories.create'))
            ->assertForbidden();

        $this->assertSame($before, Category::query()->count());
    }

    public function test_a_clashing_name_gets_its_own_slug_rather_than_a_dupe(): void
    {
        Category::query()->create([
            'name' => 'Frozen Foods',
            'slug' => 'frozen-foods',
            'icon' => 'ice-cream',
            'position' => 1,
        ]);

        $this->actingAs(User::factory()->manager()->create())
            ->post(route('admin.categories.store'), [
                'name' => 'Frozen Foods',
                'icon' => 'box',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.categories.index'));

        // Two rows, two different addresses: neither can shadow the other, and
        // neither form can post a slug to collide on.
        $this->assertSame(2, Category::query()->count());
        $this->assertNotNull(Category::query()->where('slug', 'frozen-foods-2')->first());
    }

    public function test_a_classification_needs_a_name(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->post(route('admin.categories.store'), ['icon' => 'box'])
            ->assertSessionHasErrors('name');
    }

    public function test_an_unknown_icon_is_refused(): void
    {
        // An unknown mark would render as a blank square, so it is caught here
        // rather than left to the page.
        $this->actingAs(User::factory()->manager()->create())
            ->post(route('admin.categories.store'), [
                'name' => 'Bad Icon',
                'icon' => 'not-a-real-icon',
            ])
            ->assertSessionHasErrors('icon');
    }
}
