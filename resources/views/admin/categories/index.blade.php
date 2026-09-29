@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Classifications'],
    ]" />
@endsection

<x-layouts.app title="Classifications" heading="Classifications" description="Organise goods into a nested department tree">
    <x-slot:actions>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
            <x-icon name="plus" class="size-4" />
            <span class="hidden sm:inline">New classification</span>
        </a>
    </x-slot:actions>

    <div class="space-y-4">
        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Total" :value="number_format($totals['categories'])" icon="layers" tone="brand" hint="all levels" />
            <x-stat-card label="Active" :value="number_format($totals['active'])" icon="check" tone="emerald" hint="visible to shoppers" />
            <x-stat-card label="Departments" :value="number_format($totals['orphans'])" icon="tag" tone="sky" hint="top level only" />
            <x-stat-card label="Goods classified" :value="number_format($totals['products'])" icon="box" tone="violet" />
        </section>

        <div class="grid gap-5 xl:grid-cols-3">

            <section class="card overflow-hidden xl:col-span-2">
                <div class="card-header">
                    <h2 class="card-title">Department tree</h2>
                    <p class="text-xs text-ink-400">Click a chevron to expand sub-classifications</p>
                </div>

                @if (empty($tree))
                    <x-empty-state icon="layers" title="No classifications yet"
                                  description="Start with a top-level department like Fresh Produce, then nest aisles under it.">
                        <x-slot:action>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                                <x-icon name="plus" class="size-4" /> Create first classification
                            </a>
                        </x-slot:action>
                    </x-empty-state>
                @else
                    <ul class="divide-y divide-ink-100">
                        @foreach ($tree as $node)
                            <li class="px-3 py-2.5 sm:px-4">
                                <x-admin.category-node :node="$node" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <div class="space-y-5">
                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title">Search classifications</h2>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="relative">
                            <x-icon name="search" class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-ink-400" />
                            <input type="search" name="q" value="{{ request('q') }}" data-live-search
                                   placeholder="Search names and slugs…" class="input ps-9" aria-label="Search classifications">
                        </form>

                        @if (request()->filled('q'))
                            <ul class="mt-4 space-y-1">
                                @forelse ($flat as $category)
                                    <li>
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                           class="flex items-center gap-2 rounded-lg px-2 py-2 text-sm transition hover:bg-ink-50">
                                            <span class="truncate text-ink-700">{{ $category->name }}</span>
                                            <span class="ms-auto shrink-0 text-xs text-ink-400 tabular-nums">
                                                {{ $category->products_count }}
                                            </span>
                                        </a>
                                    </li>
                                @empty
                                    <li class="py-6 text-center text-sm text-ink-400">No matches found.</li>
                                @endforelse
                            </ul>
                        @endif
                    </div>
                </section>

                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title">Tips for a clean tree</h2>
                    </div>
                    <div class="card-body space-y-3 text-sm text-ink-600">
                        <p class="flex gap-2.5">
                            <x-icon name="check" class="mt-0.5 size-4 shrink-0 text-brand-600" />
                            <span>Keep it to <strong class="font-semibold text-ink-900">two levels</strong> — department then aisle. Shoppers scan fast.</span>
                        </p>
                        <p class="flex gap-2.5">
                            <x-icon name="check" class="mt-0.5 size-4 shrink-0 text-brand-600" />
                            <span>One classification should hold <strong class="font-semibold text-ink-900">10–40 items</strong> for clean shelf labels.</span>
                        </p>
                        <p class="flex gap-2.5">
                            <x-icon name="check" class="mt-0.5 size-4 shrink-0 text-brand-600" />
                            <span>Deleting a parent keeps its goods — they simply become unclassified.</span>
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts.app>
