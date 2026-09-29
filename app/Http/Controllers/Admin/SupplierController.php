<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupplierRequest;
use App\Models\ActivityLog;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $suppliers = Supplier::query()
            ->withCount('products')
            ->search($request->string('q')->toString() ?: null)
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->string('status')->toString() === 'active'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('admin.suppliers.create', ['supplier' => new Supplier(['is_active' => true])]);
    }

    public function store(SupplierRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = ($data['code'] ?? null) ?: 'SUP-'.str_pad((string) (Supplier::count() + 1), 4, '0', STR_PAD_LEFT);

        $supplier = Supplier::create($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'subject_type' => Supplier::class,
            'subject_id' => $supplier->id,
            'description' => 'Added supplier '.$supplier->name,
        ]);

        return redirect()->route('admin.suppliers.index')->with('success', $supplier->name.' was added.');
    }

    public function edit(Supplier $supplier): View
    {
        return view('admin.suppliers.edit', [
            'supplier' => $supplier,
            'products' => $supplier->products()->orderBy('name')->limit(12)->get(),
        ]);
    }

    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'subject_type' => Supplier::class,
            'subject_id' => $supplier->id,
            'description' => 'Updated supplier '.$supplier->name,
        ]);

        return redirect()->route('admin.suppliers.index')->with('success', $supplier->name.' was updated.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $name = $supplier->name;
        $supplier->delete();

        return back()->with('success', $name.' was removed.');
    }
}
