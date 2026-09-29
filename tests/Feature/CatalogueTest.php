<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogueTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ProductSeeder hangs its items off the department tree, so categories come first.
     */
    private function seedCatalogue(): void
    {
        $this->artisan('db:seed', ['--class' => 'CategorySeeder'])->assertSuccessful();
        $this->artisan('db:seed', ['--class' => 'ProductSeeder'])->assertSuccessful();
    }

    public function test_the_homepage_carousel_shows_five_sellable_slides(): void
    {
        $category = Category::factory()->create();

        for ($i = 1; $i <= 7; $i++) {
            $product = Product::factory()->for($category)->create([
                'name' => 'Promo Item '.$i,
                'is_featured' => true,
                'is_active' => true,
                'stock' => 10,
            ]);

            // A photo, or the slide is skipped.
            $product->forceFill(['image' => 'products/test-'.$i.'.jpg'])->save();
            Storage::disk('public')->put('products/test-'.$i.'.jpg', 'fake');
        }

        $response = $this->get(route('catalog.index'))->assertOk();

        // Capped at the configured number of slides.
        $this->assertSame(5, substr_count($response->getContent(), 'data-slide'));
        $this->assertSame(5, substr_count($response->getContent(), 'data-carousel-dot='));
        $response->assertSee('aria-roledescription="carousel"', escape: false);
        $response->assertSee('data-carousel-next', escape: false);
    }

    public function test_the_carousel_skips_items_that_cannot_be_bought(): void
    {
        // Fake the disk before writing, or the files land in the real one.
        Storage::fake('public');

        $category = Category::factory()->create();

        $expired = Product::factory()->for($category)->create([
            'name' => 'Expired Promo', 'is_featured' => true, 'stock' => 10,
            'produced_at' => now()->subMonths(2), 'expires_at' => now()->subDay(),
        ]);
        $expired->forceFill(['image' => 'products/expired.jpg'])->save();
        Storage::disk('public')->put('products/expired.jpg', 'fake');

        $soldOut = Product::factory()->for($category)->create([
            'name' => 'Sold Out Promo', 'is_featured' => true, 'stock' => 0,
        ]);
        $soldOut->forceFill(['image' => 'products/sold-out.jpg'])->save();
        Storage::disk('public')->put('products/sold-out.jpg', 'fake');

        $soon = Product::factory()->for($category)->create([
            'name' => 'Coming Promo', 'is_featured' => true, 'stock' => 10,
            'available_from' => now()->addWeek(),
        ]);
        $soon->forceFill(['image' => 'products/soon.jpg'])->save();
        Storage::disk('public')->put('products/soon.jpg', 'fake');

        $buyable = Product::factory()->for($category)->create([
            'name' => 'Buyable Promo', 'is_featured' => true, 'stock' => 10,
        ]);
        $buyable->forceFill(['image' => 'products/buyable.jpg'])->save();
        Storage::disk('public')->put('products/buyable.jpg', 'fake');

        $html = $this->get(route('catalog.index'))->assertOk()->getContent();

        // Scope to the carousel: the product grid below still lists the expired
        // item, because a shopper should be able to see what is on the shelf.
        preg_match('#<div class="promo-carousel.*?</div>\s*</div>\s*</div>#s', $html, $carousel);
        $markup = $carousel[0] ?? '';

        $this->assertNotSame('', $markup, 'the carousel is rendered');
        $this->assertSame(1, substr_count($markup, 'data-slide'));
        $this->assertStringContainsString('Buyable Promo', $markup);
        $this->assertStringNotContainsString('Expired Promo', $markup);
        $this->assertStringNotContainsString('Sold Out Promo', $markup);
        $this->assertStringNotContainsString('Coming Promo', $markup);
    }

    public function test_a_configured_promo_video_becomes_the_first_slide(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('promo/week.mp4', 'fake-video');

        // A configured path with no file on disk must be ignored, not rendered.
        config()->set('shop.promo.video', 'promo/missing.mp4');
        $this->get(route('catalog.index'))->assertOk()->assertDontSee('promo/missing.mp4', escape: false);

        config()->set('shop.promo.video', 'promo/week.mp4');
        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('promo/week.mp4', escape: false)
            ->assertSee('<video', escape: false);
    }

    public function test_the_hero_uses_the_configured_advertising_copy(): void
    {
        config()->set('shop.hero.headline', 'Fresh this week');

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Fresh this week')
            ->assertSee(config('shop.hero.subline'));
    }

    public function test_public_catalogue_lists_active_products(): void
    {
        $visible = Product::factory()->create(['name' => 'Fresh Mangoes']);
        $hidden = Product::factory()->inactive()->create(['name' => 'Secret Item']);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Fresh Mangoes')
            ->assertDontSee('Secret Item')
            ->assertSee($visible->sku);
    }

    public function test_catalogue_search_matches_sku_brand_and_barcode(): void
    {
        Product::factory()->create(['name' => 'Iced Tea', 'sku' => 'SKU-SEARCH-1', 'brand' => 'Lipton', 'barcode' => '4800000001111']);
        Product::factory()->create(['name' => 'Dish Soap', 'sku' => 'SKU-OTHER-9', 'brand' => 'Joy', 'barcode' => '4800000002222']);

        $this->get(route('catalog.index', ['q' => 'Lipton']))->assertOk()->assertSee('Iced Tea')->assertDontSee('Dish Soap');
        $this->get(route('catalog.index', ['q' => 'SKU-SEARCH-1']))->assertOk()->assertSee('Iced Tea');
        $this->get(route('catalog.index', ['q' => '4800000002222']))->assertOk()->assertSee('Dish Soap');
    }

    public function test_catalogue_can_filter_by_classification_including_children(): void
    {
        $parent = Category::factory()->create(['name' => 'Fresh Produce']);
        $child = Category::factory()->childOf($parent)->create(['name' => 'Fruits']);

        Product::factory()->for($parent)->create(['name' => 'Loose Carrots']);
        Product::factory()->for($child)->create(['name' => 'Cavendish Bananas']);

        $this->get(route('catalog.show', $parent))
            ->assertOk()
            ->assertSee('Loose Carrots')
            ->assertSee('Cavendish Bananas');
    }

    public function test_hidden_classification_returns_not_found(): void
    {
        $category = Category::factory()->create(['is_active' => false]);

        $this->get(route('catalog.show', $category))->assertNotFound();
    }

    /**
     * The navigation bar must never light up two entries at once. The same
     * entries are rendered twice (mobile drawer + desktop bar), so both are
     * checked separately. Only the header is inspected: the catalogue page has
     * its own category filter that legitimately marks a selection too.
     */
    private function selectedNavLabels(string $html): array
    {
        preg_match_all('#<a[^>]*class="([^"]*nav-link[^"]*)"[^>]*>(.*?)</a>#s', $html, $matches, PREG_SET_ORDER);

        $selected = [];

        foreach ($matches as $match) {
            if (! str_contains($match[1], 'nav-link-active')) {
                continue;
            }

            $selected[] = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($match[2]))));
        }

        return $selected;
    }

    private function assertOnlyThisNavEntryIsSelected(string $html, string $label, ?string $mobile = null): void
    {
        preg_match('#<header.*?</header>#s', $html, $header);
        $markup = $header[0] ?? $html;

        $parts = preg_split('#<nav class="hidden[^"]*md:block"#', $markup, 2) ?: [$markup];

        $this->assertSame([$mobile ?? $label], $this->selectedNavLabels($parts[0]), 'mobile menu');
        $this->assertSame([$label], $this->selectedNavLabels($parts[1] ?? ''), 'desktop bar');
    }

    public function test_only_all_goods_is_selected_on_the_catalogue(): void
    {
        $this->get(route('catalog.index'))
            ->assertOk()
            ->tap(fn ($r) => $this->assertOnlyThisNavEntryIsSelected($r->getContent(), 'All goods'));
    }

    public function test_only_new_arrivals_is_selected_on_the_new_arrivals_page(): void
    {
        $this->get(route('catalog.new-arrivals'))
            ->assertOk()
            ->tap(fn ($r) => $this->assertOnlyThisNavEntryIsSelected($r->getContent(), 'New arrivals'));
    }

    public function test_only_the_department_is_selected_on_a_classification_page(): void
    {
        $parent = Category::factory()->create(['name' => 'Fresh Produce']);
        $child = Category::factory()->childOf($parent)->create(['name' => 'Fruits']);

        $this->get(route('catalog.show', $parent))
            ->assertOk()
            ->tap(fn ($r) => $this->assertOnlyThisNavEntryIsSelected($r->getContent(), 'Fresh Produce'));

        // A sub-classification lights up the leaf in the drawer, and the
        // department it sits under in the desktop bar.
        $this->get(route('catalog.show', $child))
            ->assertOk()
            ->tap(fn ($r) => $this->assertOnlyThisNavEntryIsSelected($r->getContent(), 'Fresh Produce', 'Fruits'));
    }

    public function test_the_department_is_selected_on_a_product_page(): void
    {
        $parent = Category::factory()->create(['name' => 'Dairy & Eggs']);
        $child = Category::factory()->childOf($parent)->create(['name' => 'Milk']);
        $product = Product::factory()->for($child)->create();

        $this->get(route('catalog.product', $product))
            ->assertOk()
            ->tap(fn ($r) => $this->assertOnlyThisNavEntryIsSelected($r->getContent(), 'Dairy & Eggs'));
    }

    public function test_product_page_increments_view_counter(): void
    {
        $product = Product::factory()->create();

        $this->get(route('catalog.product', $product))->assertOk();

        $this->assertSame(1, $product->fresh()->views);
    }

    public function test_product_page_404s_for_inactive_item(): void
    {
        $product = Product::factory()->inactive()->create();

        $this->get(route('catalog.product', $product))->assertNotFound();
    }

    public function test_bundled_photo_credits_are_resolvable(): void
    {
        $product = Product::factory()->make(['slug' => 'pineapple']);

        $credit = $product->imageCredit();

        $this->assertIsArray($credit);
        $this->assertNotSame('', $credit['author']);
        $this->assertStringContainsString('wikimedia.org', $credit['source']);

        $this->get(route('catalog.product', Product::factory()->create(['slug' => 'pineapple'])))
            ->assertOk()
            ->assertSee('via Wikimedia Commons');
    }

    public function test_every_manifest_entry_points_at_an_image_that_exists(): void
    {
        $manifest = json_decode((string) file_get_contents(resource_path('data/product-images.json')), true);

        $this->assertNotEmpty($manifest);

        foreach ($manifest as $slug => $entry) {
            $this->assertStringStartsWith('File:', $entry['file'], $slug.' has no locked Commons file');
            $this->assertStringContainsString('commons.wikimedia.org', $entry['source'], $slug.' has no source URL');
        }
    }

    public function test_seeding_falls_back_to_svg_when_no_photo_is_downloaded(): void
    {
        Storage::fake('public');

        $this->artisan('db:seed', ['--class' => 'CategorySeeder'])->assertSuccessful();
        $this->artisan('db:seed', ['--class' => 'ProductSeeder'])->assertSuccessful();

        $products = Product::query()->get();

        $this->assertGreaterThan(0, $products->count());

        foreach ($products as $product) {
            $this->assertStringEndsWith('.svg', (string) $product->image, $product->name.' should have no photo');
            $this->assertTrue(Storage::disk('public')->exists($product->image));
        }
    }

    public function test_a_downloaded_photo_is_preferred_over_the_svg_placeholder(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/pineapple.jpg', 'fake-jpeg-bytes');

        $this->artisan('db:seed', ['--class' => 'CategorySeeder'])->assertSuccessful();
        $this->artisan('db:seed', ['--class' => 'ProductSeeder'])->assertSuccessful();

        $pineapple = Product::query()->where('slug', 'pineapple')->firstOrFail();

        $this->assertSame('products/pineapple.jpg', $pineapple->image);

        $others = Product::query()->where('slug', '!=', 'pineapple')->get();

        foreach ($others as $product) {
            $this->assertStringEndsWith('.svg', (string) $product->image);
        }
    }
}
