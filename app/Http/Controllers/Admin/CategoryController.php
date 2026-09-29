<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Services\CategoryTreeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryTreeService $tree) {}

    public function index(Request $request): View
    {
        $tree = $this->tree->nest(
            Category::query()
                ->withCount('products')
                ->orderBy('position')
                ->orderBy('name')
                ->get()
        );

        $flat = Category::query()
            ->when($request->filled('q'), fn ($q) => $q->search($request->string('q')->toString()))
            ->orderBy('path')
            ->get();

        $totals = [
            'categories' => Category::query()->count(),
            'active' => Category::query()->active()->count(),
            'orphans' => Category::query()->whereNull('parent_id')->count(),
            'products' => Product::query()->count(),
        ];

        return view('admin.categories.index', [
            'tree' => $tree,
            'flat' => $flat,
            'totals' => $totals,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new Category(['is_active' => true, 'color' => 'emerald']),
            'parents' => $this->parentOptions(),
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Category::buildSlug($data['name']);
        $data['position'] = (int) Category::where('parent_id', $data['parent_id'] ?? null)->max('position') + 1;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create($data);
        $this->tree->rebuildSubtree($category->id);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'description' => 'Created classification '.$category->name,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', $category->name.' classification was created.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
            'parents' => $this->parentOptions($category->id),
            'products' => $category->products()->with('category')->orderBy('name')->limit(12)->get(),
        ]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();
        $data['position'] = $category->position;

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);
        $this->tree->rebuildSubtree($category->id);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'description' => 'Updated classification '.$category->name,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', $category->name.' was updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $name = $category->name;
        $childCount = $category->children()->count();
        $productCount = $category->products()->count();

        $category->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'description' => 'Deleted classification '.$name,
        ]);

        $message = $childCount > 0
            ? "{$name} was removed. {$childCount} sub-classification(s) moved to the top level."
            : ($productCount > 0
                ? "{$name} was removed. {$productCount} goods item(s) are now unclassified."
                : "{$name} was removed.");

        return back()->with('success', $message);
    }

    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*.id' => ['required', 'integer', 'exists:categories,id'],
            'order.*.position' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($data['order'] as $row) {
            Category::whereKey($row['id'])->update(['position' => $row['position']]);
        }

        return response()->json(['ok' => true, 'message' => 'Classification order saved.']);
    }

    public function toggle(Request $request, Category $category): JsonResponse
    {
        $category->update(['is_active' => ! $category->is_active]);

        return response()->json([
            'ok' => true,
            'is_active' => $category->is_active,
            'message' => $category->name.' is now '.($category->is_active ? 'visible' : 'hidden').'.',
        ]);
    }

    private function parentOptions(?int $excludeId = null)
    {
        return Category::query()
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->orderBy('path')
            ->get(['id', 'name', 'path'])
            ->reject(fn (Category $c) => $excludeId && $this->tree->isDescendant($excludeId, $c->id))
            ->mapWithKeys(fn (Category $c) => [$c->id => str_repeat('— ', $c->depth).$c->name]);
    }
}
