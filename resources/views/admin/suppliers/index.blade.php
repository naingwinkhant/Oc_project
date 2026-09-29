@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Suppliers'],
    ]" />
@endsection

<x-layouts.app title="Suppliers" heading="Suppliers" description="Vendors who supply your goods">
    <x-slot:actions>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary btn-sm">
            <x-icon name="plus" class="size-4" />
            <span class="hidden sm:inline">Add supplier</span>
        </a>
    </x-slot:actions>

    <div class="space-y-4">
        <form method="GET" class="card">
            <div class="card-body grid gap-3 sm:grid-cols-3">
                <div class="relative sm:col-span-2">
                    <x-icon name="search" class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ request('q') }}" data-live-search
                           placeholder="Search suppliers…" class="input ps-9" aria-label="Search suppliers">
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-primary flex-1">
                        <x-icon name="filter" class="size-4" /> Apply
                    </button>
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
                        <x-icon name="refresh" class="size-4" />
                    </a>
                </div>
            </div>
        </form>

        <section class="card overflow-hidden">
            <div class="card-header">
                <h2 class="card-title">{{ number_format($suppliers->total()) }} suppliers</h2>
            </div>

            @if ($suppliers->isEmpty())
                <x-empty-state icon="truck" title="No suppliers yet"
                              description="Add the vendors you buy stock from to keep traceability.">
                    <x-slot:action>
                        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary btn-sm">
                            <x-icon name="plus" class="size-4" /> Add supplier
                        </a>
                    </x-slot:action>
                </x-empty-state>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Supplier</th>
                                <th class="hidden sm:table-cell">Contact</th>
                                <th class="hidden md:table-cell text-end">Goods</th>
                                <th class="text-end">Status</th>
                                <th class="w-px text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suppliers as $supplier)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-ink-100 text-xs font-bold text-ink-600">
                                                {{ Str::upper(Str::substr($supplier->name, 0, 2)) }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-ink-900">{{ $supplier->name }}</p>
                                                <p class="font-mono text-[0.6875rem] text-ink-400">{{ $supplier->code }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell">
                                        <p class="truncate text-sm text-ink-700">{{ $supplier->contact_name ?: '—' }}</p>
                                        @if ($supplier->phone)
                                            <p class="text-[0.6875rem] text-ink-400">{{ $supplier->phone }}</p>
                                        @endif
                                    </td>
                                    <td class="hidden text-end text-sm text-ink-600 tabular-nums md:table-cell">
                                        {{ $supplier->products_count }}
                                    </td>
                                    <td class="text-end">
                                        <span @class([
                                            'badge',
                                            'bg-emerald-50 text-emerald-700 ring-emerald-600/20' => $supplier->is_active,
                                            'bg-ink-100 text-ink-500 ring-ink-500/10' => ! $supplier->is_active,
                                        ])>{{ $supplier->is_active ? 'Active' : 'Inactive' }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="flex items-center justify-end gap-0.5">
                                            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn-icon" title="Edit">
                                                <x-icon name="pencil" class="size-4" />
                                            </a>
                                            <x-delete-button name="" icon="trash" label="Delete {{ $supplier->name }}"
                                                            :action="route('admin.suppliers.destroy', $supplier)"
                                                            :confirm="'Delete '.$supplier->name.'?'"
                                                            class="btn-icon text-rose-600 hover:bg-rose-50 hover:text-rose-700" />
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-ink-200 px-4 py-3 sm:px-5">
                    {{ $suppliers->links('pagination::tailwind-simple') }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
