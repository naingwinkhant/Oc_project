@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Goods'],
    ]" />
@endsection

<x-layouts.app title="Goods" heading="Goods" description="Every item in your catalogue">
    <x-slot:actions>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
            <x-icon name="plus" class="size-4" />
            <span class="hidden sm:inline">Add goods</span>
        </a>
    </x-slot:actions>

    <div class="space-y-4">
        <form method="GET" class="card" data-search-reset-trigger>
            <div class="card-body grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="relative lg:col-span-2">
                    <x-icon name="search" class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Name, SKU, brand or barcode"
                           data-live-search class="input ps-9" aria-label="Search goods">
                </div>

                <div>
                    <select name="category" class="select" data-autosubmit aria-label="Filter by classification">
                        <option value="">All classifications</option>
                        @foreach ($categories as $id => $name)
                            <option value="{{ $id }}" @selected(request('category') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="status" class="select" data-autosubmit aria-label="Filter by status">
                        <option value="">Any status</option>
                        @foreach ([
                            'active' => 'Active only',
                            'inactive' => 'Hidden only',
                            'low' => 'Low stock',
                            'out' => 'Out of stock',
                            'expiring' => 'Expiring soon',
                            'expired' => 'Expired',
                            'coming_soon' => 'Coming soon',
                            'featured' => 'Featured',
                        ] as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="sort" class="select" data-autosubmit aria-label="Sort results">
                        <option value="">Newest first</option>
                        @foreach ([
                            'name' => 'Name A → Z',
                            'name_desc' => 'Name Z → A',
                            'price' => 'Price low → high',
                            'price_desc' => 'Price high → low',
                            'stock' => 'Stock low → high',
                            'stock_desc' => 'Stock high → low',
                        ] as $value => $label)
                            <option value="{{ $value }}" @selected(request('sort') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-primary flex-1">
                        <x-icon name="filter" class="size-4" /> Apply
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary" title="Reset filters">
                        <x-icon name="refresh" class="size-4" />
                    </a>
                </div>
            </div>
        </form>

        <section class="card overflow-hidden">
            <div class="card-header">
                <h2 class="card-title">
                    {{ number_format($products->total()) }}
                    {{ Str::plural('item', $products->total()) }}
                    @if (request()->filled('q'))
                        <span class="font-normal text-ink-500">matching “{{ request('q') }}”</span>
                    @endif
                </h2>
                <p class="text-xs text-ink-400">Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</p>
            </div>

            @if ($products->isEmpty())
                <x-empty-state icon="box" title="No goods found"
                              description="Adjust your filters, or add the first item to this catalogue.">
                    <x-slot:action>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                            <x-icon name="plus" class="size-4" /> Add goods
                        </a>
                    </x-slot:action>
                </x-empty-state>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Goods</th>
                                <th class="hidden lg:table-cell">Classification</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Stock</th>
                                <th class="hidden sm:table-cell">Status</th>
                                <th class="w-px text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            @if ($product->imageUrl())
                                                <img src="{{ $product->imageUrl() }}" alt="" class="size-10 shrink-0 rounded-lg object-cover ring-1 ring-ink-200">
                                            @else
                                                <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-ink-100 text-ink-400">
                                                    <x-icon name="box" class="size-5" />
                                                </span>
                                            @endif
                                            <div class="min-w-0">
                                                <a href="{{ route('admin.products.edit', $product) }}"
                                                   class="block max-w-[16rem] truncate text-sm font-semibold text-ink-900 hover:text-brand-700">
                                                    {{ $product->name }}
                                                </a>
                                                <p class="truncate font-mono text-[0.6875rem] text-ink-400">
                                                    {{ $product->sku }}@if ($product->brand) · {{ $product->brand }} @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden lg:table-cell">
                                        @if ($product->category)
                                            <a href="{{ route('admin.categories.index') }}"
                                               class="badge bg-ink-100 text-ink-600 ring-ink-500/10 hover:bg-ink-200">
                                                {{ $product->category->name }}
                                            </a>
                                        @else
                                            <span class="text-xs text-ink-400">Unclassified</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <x-price :product="$product" size="sm" :show-unit="false" class="justify-end" />
                                        @if ($product->cost_price > 0)
                                            <p class="hidden text-[0.6875rem] text-ink-400 tabular-nums sm:block">
                                                cost {{ \App\Support\Money::format($product->cost_price) }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span @class([
                                            'text-sm font-bold tabular-nums',
                                            'text-rose-600' => $product->isOutOfStock(),
                                            'text-amber-600' => $product->isLowStock(),
                                            'text-ink-900' => ! $product->isLowStock(),
                                        ])>{{ $product->stock }}</span>
                                        <span class="text-xs text-ink-400">{{ $product->unit }}</span>
                                    </td>
                                    <td class="hidden sm:table-cell">
                                        <span class="badge {{ $product->stockStatusTone() }}">{{ $product->stockStatusLabel() }}</span>
                                        <br>
                                        <span class="badge mt-1 {{ $product->expiryStatusTone() }}">{{ $product->expiryStatusLabel() }}</span>
                                        @if ($product->isComingSoon())
                                            <span class="badge mt-1 bg-violet-50 text-violet-700 ring-violet-600/20">
                                                Coming soon · {{ $product->available_from->format('j M') }}
                                            </span>
                                        @endif
                                        @unless ($product->is_active)
                                            <span class="badge mt-1 bg-ink-100 text-ink-500 ring-ink-500/10">Hidden</span>
                                        @endunless
                                        <x-freshness :product="$product" variant="compact" class="mt-1" />
                                    </td>
                                    <td class="text-end">
                                        <div class="flex items-center justify-end gap-0.5">
                                            <a href="{{ route('admin.products.edit', $product) }}" class="btn-icon" title="Edit">
                                                <x-icon name="pencil" class="size-4" />
                                            </a>
                                            <a href="{{ route('catalog.product', $product) }}" class="btn-icon" title="View in catalogue" target="_blank" rel="noopener">
                                                <x-icon name="eye" class="size-4" />
                                            </a>
                                            <x-delete-button name="Delete" icon="trash" label="Delete {{ $product->name }}"
                                                            :action="route('admin.products.destroy', $product)"
                                                            :confirm="'Delete '.$product->name.'? This cannot be undone.'" />
                                        </div>
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
