<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->search($request->string('q')->toString() ?: null)
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->integer('category')))
            ->when($request->filled('brand'), fn ($q) => $q->where('brand', $request->string('brand')->toString()))
            ->when($request->filled('status'), function ($q) use ($request) {
                match ($request->string('status')->toString()) {
                    'active' => $q->where('is_active', true),
                    'inactive' => $q->where('is_active', false),
                    'low' => $q->lowStock(),
                    'out' => $q->where('stock', '<=', 0),
                    'featured' => $q->featured(),
                    'expiring' => $q->expiringSoon(),
                    'expired' => $q->expired(),
                    'coming_soon' => $q->comingSoon(),
                    default => $q,
                };
            })
            ->when($request->filled('price_from'), fn ($q) => $q->where('price', '>=', (float) $request->input('price_from')))
            ->when($request->filled('price_to'), fn ($q) => $q->where('price', '<=', (float) $request->input('price_to')))
            ->when(
                $request->filled('sort'),
                fn ($q) => $this->applySort($q, $request->string('sort')->toString()),
                fn ($q) => $q->latest()
            )
            ->paginate(15)
            ->withQueryString();

        $categories = Category::query()
            ->orderBy('name')
            ->pluck('name', 'id');

        $brands = Product::query()
            ->whereNotNull('brand')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('path')->pluck('name', 'id');

        return view('admin.products.create', [
            'product' => new Product([
                'unit' => 'pcs',
                'min_stock' => 5,
                'is_active' => true,
            ]),
            'categories' => $categories,
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ($data['slug'] ?? null) ?: Product::buildSlug($data['name']);
        $data['sku'] = ($data['sku'] ?? null) ?: Product::buildSku();
        $data['cost_price'] = $data['cost_price'] ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'subject_type' => Product::class,
            'subject_id' => $product->id,
            'description' => 'Added goods item '.$product->name,
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', $product->name.' was added to the catalogue.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::query()->orderBy('path')->pluck('name', 'id');

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ($data['slug'] ?? null) ?: Product::buildSlug($data['name'], $product->id);
        $data['cost_price'] = $data['cost_price'] ?? 0;

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'subject_type' => Product::class,
            'subject_id' => $product->id,
            'description' => 'Updated goods item '.$product->name,
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', $product->name.' was updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;
        $product->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'subject_type' => Product::class,
            'subject_id' => $product->id,
            'description' => 'Removed goods item '.$name,
        ]);

        return back()->with('success', $name.' was removed from the catalogue.');
    }

    private function applySort($query, string $sort)
    {
        return match ($sort) {
            'name' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'price' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'stock' => $query->orderBy('stock'),
            'stock_desc' => $query->orderByDesc('stock'),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };
    }
}
