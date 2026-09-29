@php
    $money = fn ($value) => '₱' . number_format((float) $value, 2);
    $compact = fn ($value) => '₱' . number_format((float) $value, 0);
    $chartFloor = 6;
@endphp

@section('breadcrumb')
    <x-breadcrumbs :items="[['label' => 'Dashboard']]" />
@endsection

<x-layouts.app title="Dashboard" heading="Dashboard" description="How the store is doing right now">
    <x-slot:actions>
        <div data-live-region data-live-interval="60" class="hidden items-center gap-2 sm:flex">
            <span class="relative flex size-2">
                <span class="absolute inline-flex size-full animate-ping rounded-full bg-brand-400 opacity-75"></span>
                <span class="relative inline-flex size-2 rounded-full bg-brand-500"></span>
            </span>
            <span data-live-stamp class="text-xs text-ink-500 tabular-nums">just now</span>
        </div>

        <button type="button" data-autorefresh class="btn btn-secondary btn-sm" title="Reload every 60 seconds">
            <x-icon name="refresh" class="size-4" />
            <span class="hidden sm:inline" data-autorefresh-label>Auto-refresh off</span>
        </button>

        @if (auth()->user()->canManageCatalog())
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                <x-icon name="plus" class="size-4" />
                <span class="hidden sm:inline">Add goods</span>
            </a>
        @endif
    </x-slot:actions>

    <div class="space-y-4">

        {{-- ── 1. One clear answer: shelf health ───────────────────────────── --}}
        <section class="card overflow-hidden">
            <div class="grid gap-5 p-5 sm:p-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-center">
                <div>
                    <div class="flex items-start gap-3">
                        <span @class([
                            'mt-0.5 grid size-11 shrink-0 place-items-center rounded-xl ring-1 ring-inset',
                            'bg-emerald-50 text-emerald-600 ring-emerald-600/20' => $health['score'] >= 90,
                            'bg-amber-50 text-amber-600 ring-amber-600/20' => $health['score'] >= 70 && $health['score'] < 90,
                            'bg-rose-50 text-rose-600 ring-rose-600/20' => $health['score'] < 70,
                        ])>
                            <x-icon :name="$health['score'] >= 90 ? 'check' : 'alert'" class="size-6" />
                        </span>

                        <div class="min-w-0">
                            <p class="section-title">Shelf health</p>
                            <h2 class="mt-0.5 text-lg font-bold tracking-tight text-ink-900 sm:text-xl">
                                {{ $health['headline'] }}
                            </h2>
                            <p class="mt-1 text-sm text-ink-500">
                                <strong class="font-semibold text-ink-800 tabular-nums">{{ number_format($health['score']) }}%</strong>
                                of {{ number_format($health['total']) }} goods items are above their reorder threshold.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 flex h-3 w-full overflow-hidden rounded-full bg-ink-100">
                        @foreach ($health['segments'] as $segment)
                            @if ($segment['percent'] > 0)
                                <a href="{{ $segment['key'] === 'ok' ? route('admin.products.index', ['status' => 'active']) : route('admin.stock.low') }}"
                                   title="{{ $segment['label'] }}: {{ $segment['count'] }} ({{ $segment['percent'] }}%)"
                                   @class([
                                       'bar-animate h-full transition-colors',
                                       'bg-emerald-500 hover:bg-emerald-600' => $segment['key'] === 'ok',
                                       'bg-amber-400 hover:bg-amber-500' => $segment['key'] === 'low',
                                       'bg-rose-500 hover:bg-rose-600' => $segment['key'] === 'out',
                                   ])
                                   style="--w: {{ max(1.5, $segment['percent']) }}%"></a>
                            @endif
                        @endforeach
                    </div>

                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2">
                        @foreach ($health['segments'] as $segment)
                            <a href="{{ $segment['key'] === 'ok' ? route('admin.products.index', ['status' => 'active']) : route('admin.stock.low') }}"
                               class="group flex items-center gap-2 text-xs">
                                <span @class([
                                    'size-2.5 rounded-full',
                                    'bg-emerald-500' => $segment['key'] === 'ok',
                                    'bg-amber-400' => $segment['key'] === 'low',
                                    'bg-rose-500' => $segment['key'] === 'out',
                                ])></span>
                                <span class="text-ink-500 group-hover:text-ink-800">{{ $segment['label'] }}</span>
                                <span class="font-semibold text-ink-900 tabular-nums">{{ number_format($segment['count']) }}</span>
                                <span class="text-ink-400 tabular-nums">({{ $segment['percent'] }}%)</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('admin.stock.low') }}"
                   class="flex items-center justify-between gap-4 rounded-xl border border-ink-200 bg-ink-50/70 p-4 transition hover:border-rose-300 hover:bg-rose-50/50">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold tracking-wide text-ink-500 uppercase">Action needed</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight text-ink-900 tabular-nums">
                            {{ number_format($health['needsAction']) }}
                            <span class="text-sm font-medium text-ink-500">items</span>
                        </p>
                    </div>
                    <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-rose-600 text-white shadow-raise">
                        <x-icon name="arrow-right" class="size-5" />
                    </span>
                </a>
            </div>
        </section>

        {{-- ── 2. Key numbers, one meaning each ────────────────────────────── --}}
        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($kpis as $kpi)
                <a href="{{ $kpi['link'] }}" class="card group p-4 transition hover:-translate-y-0.5 hover:shadow-raise sm:p-5">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-xs font-semibold tracking-wide text-ink-500 uppercase">{{ $kpi['label'] }}</p>
                        <span @class([
                            'grid size-9 shrink-0 place-items-center rounded-lg ring-1 ring-inset transition group-hover:scale-105',
                            'bg-brand-50 text-brand-600 ring-brand-600/15' => $kpi['tone'] === 'brand',
                            'bg-emerald-50 text-emerald-600 ring-emerald-600/15' => $kpi['tone'] === 'emerald',
                            'bg-sky-50 text-sky-600 ring-sky-600/15' => $kpi['tone'] === 'sky',
                            'bg-violet-50 text-violet-600 ring-violet-600/15' => $kpi['tone'] === 'violet',
                        ])>
                            <x-icon :name="$kpi['icon']" class="size-[1.125rem]" />
                        </span>
                    </div>

                    <p class="mt-2 flex items-baseline gap-1.5">
                        <span @if (! empty($kpi['money']))
                            data-count-to="{{ (float) $kpi['value'] }}" data-count-money
                        @else
                            data-count-to="{{ $kpi['value'] }}"
                        @endif
                              class="text-[1.75rem] leading-none font-bold tracking-tight text-ink-900 tabular-nums">0</span>
                        @if (! empty($kpi['unit']))
                            <span class="text-sm font-medium text-ink-400">{{ $kpi['unit'] }}</span>
                        @endif
                    </p>

                    <p class="mt-2 truncate text-xs text-ink-500">{{ $kpi['hint'] }}</p>
                </a>
            @endforeach
        </section>

        <div class="grid items-start gap-4 xl:grid-cols-3">

            {{-- ── 3. Restock queue, sorted by urgency ───────────────────── --}}
            <section class="card overflow-hidden xl:col-span-2">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Restock queue</h2>
                        <p class="text-xs text-ink-400">Most urgent first · {{ count($restock) }} shown</p>
                    </div>
                    <a href="{{ route('admin.stock.low') }}" class="btn btn-secondary btn-sm">
                        Open list
                        <x-icon name="arrow-right" class="size-4" />
                    </a>
                </div>

                @if ($restock->isEmpty())
                    <x-empty-state icon="check" title="Nothing to reorder"
                                  description="Every goods item is above its reorder threshold." />
                @else
                    <ul class="divide-y divide-ink-100">
                        @foreach ($restock as $row)
                            @php $product = $row['product']; @endphp
                            <li class="flex items-center gap-3 px-4 py-3 transition-colors hover:bg-brand-50/40 sm:gap-4 sm:px-5">
                                @if ($product->imageUrl())
                                    <img src="{{ $product->imageUrl() }}" alt="" class="size-10 shrink-0 rounded-lg object-cover ring-1 ring-ink-200">
                                @else
                                    <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-ink-100 text-ink-400">
                                        <x-icon name="box" class="size-5" />
                                    </span>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-ink-900">{{ $product->name }}</p>
                                    <p class="truncate text-[0.6875rem] text-ink-400">
                                        {{ $product->sku }}
                                        @if ($product->category) · {{ $product->category->name }} @endif
                                    </p>
                                </div>

                                <div class="hidden w-44 shrink-0 sm:block">
                                    <div class="mb-1 flex items-baseline justify-between gap-2 text-[0.6875rem]">
                                        <span @class([
                                            'font-semibold',
                                            'text-rose-600' => $row['tone'] === 'rose',
                                            'text-amber-600' => $row['tone'] === 'amber',
                                        ])>{{ $product->stock }} left</span>
                                        <span class="text-ink-400 tabular-nums">min {{ $product->min_stock }}</span>
                                    </div>
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-ink-100">
                                        <div @class([
                                            'bar-animate h-full rounded-full',
                                            'bg-rose-500' => $row['tone'] === 'rose',
                                            'bg-amber-400' => $row['tone'] === 'amber',
                                        ]) style="--w: {{ $row['ratio'] }}%"></div>
                                    </div>
                                </div>

                                <div class="hidden shrink-0 text-right lg:block">
                                    <p class="text-[0.6875rem] text-ink-400">Order</p>
                                    <p class="text-sm font-semibold text-ink-800 tabular-nums">+{{ $row['suggested'] }} {{ $product->unit }}</p>
                                </div>

                                <a href="{{ route('admin.products.edit', $product) }}"
                                   @class([
                                       'btn btn-sm shrink-0',
                                       'btn-danger' => $row['tone'] === 'rose',
                                       'btn-soft' => $row['tone'] === 'amber',
                                   ])>
                                    <x-icon name="arrow-down" class="size-3.5" />
                                    <span class="hidden md:inline">Restock</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- ── 4. 30-day movement chart ───────────────────────────────── --}}
            <section class="card flex flex-col overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Goods movement</h2>
                        <p class="text-xs text-ink-400">Last 30 days</p>
                    </div>
                    <a href="{{ route('admin.stock.index') }}" class="btn btn-ghost btn-sm" title="Full log">
                        <x-icon name="arrow-right" class="size-4" />
                    </a>
                </div>

                <div class="flex items-center gap-4 px-4 pt-1 sm:px-5">
                    <div class="flex items-center gap-1.5">
                        <span class="size-2.5 rounded-sm bg-emerald-500"></span>
                        <span class="text-xs text-ink-500">In</span>
                        <span class="text-sm font-bold text-ink-900 tabular-nums">{{ number_format($movement['in']) }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="size-2.5 rounded-sm bg-rose-400"></span>
                        <span class="text-xs text-ink-500">Out</span>
                        <span class="text-sm font-bold text-ink-900 tabular-nums">{{ number_format($movement['out']) }}</span>
                    </div>
                    <span @class([
                        'badge ms-auto',
                        'bg-emerald-50 text-emerald-700 ring-emerald-600/20' => $movement['net'] >= 0,
                        'bg-rose-50 text-rose-700 ring-rose-600/20' => $movement['net'] < 0,
                    ])>
                        {{ $movement['net'] >= 0 ? '+' : '' }}{{ number_format($movement['net']) }} net
                    </span>
                </div>

                <div class="mt-4 flex-1 px-4 pb-4 sm:px-5">
                    <div class="flex h-40 items-end gap-[3px]" role="img"
                         aria-label="Daily goods in and out for the last {{ count($movement['days']) }} days">
                        @foreach ($movement['days'] as $day)
                            @php
                                $inH = round($day['in'] / $movement['peak'] * 100, 1);
                                $outH = round($day['out'] / $movement['peak'] * 100, 1);
                                $empty = $day['total'] === 0;
                            @endphp
                            <div class="group relative flex h-full flex-1 flex-col justify-end gap-[2px]"
                                 title="{{ $day['label'] }} · in {{ $day['in'] }} · out {{ $day['out'] }}">
                                @unless ($empty)
                                    @if ($day['out'] > 0)
                                        <div class="bar-animate w-full rounded-t-sm bg-rose-400 transition-colors group-hover:bg-rose-500"
                                             style="--w: {{ max(3, $outH) }}%; height: {{ $outH }}%"></div>
                                    @endif
                                    @if ($day['in'] > 0)
                                        <div @class([
                                            'bar-animate w-full bg-emerald-500 transition-colors group-hover:bg-emerald-600',
                                            'rounded-t-sm' => $day['out'] > 0,
                                            'rounded-sm' => $day['out'] === 0,
                                        ]) style="--w: 100%; height: {{ $inH }}%"></div>
                                    @endif
                                @else
                                    <div class="h-[3px] w-full rounded-sm bg-ink-200/70"></div>
                                @endunless
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-2 flex justify-between text-[0.625rem] text-ink-400">
                        <span>{{ $movement['days'][0]['label'] }}</span>
                        <span>{{ $movement['days'][count($movement['days']) - 1]['label'] }}</span>
                    </div>
                </div>

                <div class="border-t border-ink-200 px-4 py-3 sm:px-5">
                    <p class="text-xs text-ink-500">
                        Recorded on <strong class="font-semibold text-ink-800 tabular-nums">{{ $movement['activeDays'] }}</strong>
                        of the last {{ count($movement['days']) }} days.
                    </p>
                </div>
            </section>
        </div>

        <div class="grid items-start gap-4 xl:grid-cols-3">
            <div class="space-y-4 xl:col-span-2">

                {{-- ── 5. Where the value sits ───────────────────────────────── --}}
                <section class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Where your money sits</h2>
                            <p class="text-xs text-ink-400">Stock value by department, aisles included</p>
                        </div>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-ghost btn-sm">
                            Classifications
                            <x-icon name="arrow-right" class="size-4" />
                        </a>
                    </div>

                    @if ($departments->isEmpty())
                        <x-empty-state icon="layers" title="No classifications yet"
                                      description="Create a department to start organising your goods." />
                    @else
                        <div class="card-body space-y-3.5">
                            @foreach ($departments as $dept)
                                <div>
                                    <div class="mb-1.5 flex items-baseline justify-between gap-3">
                                        <div class="flex min-w-0 items-center gap-2">
                                            <p @class([
                                                'truncate text-sm font-semibold',
                                                'text-ink-500' => $dept['isOther'],
                                                'text-ink-800' => ! $dept['isOther'],
                                            ])>{{ $dept['name'] }}</p>
                                            <span class="shrink-0 text-[0.6875rem] text-ink-400 tabular-nums">{{ $dept['items'] }} items</span>
                                        </div>
                                        <p class="shrink-0 text-xs font-semibold text-ink-700 tabular-nums">
                                            {{ $compact($dept['value']) }}
                                            <span class="font-normal text-ink-400">{{ $dept['share'] }}%</span>
                                        </p>
                                    </div>
                                    <div class="h-2 w-full overflow-hidden rounded-full bg-ink-100">
                                        <div @class([
                                            'bar-animate h-full rounded-full transition-colors',
                                            'bg-ink-300' => $dept['isOther'],
                                            'bg-brand-500' => ! $dept['isOther'],
                                        ]) style="--w: {{ max(1.5, $dept['share'] * 3) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                {{-- ── 6. Fast movers ─────────────────────────────────────── --}}
                <section class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Fastest movers</h2>
                            <p class="text-xs text-ink-400">Most goods handled in the last 30 days</p>
                        </div>
                        @if ($movers['total'] > 0)
                            <span class="text-xs text-ink-400 tabular-nums">
                                {{ number_format($movers['total']) }} units total
                            </span>
                        @endif
                    </div>

                    @if ($movers['items']->isEmpty())
                        <x-empty-state icon="clipboard" title="No movements yet"
                                      description="Record goods in or out to see what moves fastest." />
                    @else
                        <ol class="divide-y divide-ink-100">
                            @foreach ($movers['items'] as $index => $row)
                                @php
                                    $product = $row['product'];
                                    $share = $movers['total'] > 0 ? round($row['total'] / $movers['total'] * 100, 1) : 0;
                                @endphp
                                <li class="flex items-center gap-3 px-4 py-2.5 sm:px-5">
                                    <span class="grid size-6 shrink-0 place-items-center rounded-md bg-ink-100 text-[0.6875rem] font-bold text-ink-500 tabular-nums">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                           class="block truncate text-sm font-medium text-ink-800 hover:text-brand-700">
                                            {{ $product->name }}
                                        </a>
                                        <div class="mt-1 flex items-center gap-2">
                                            <div class="h-1 min-w-0 flex-1 overflow-hidden rounded-full bg-ink-100">
                                                <div class="bar-animate h-full rounded-full bg-brand-400"
                                                     style="--w: {{ max(2, $share * 3) }}%"></div>
                                            </div>
                                            <span class="shrink-0 text-[0.6875rem] text-ink-400 tabular-nums">{{ $share }}%</span>
                                        </div>
                                    </div>
                                    <span class="w-16 shrink-0 text-right text-sm font-bold text-ink-900 tabular-nums">
                                        {{ number_format($row['total']) }}
                                        <span class="block text-[0.625rem] font-normal text-ink-400">{{ $product->unit }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </section>
            </div>

            <div class="space-y-4">
                {{-- ── 7. What changed ───────────────────────────────────── --}}
                <section class="card overflow-hidden">
                    <div class="card-header">
                        <h2 class="card-title">What changed</h2>
                        <a href="{{ route('admin.activity.index') }}" class="btn btn-ghost btn-sm">All</a>
                    </div>

                    @if ($activity->isEmpty())
                        <x-empty-state icon="clock" title="No changes yet"
                                      description="Edits, deletions and stock movements will show up here." />
                    @else
                        <ul class="divide-y divide-ink-100">
                            @foreach ($activity as $log)
                                <li class="flex items-start gap-2.5 px-4 py-2.5 sm:px-5">
                                    <span class="badge {{ $log->tone() }} mt-0.5 shrink-0">{{ $log->action }}</span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs leading-snug text-ink-700">{{ $log->description }}</p>
                                        <p class="mt-0.5 text-[0.6875rem] text-ink-400">
                                            {{ $log->user?->name ?? 'System' }} · {{ $log->created_at->diffForHumans(short: true) }}
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                {{-- ── 8. Team ────────────────────────────────────────────── --}}
                <section class="card overflow-hidden">
                    <div class="card-header">
                        <h2 class="card-title">On shift</h2>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Team</a>
                    </div>
                    <ul class="divide-y divide-ink-100">
                        @foreach ($team as $member)
                            <li class="flex items-center gap-3 px-4 py-2.5 sm:px-5">
                                <span class="grid size-8 shrink-0 place-items-center rounded-full bg-brand-100 text-[0.6875rem] font-bold text-brand-700">
                                    {{ $member->initials() }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-ink-800">{{ $member->name }}</p>
                                    <p class="truncate text-[0.6875rem] text-ink-400">{{ $member->role->label() }}</p>
                                </div>
                                <span class="shrink-0 text-[0.6875rem] text-ink-400">
                                    {{ $member->last_login_at ? $member->last_login_at->diffForHumans(short: true) : 'never' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            </div>
        </div>
    </div>
</x-layouts.app>
