<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StockMovementType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockMovementRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $movements = StockMovement::query()
            ->with(['product:id,name,sku,unit', 'user:id,name'])
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')->toString()))
            ->when($request->filled('product'), fn ($q) => $q->where('product_id', $request->integer('product')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $like = '%'.$request->string('q')->toString().'%';
                $q->whereHas('product', fn ($sub) => $sub->where('name', 'like', $like)->orWhere('sku', 'like', $like));
            })
            ->when(
                $request->filled('from'),
                fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')),
                fn ($q) => $q->where('created_at', '>=', now()->subDays(30))
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $summary = [
            'in' => StockMovement::query()->ofType(StockMovementType::In)->where('created_at', '>=', now()->startOfMonth())->sum('quantity'),
            'out' => abs(StockMovement::query()->ofType(StockMovementType::Out)->where('created_at', '>=', now()->startOfMonth())->sum('quantity')),
            'today' => StockMovement::query()->whereDate('created_at', now())->count(),
        ];

        return view('admin.stock.index', [
            'movements' => $movements,
            'summary' => $summary,
            'products' => Product::query()->orderBy('name')->get(['id', 'name', 'sku', 'unit', 'stock']),
        ]);
    }

    public function low(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->lowStock()
            ->search($request->string('q')->toString() ?: null)
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->integer('category')))
            ->orderByRaw('CASE WHEN stock = 0 THEN 0 ELSE 1 END')
            ->orderBy('stock')
            ->orderBy('min_stock')
            ->paginate(20)
            ->withQueryString();

        return view('admin.stock.low', [
            'products' => $products,
            'categories' => Category::query()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(StockMovementRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $type = StockMovementType::from($data['type']);
        $product = Product::findOrFail($data['product_id']);

        $product->recordMovement(
            type: $type,
            quantity: (int) $data['quantity'],
            reason: $data['reason'] ?? null,
            reference: $data['reference'] ?? null,
        );

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'stock_'.($type->isPositive() ? 'in' : 'out'),
            'subject_type' => Product::class,
            'subject_id' => $product->id,
            'description' => $type->label().' '.$data['quantity'].' '.$product->unit.' of '.$product->name,
            'properties' => ['balance_after' => $product->fresh()->stock],
        ]);

        return back()->with('success', $type->label().' recorded for '.$product->name.'.');
    }
}
