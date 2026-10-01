<x-layouts.app title="Staff" heading="Staff page"
               description="Goods and stock: the two things you can act on">
    <div class="space-y-5">
        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Goods" :value="number_format($stats['goods'])" icon="box" tone="brand" />
            <x-stat-card label="Low stock" :value="number_format($stats['lowStock'])" icon="alert" tone="rose" />
            <x-stat-card label="Out of stock" :value="number_format($stats['outOfStock'])" icon="x" tone="amber" />
            <x-stat-card label="Orders to pick" :value="number_format($stats['ordersToPick'])" icon="clipboard"
                         tone="emerald" hint="paid and waiting" />
        </section>

        <div class="grid items-start gap-4 xl:grid-cols-3">
            <section class="card overflow-hidden xl:col-span-2">
                <div class="card-header">
                    <h2 class="card-title">Running low</h2>
                    <a href="{{ route('admin.stock.low') }}" class="btn btn-ghost btn-sm">
                        All low stock <x-icon name="arrow-right" class="size-4" />
                    </a>
                </div>

                @if ($lowStock->isEmpty())
                    <x-empty-state icon="check" title="Nothing is running low"
                                  description="Every item is above its reorder threshold." />
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Goods</th>
                                    <th class="text-end">On hand</th>
                                    <th class="hidden text-end sm:table-cell">Reorder at</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lowStock as $product)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.products.edit', $product) }}"
                                               class="text-sm font-semibold text-ink-900 hover:text-brand-700">
                                                {{ $product->name }}
                                            </a>
                                            <p class="font-mono text-[0.6875rem] text-ink-400">{{ $product->sku }}</p>
                                        </td>
                                        <td class="text-end">
                                            <span @class([
                                                'text-sm font-semibold tabular-nums',
                                                'text-rose-600' => $product->stock <= 0,
                                                'text-ink-900' => $product->stock > 0,
                                            ])>{{ $product->stock }}</span>
                                        </td>
                                        <td class="hidden text-end text-sm tabular-nums text-ink-500 sm:table-cell">
                                            {{ $product->min_stock }}
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.stock.index', ['product' => $product->id]) }}"
                                               class="btn btn-secondary btn-sm">Record movement</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <div class="space-y-4">
                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title">Your work</h2>
                    </div>
                    <div class="card-body space-y-1.5">
                        @foreach ([
                            ['admin.products.index', 'box', 'Goods'],
                            ['admin.stock.index', 'clipboard', 'Stock movements'],
                            ['admin.stock.low', 'alert', 'Low stock'],
                            ['admin.orders.index', 'clipboard', 'Orders'],
                        ] as [$route, $icon, $label])
                            <a href="{{ route($route) }}" class="nav-link">
                                <x-icon :name="$icon" class="size-4 shrink-0" />
                                <span class="truncate">{{ $label }}</span>
                                <x-icon name="chevron-right" class="ms-auto size-4 shrink-0 opacity-40" />
                            </a>
                        @endforeach
                    </div>
                </section>

                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title">What you cannot do</h2>
                    </div>
                    <div class="card-body text-sm leading-relaxed text-ink-600">
                        <p>
                            Users, roles, classifications and suppliers are handled by an
                            administrator or manager. If you need a change there, ask one of them.
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts.app>