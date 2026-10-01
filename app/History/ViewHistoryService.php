<?php

namespace App\History;

use App\Models\Product;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * What the shopper looked at, most recent first.
 *
 * A guest's list is keyed by a random session token; on sign-in it is merged
 * into the account so the history follows them to another device.
 */
class ViewHistoryService
{
    private const TOKEN_KEY = 'history.token';

    /** How many items the history keeps. */
    public const LIMIT = 24;

    public function __construct(private readonly Session $session) {}

    public function token(): string
    {
        if (! $this->session->has(self::TOKEN_KEY)) {
            $this->session->put(self::TOKEN_KEY, Str::random(40));
        }

        return (string) $this->session->get(self::TOKEN_KEY);
    }

    public function record(Product $product): void
    {
        $userId = auth()->id();

        DB::table('product_views')->updateOrInsert(
            $userId
                ? ['user_id' => $userId, 'product_id' => $product->id]
                : ['session_id' => $this->token(), 'product_id' => $product->id],
            ['viewed_at' => now()]
        );

        $this->trim();
    }

    /**
     * @return Collection<int, Product>
     */
    public function products(): Collection
    {
        $userId = auth()->id();

        $ids = DB::table('product_views')
            ->when($userId, fn ($q) => $q->where('user_id', $userId), fn ($q) => $q->where('session_id', $this->token()))
            ->orderByDesc('viewed_at')
            ->limit(self::LIMIT)
            ->pluck('product_id');

        if ($ids->isEmpty()) {
            return new Collection;
        }

        // Keep the order the query gave us rather than the table's default.
        return Product::query()
            ->with('category')
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Product $product) => $ids->search($product->id))
            ->values();
    }

    public function count(): int
    {
        return DB::table('product_views')
            ->when(auth()->id(), fn ($q) => $q->where('user_id', auth()->id()), fn ($q) => $q->where('session_id', $this->token()))
            ->count();
    }

    public function clear(): void
    {
        DB::table('product_views')
            ->when(auth()->id(), fn ($q) => $q->where('user_id', auth()->id()), fn ($q) => $q->where('session_id', $this->token()))
            ->delete();
    }

    /**
     * Move a guest's history onto the account on sign-in.
     */
    public function mergeOnLogin(int $userId): void
    {
        $guestRows = DB::table('product_views')->whereNull('user_id')->where('session_id', $this->token())->get();

        foreach ($guestRows as $row) {
            $already = DB::table('product_views')
                ->where('user_id', $userId)
                ->where('product_id', $row->product_id)
                ->exists();

            if ($already) {
                DB::table('product_views')->where('id', $row->id)->delete();

                continue;
            }

            DB::table('product_views')->where('id', $row->id)->update([
                'user_id' => $userId,
                'session_id' => null,
            ]);
        }
    }

    /**
     * Keep only the most recent items per viewer, so the table cannot grow
     * without bound as somebody browses.
     */
    private function trim(): void
    {
        $userId = auth()->id();
        $column = $userId ? 'user_id' : 'session_id';
        $value = $userId ?? $this->token();

        $stale = DB::table('product_views')
            ->where($column, $value)
            ->orderByDesc('viewed_at')
            ->skip(self::LIMIT)
            ->take(1)
            ->value('id');

        while ($stale !== null) {
            DB::table('product_views')->where('id', $stale)->delete();

            $stale = DB::table('product_views')
                ->where($column, $value)
                ->orderByDesc('viewed_at')
                ->skip(self::LIMIT)
                ->take(1)
                ->value('id');
        }
    }
}
