<?php

namespace App\Cart;

use App\Models\Favourite;
use App\Models\Product;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Wishlist for both guests and signed-in shoppers.
 *
 * Guests are keyed by a random session token; when someone signs in their guest
 * favourites are merged into their account so nothing is lost.
 */
class FavouriteService
{
    private const TOKEN_KEY = 'favourites.token';

    public function __construct(private readonly Session $session) {}

    /**
     * Favourite ids for the current guest or user, memoised per request.
     *
     * @var Collection<int, int>|null
     */
    private ?Collection $ids = null;

    /**
     * The identity the memoised ids belong to, so signing in mid-request or a
     * different session token never reads a stale set.
     */
    private ?string $idsOwner = null;

    public function token(): string
    {
        if (! $this->session->has(self::TOKEN_KEY)) {
            $this->session->put(self::TOKEN_KEY, Str::random(40));
        }

        return (string) $this->session->get(self::TOKEN_KEY);
    }

    /**
     * @return Collection<int, Product>
     */
    public function products(): Collection
    {
        return $this->query()
            ->with('product.category')
            ->get()
            ->pluck('product')
            ->filter()
            ->values();
    }

    public function count(): int
    {
        return $this->ids()->count();
    }

    /**
     * @return Collection<int, int>
     */
    public function ids(): Collection
    {
        $owner = auth()->id() ? 'user:'.auth()->id() : 'guest:'.$this->token();

        if ($this->ids === null || $this->idsOwner !== $owner) {
            $this->ids = $this->query()->pluck('product_id');
            $this->idsOwner = $owner;
        }

        return $this->ids;
    }

    public function has(int $productId): bool
    {
        return $this->ids()->contains($productId);
    }

    public function toggle(Product $product): bool
    {
        $existing = $this->query()->where('product_id', $product->id)->first();

        $this->ids = null;

        if ($existing) {
            $existing->delete();

            return false;
        }

        $userId = auth()->id();

        $this->query()->create([
            'user_id' => $userId,
            'session_id' => $userId ? null : $this->token(),
            'product_id' => $product->id,
        ]);

        return true;
    }

    public function clear(): void
    {
        $this->query()->delete();

        $this->ids = null;
    }

    /**
     * Move a guest's wishlist onto their account, then drop the guest rows.
     */
    public function mergeOnLogin(int $userId): void
    {
        $this->ids = null;

        Favourite::query()
            ->whereNull('user_id')
            ->where('session_id', $this->token())
            ->get()
            ->each(function (Favourite $favourite) use ($userId) {
                $alreadySaved = Favourite::query()
                    ->where('user_id', $userId)
                    ->where('product_id', $favourite->product_id)
                    ->exists();

                if ($alreadySaved) {
                    $favourite->delete();

                    return;
                }

                $favourite->update(['user_id' => $userId, 'session_id' => null]);
            });
    }

    /**
     * @return Builder<Favourite>
     */
    private function query()
    {
        $userId = auth()->id();

        // Never OR these together: a guest's rows all have user_id NULL, so an
        // OR would match every other guest's wishlist instead of just this one.
        return Favourite::query()
            ->where($userId ? 'user_id' : 'session_id', $userId ?: $this->token());
    }
}
