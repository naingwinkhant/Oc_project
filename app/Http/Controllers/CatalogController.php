<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $term = $request->string('q')->toString() ?: null;

        $products = Product::query()
            ->with('category')
            ->active()
            ->search($term)
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->whereIn('category_id', Category::find($request->integer('category'))?->descendantIds() ?? [0]);
            })
            ->when($request->boolean('in_stock'), fn ($q) => $q->where('stock', '>', 0))
            ->when($request->boolean('on_sale'), fn ($q) => $q->featured())
            ->when($request->boolean('coming_soon'), fn ($q) => $q->comingSoon())
            ->when(
                $request->filled('sort'),
                fn ($q) => $this->applySort($q, $request->string('sort')->toString()),
                fn ($q) => $q->orderBy('name')
            )
            ->paginate(24)
            ->withQueryString();

        return view('catalog.index', [
            'products' => $products,
            'categories' => $this->navigation(),
            'brands' => Product::query()->active()->whereNotNull('brand')->distinct()->orderBy('brand')->limit(20)->pluck('brand'),
            'selectedCategory' => $request->filled('category') ? Category::find($request->integer('category')) : null,
            'promo' => $this->promoSlides(),
        ]);
    }

    /**
     * Slides for the hero carousel: the configured video first, then the best
     * advertising material in the catalogue — promotions and new arrivals,
     * because those are the things a shopper is meant to notice.
     *
     * @return array<int, array<string, mixed>>
     */
    private function promoSlides(): array
    {
        $slides = [];

        $video = config('shop.promo.video');

        // Only offered once the file is actually there, so a missing upload
        // cannot leave a black rectangle at the top of the page.
        if ($video && Storage::disk('public')->exists($video)) {
            $poster = config('shop.promo.video_poster');

            $slides[] = [
                'type' => 'video',
                'src' => Storage::disk('public')->url($video),
                'poster' => $poster && Storage::disk('public')->exists($poster)
                    ? Storage::disk('public')->url($poster)
                    : null,
                'eyebrow' => 'Watch',
                'title' => config('shop.promo.video_title', 'This week at the store'),
                'text' => null,
                'url' => null,
            ];
        }

        $limit = max(1, (int) config('shop.promo.slides', 5));

        $products = Product::query()
            ->with('category')
            ->active()
            ->whereNotNull('image')
            ->where(fn ($q) => $q->featured()->orWhere('is_new', true))
            ->orderByDesc('is_featured')
            ->latest('id')
            // Over-fetch, because anything unsellable or without a real photo
            // is dropped below and must not eat a slot.
            ->limit($limit * 4)
            ->get()
            ->filter(fn (Product $product) => $product->isSellable() && $product->imageUrl())
            ->take($limit);

        foreach ($products as $product) {
            $slides[] = [
                'type' => 'image',
                'src' => $product->imageUrl(),
                'eyebrow' => $product->hasDiscount() ? 'On promotion' : ($product->isNewArrival() ? 'New arrival' : $product->category?->name),
                'title' => $product->name,
                'text' => $product->hasDiscount()
                    ? $product->discountPercent().'% off this week'
                    : null,
                'price' => Money::format($product->effectivePrice()),
                'was' => $product->hasDiscount() ? Money::format($product->price) : null,
                'url' => route('catalog.product', $product),
                'alt' => $product->name,
            ];
        }

        return $slides;
    }

    public function newArrivals(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->active()
            ->newArrivals()
            ->when($request->boolean('in_stock'), fn ($q) => $q->where('stock', '>', 0))
            ->when(
                $request->filled('sort'),
                fn ($q) => $this->applySort($q, $request->string('sort')->toString()),
                fn ($q) => $q->orderByDesc('created_at')
            )
            ->paginate(24)
            ->withQueryString();

        return view('catalog.new-arrivals', [
            'products' => $products,
            'categories' => $this->navigation(),
            'days' => (int) config('shop.freshness.new_arrival_days', 30),
        ]);
    }

    public function show(Request $request, Category $category): View
    {
        abort_unless($category->is_active, 404);

        $term = $request->string('q')->toString() ?: null;
        $activeId = $category->id;

        while (Category::whereKey($activeId)->value('parent_id')) {
            $activeId = Category::whereKey($activeId)->value('parent_id');
        }

        $categories = $category->children()->active()->orderBy('position')->get();

        $products = Product::query()
            ->with('category')
            ->active()
            ->inCategory($category)
            ->search($term)
            ->when($request->boolean('in_stock'), fn ($q) => $q->where('stock', '>', 0))
            ->when(
                $request->filled('sort'),
                fn ($q) => $this->applySort($q, $request->string('sort')->toString()),
                fn ($q) => $q->orderBy('name')
            )
            ->paginate(24)
            ->withQueryString();

        return view('catalog.category', [
            'category' => $category,
            'children' => $categories,
            'products' => $products,
            'categories' => $this->navigation(),
            'root' => Category::find($activeId),
        ]);
    }

    public function product(Request $request, Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->increment('views');

        $related = Product::query()
            ->with('category')
            ->active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->limit(4)
            ->get();

        if ($related->count() < 4) {
            $related = $related->concat(
                Product::query()
                    ->with('category')
                    ->active()
                    ->where('brand', $product->brand)
                    ->whereNotNull('brand')
                    ->whereKeyNot($product->id)
                    ->whereNotIn('id', $related->pluck('id'))
                    ->limit(4 - $related->count())
                    ->get()
            );
        }

        return view('catalog.product', [
            'product' => $product,
            'related' => $related,
            'categories' => $this->navigation(),
            'movements' => $product->stockMovements()->with('user:id,name')->limit(5)->get(),
        ]);
    }

    private function navigation()
    {
        $roots = Category::query()
            ->active()
            ->roots()
            ->with(['children' => fn ($q) => $q->active()->orderBy('position')])
            ->withCount('products')
            ->get();

        $byCategory = Product::query()
            ->active()
            ->whereNotNull('category_id')
            ->selectRaw('category_id, COUNT(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        return $roots->each(function (Category $root) use ($byCategory) {
            $total = 0;

            foreach (array_merge([$root->id], $root->children->pluck('id')->all()) as $id) {
                $total += (int) $byCategory->get($id, 0);
            }

            $root->setAttribute('branch_products_count', $total);
        });
    }

    private function applySort($query, string $sort)
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->latest(),
            'popular' => $query->orderByDesc('views'),
            'name' => $query->orderBy('name'),
            default => $query->orderBy('name'),
        };
    }
}
