@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Stock movements', 'url' => route('admin.stock.index')],
        ['label' => 'Low stock'],
    ]" />
@endsection

<x-layouts.app title="Low stock" heading="Low stock alerts" description="Items at or below their reorder threshold">
    <div class="space-y-4">
        <form method="GET" class="card">
            <div class="card-body grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="relative lg:col-span-2">
                    <x-icon name="search" class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ request('q') }}" data-live-search
                           placeholder="Search goods or SKU…" class="input ps-9" aria-label="Search">
                </div>

                <select name="category" class="select" data-autosubmit aria-label="Filter by classification">
                    <option value="">All classifications</option>
                    @foreach ($categories as $id => $name)
                        <option value="{{ $id }}" @selected(request('category') == $id)>{{ $name }}</option>
                    @endforeach
                </select>

                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-primary flex-1">
                        <x-icon name="filter" class="size-4" /> Apply
                    </button>
                    <a href="{{ route('admin.stock.low') }}" class="btn btn-secondary">
                        <x-icon name="refresh" class="size-4" />
                    </a>
                </div>
            </div>
        </form>

        <section class="card overflow-hidden">
            <div class="card-header">
                <h2 class="card-title">{{ number_format($products->total()) }} items need attention</h2>
                <a href="{{ route('admin.stock.index') }}" class="btn btn-secondary btn-sm">
                    <x-icon name="clipboard" class="size-4" /> Movement log
                </a>
            </div>

            @if ($products->isEmpty())
                <x-empty-state icon="check" title="All shelves are healthy"
                              description="No goods item is at or below its reorder threshold right now." />
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Goods</th>
                                <th class="hidden md:table-cell">Classification</th>
                                <th class="text-end">On hand</th>
                                <th class="text-end">Threshold</th>
                                <th class="hidden sm:table-cell">Status</th>
                                <th class="w-px text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $item)
                                <tr>
                                    <td>
                                        <p class="max-w-[18rem] truncate text-sm font-semibold text-ink-900">{{ $item->name }}</p>
                                        <p class="font-mono text-[0.6875rem] text-ink-400">{{ $item->sku }}</p>
                                    </td>
                                    <td class="hidden md:table-cell">
                                        @if ($item->category)
                                            <span class="badge bg-ink-100 text-ink-600 ring-ink-500/10">{{ $item->category->name }}</span>
                                        @else
                                            <span class="text-xs text-ink-400">Unclassified</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span @class([
                                            'text-sm font-bold tabular-nums',
                                            'text-rose-600' => $item->isOutOfStock(),
                                            'text-amber-600' => $item->isLowStock(),
                                        ])>{{ $item->stock }}</span>
                                        <span class="text-xs text-ink-400">{{ $item->unit }}</span>
                                    </td>
                                    <td class="text-end text-sm text-ink-500 tabular-nums">{{ $item->min_stock }}</td>
                                    <td class="hidden sm:table-cell">
                                        <span class="badge {{ $item->stockStatusTone() }}">{{ $item->stockStatusLabel() }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.products.edit', $item) }}" class="btn btn-soft btn-sm">
                                            <x-icon name="arrow-down" class="size-3.5" /> Restock
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-ink-200 px-4 py-3 sm:px-5">
                    {{ $products->links('pagination::tailwind-simple') }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
