<?php

namespace Tests\Feature;

use App\Cart\FavouriteService;
use App\Models\Favourite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavouriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_can_favourite_an_item(): void
    {
        $product = Product::factory()->create(['name' => 'Fresh Basil']);

        $this->get(route('favourites.index'))->assertOk()->assertSee('No favourites yet');

        $this->post(route('favourites.toggle'), ['product_id' => $product->id])
            ->assertRedirect();

        $this->get(route('favourites.index'))->assertOk()->assertSee('Fresh Basil');

        $this->assertSame(1, Favourite::query()->count());
        $this->assertNotNull(Favourite::query()->first()->session_id);
        $this->assertNull(Favourite::query()->first()->user_id);
    }

    public function test_toggling_twice_removes_the_item(): void
    {
        $product = Product::factory()->create();

        $this->post(route('favourites.toggle'), ['product_id' => $product->id]);
        $this->post(route('favourites.toggle'), ['product_id' => $product->id]);

        $this->assertSame(0, Favourite::query()->count());
    }

    public function test_the_header_badge_counts_favourites(): void
    {
        $this->post(route('favourites.toggle'), ['product_id' => Product::factory()->create()->id]);
        $this->post(route('favourites.toggle'), ['product_id' => Product::factory()->create()->id]);

        $this->get(route('catalog.index'))->assertOk()->assertSee('Favourites');
        $this->assertSame(2, app(FavouriteService::class)->count());
    }

    public function test_a_guest_wishlist_merges_into_the_account_on_sign_in(): void
    {
        $keep = Product::factory()->create();
        $merge = Product::factory()->create();
        $user = User::factory()->create(['username' => 'aung']);

        $this->post(route('favourites.toggle'), ['product_id' => $keep->id]);
        $this->post(route('favourites.toggle'), ['product_id' => $merge->id]);

        $this->post(route('login'), ['username' => 'aung', 'password' => 'password']);

        $this->assertAuthenticated();

        // Both guest rows now belong to the user, and the guest token is cleared.
        $this->assertSame(2, Favourite::query()->where('user_id', $user->id)->count());
        $this->assertSame(0, Favourite::query()->whereNull('user_id')->count());
    }

    public function test_a_duplicate_favourite_is_not_created_after_merging(): void
    {
        $product = Product::factory()->create();
        $user = User::factory()->create(['username' => 'thein']);

        Favourite::query()->create(['user_id' => $user->id, 'product_id' => $product->id]);

        $this->post(route('favourites.toggle'), ['product_id' => $product->id]);
        $this->post(route('login'), ['username' => 'thein', 'password' => 'password']);

        $this->assertSame(1, Favourite::query()->where('user_id', $user->id)->where('product_id', $product->id)->count());
    }

    public function test_favourites_can_be_cleared(): void
    {
        $this->post(route('favourites.toggle'), ['product_id' => Product::factory()->create()->id]);

        $this->delete(route('favourites.clear'))->assertRedirect();

        $this->assertSame(0, Favourite::query()->count());
    }

    public function test_each_guest_gets_their_own_wishlist(): void
    {
        $product = Product::factory()->create();

        $this->post(route('favourites.toggle'), ['product_id' => $product->id]);
        $firstToken = app(FavouriteService::class)->token();
        $this->assertSame(1, app(FavouriteService::class)->count());

        // A brand-new session token must not see the first guest's items.
        session(['favourites.token' => 'a-different-guest-token']);

        $this->assertSame(0, app(FavouriteService::class)->count());
        $this->assertNotSame($firstToken, app(FavouriteService::class)->token());
    }

    public function test_a_favourited_heart_is_filled_on_the_catalogue_and_product_pages(): void
    {
        $product = Product::factory()->create(['name' => 'Carrots']);

        $this->post(route('favourites.toggle'), ['product_id' => $product->id]);

        foreach ([route('catalog.index'), route('catalog.product', $product)] as $url) {
            $response = $this->get($url)->assertOk();

            $response->assertSee('Remove from favourites');
            $response->assertSee('fill-rose-600', escape: false);
        }
    }

    public function test_an_unfavourited_heart_stays_outlined(): void
    {
        $product = Product::factory()->create();

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Save to favourites')
            ->assertDontSee('Remove from favourites');
    }
}
