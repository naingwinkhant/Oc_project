@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Stock movements'],
    ]" />
@endsection

<x-layouts.app title="Stock movements" heading="Stock movements" description="Every goods in, goods out and adjustment">
    <div class="space-y-4">
        <section class="grid gap-3 sm:grid-cols-3">
            <x-stat-card label="Stock in" :value="number_format($summary['in'])" icon="arrow-down" tone="emerald" hint="this month" />
            <x-stat-card label="Stock out" :value="number_format($summary['out'])" icon="arrow-up" tone="rose" hint="this month" />
            <x-stat-card label="Entries today" :value="number_format($summary['today'])" icon="clipboard" tone="sky" />
        </section>

        <div class="grid gap-5 xl:grid-cols-3">
            <div class="space-y-4 xl:col-span-2">
                <form method="GET" class="card">
                    <div class="card-body grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="relative lg:col-span-2">
                            <x-icon name="search" class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-ink-400" />
                            <input type="search" name="q" value="{{ request('q') }}" data-live-search
                                   placeholder="Search goods or SKU…" class="input ps-9" aria-label="Search movements">
                        </div>

                        <select name="type" class="select" data-autosubmit aria-label="Filter by movement type">
                            <option value="">All types</option>
                            @foreach (\App\Enums\StockMovementType::cases() as $type)
                                <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>

                        <input type="date" name="from" value="{{ request('from') }}" class="input" data-autosubmit
                               title="From date" aria-label="From date">

                        <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-4">
                            <button type="submit" class="btn btn-primary flex-1">
                                <x-icon name="filter" class="size-4" /> Apply filters
                            </button>
                            <a href="{{ route('admin.stock.index') }}" class="btn btn-secondary">
                                <x-icon name="refresh" class="size-4" /> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <section class="card overflow-hidden">
                    <div class="card-header">
                        <h2 class="card-title">{{ number_format($movements->total()) }} movements</h2>
                        <a href="{{ route('admin.stock.low') }}" class="btn btn-secondary btn-sm">
                            <x-icon name="alert" class="size-4" /> Low stock
                        </a>
                    </div>

                    @if ($movements->isEmpty())
                        <x-empty-state icon="clipboard" title="No movements recorded"
                                      description="Use the form on the right to log goods arriving or leaving the shelf." />
                    @else
                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Goods</th>
                                        <th class="text-end">Change</th>
                                        <th class="hidden sm:table-cell text-end">Balance</th>
                                        <th class="hidden lg:table-cell">Recorded by</th>
                                        <th class="hidden md:table-cell text-end">When</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($movements as $movement)
                                        <tr>
                                            <td>
                                                <span @class(['inline-flex items-center gap-1.5 text-xs font-semibold', 'text-emerald-700' => $movement->isPositive(), 'text-rose-700' => ! $movement->isPositive()])>
                                                    <x-icon :name="$movement->type->icon()" class="size-3.5" />
                                                    {{ $movement->type->label() }}
                                                </span>
                                                @if ($movement->reason)
                                                    <p class="mt-0.5 max-w-[14rem] truncate text-[0.6875rem] text-ink-400">{{ $movement->reason }}</p>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.products.edit', $movement->product_id) }}"
                                                   class="block max-w-[16rem] truncate text-sm font-semibold text-ink-900 hover:text-brand-700">
                                                    {{ $movement->product?->name ?? 'Deleted item' }}
                                                </a>
                                                <p class="font-mono text-[0.6875rem] text-ink-400">{{ $movement->product?->sku }}</p>
                                            </td>
                                            <td class="text-end">
                                                <span @class([
                                                    'text-sm font-bold tabular-nums',
                                                    'text-emerald-600' => $movement->isPositive(),
                                                    'text-rose-600' => ! $movement->isPositive(),
                                                ])>
                                                    {{ $movement->isPositive() ? '+' : '' }}{{ $movement->quantity }}
                                                </span>
                                            </td>
                                            <td class="hidden text-end text-sm text-ink-600 tabular-nums sm:table-cell">
                                                {{ $movement->balance_after }}
                                            </td>
                                            <td class="hidden lg:table-cell">
                                                <span class="text-sm text-ink-600">{{ $movement->user?->name ?? 'System' }}</span>
                                            </td>
                                            <td class="hidden whitespace-nowrap text-right text-xs text-ink-400 md:table-cell">
                                                {{ $movement->created_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="border-t border-ink-200 px-4 py-3 sm:px-5">
                            {{ $movements->links('pagination::tailwind-simple') }}
                        </div>
                    @endif
                </section>
            </div>

            <section class="card h-fit">
                <div class="card-header">
                    <h2 class="card-title">Record movement</h2>
                </div>
                <form method="POST" action="{{ route('admin.stock.store') }}" class="card-body space-y-4">
                    @csrf

                    <div>
                        <label for="product_id" class="label">Goods item</label>
                        <select id="product_id" name="product_id" required
                                class="select @if ($errors->has('product_id')) input-error @endif">
                            <option value="">Choose an item…</option>
                            @foreach ($products as $item)
                                <option value="{{ $item->id }}" @selected(old('product_id') == $item->id)>
                                    {{ $item->name }} ({{ $item->stock }} {{ $item->unit }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <span class="label">Movement type</span>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach (\App\Enums\StockMovementType::cases() as $type)
                                <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-ink-200 px-3 py-2 text-sm font-medium text-ink-700 transition hover:bg-ink-50 has-checked:border-brand-500 has-checked:bg-brand-50 has-checked:text-brand-700">
                                    <input type="radio" name="type" value="{{ $type->value }}" class="sr-only"
                                           @checked(old('type', 'in') === $type->value)>
                                    <x-icon :name="$type->icon()" class="size-4" />
                                    {{ $type->label() }}
                                </label>
                            @endforeach
                        </div>
                        @error('type')
                            <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <x-form-field field="quantity" label="Quantity" type="number" min="1" value="1" required
                                  hint="Units received or issued." />

                    <x-form-field field="reason" label="Reason" placeholder="Delivery PO-2291, spoilage, stock count…" />

                    <x-form-field field="reference" label="Reference" placeholder="PO number, invoice, ticket…" />

                    <button type="submit" class="btn btn-primary w-full">
                        <x-icon name="check" class="size-4" /> Save movement
                    </button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.app>
