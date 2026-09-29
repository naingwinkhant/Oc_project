<x-layouts.public title="Goods catalogue" description="Browse every item by classification">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <section class="mb-6 overflow-hidden rounded-card bg-brand-700 p-5 text-white shadow-pop sm:p-8">
            <div @class([
                'grid items-center gap-3 sm:gap-4',
                'lg:grid-cols-[minmax(0,21rem)_minmax(0,1fr)]' => count($promo ?? []),
            ])>
                @if (count($promo ?? []))
                    <x-promo-carousel :slides="$promo" />
                @endif

                <div class="min-w-0">
                    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
                        {{ config('shop.hero.headline') }}
                    </h1>
                    <p class="mt-1.5 max-w-xl text-base text-brand-100 sm:text-base">
                        {{ config('shop.hero.subline') }}
                    </p>
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <a href="{{ route('catalog.index', ['on_sale' => 1]) }}"
                           class="btn border-0 bg-white text-brand-800 hover:bg-white/90">
                            <x-icon name="tag" class="size-4" /> {{ config('shop.hero.cta') }}
                        </a>
                        <a href="{{ route('login') }}" class="btn border-0 bg-white/15 text-white backdrop-blur hover:bg-white/25">
                            <x-icon name="dashboard" class="size-4" /> <span class="hidden sm:inline">Staff sign in</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <details class="card mb-4 lg:hidden" id="mobile-filters">
            <summary class="flex cursor-pointer list-none items-center gap-2 px-4 py-3 text-sm font-semibold text-ink-900">
                <x-icon name="sliders" class="size-4 text-ink-500" />
                Classifications &amp; filters
                <x-icon name="chevron-down" class="ms-auto size-4 text-ink-400" />
            </summary>
            <div class="space-y-3 border-t border-ink-200 p-4">
                <div class="flex flex-wrap gap-1.5">
                    <a href="{{ route('catalog.index') }}"
                       @class([
                           'rounded-full px-3 py-1.5 text-xs font-semibold transition',
                           'bg-brand-600 text-white' => ! $selectedCategory,
                           'bg-ink-100 text-ink-700 hover:bg-ink-200' => $selectedCategory,
                       ])>All goods</a>

                    @foreach ($categories as $category)
                        <a href="{{ route('catalog.show', $category) }}"
                           @class([
                               'rounded-full px-3 py-1.5 text-xs font-semibold transition',
                               'bg-brand-600 text-white' => $selectedCategory?->id === $category->id,
                               'bg-ink-100 text-ink-700 hover:bg-ink-200' => $selectedCategory?->id !== $category->id,
                           ])>{{ $category->name }}</a>
                    @endforeach
                </div>

                <form method="GET" class="flex flex-wrap items-center gap-4 border-t border-ink-100 pt-3">
                    @if (request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-ink-700">
                        <input type="checkbox" name="in_stock" value="1" @checked(request()->boolean('in_stock')) class="checkbox">
                        In stock only
                    </label>
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-ink-700">
                        <input type="checkbox" name="on_sale" value="1" @checked(request()->boolean('on_sale')) class="checkbox">
                        On promotion
                    </label>
                    <button type="submit" class="btn btn-primary btn-sm ms-auto">Apply</button>
                </form>
            </div>
        </details>

        <div class="grid gap-6 lg:grid-cols-4">

            <aside class="hidden lg:col-span-1 lg:block">
                <div class="lg:sticky lg:top-36">
                    <section class="card mb-4">
                        <div class="card-header">
                            <h2 class="card-title">Classifications</h2>
                        </div>
                        <div class="max-h-[22rem] overflow-y-auto p-2">
                            <a href="{{ route('catalog.index') }}"
                               @class(['nav-link !py-2', 'nav-link-active' => ! $selectedCategory])>
                                <x-icon name="grid" class="size-4 shrink-0" />
                                <span class="flex-1">All goods</span>
                            </a>

                            @foreach ($categories as $category)
                                <div>
                                    <a href="{{ route('catalog.show', $category) }}"
                                       @class(['nav-link !py-2', 'nav-link-active' => $selectedCategory?->id === $category->id])>
                                        <x-icon name="tag" class="size-4 shrink-0" />
                                        <span class="flex-1 truncate">{{ $category->name }}</span>
                                        <span class="shrink-0 text-xs text-ink-400 tabular-nums">{{ $category->branch_products_count }}</span>
                                    </a>

                                    @if ($category->children->isNotEmpty())
                                        <ul class="ms-6 mt-0.5 space-y-0.5 border-s border-ink-200 ps-2">
                                            @foreach ($category->children as $child)
                                                <li>
                                                    <a href="{{ route('catalog.show', $child) }}"
                                                       @class([
                                                           'block truncate rounded-md px-2.5 py-1.5 text-[0.8125rem] transition-colors',
                                                           'bg-brand-50 font-semibold text-brand-700' => $selectedCategory?->id === $child->id,
                                                           'text-ink-600 hover:bg-ink-100 hover:text-ink-900' => $selectedCategory?->id !== $child->id,
                                                       ])>{{ $child->name }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="card">
                        <div class="card-header">
                            <h2 class="card-title">Refine</h2>
                        </div>
                        <form method="GET" class="card-body space-y-3">
                            @if (request('q'))
                                <input type="hidden" name="q" value="{{ request('q') }}">
                            @endif

                            <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                                <input type="checkbox" name="in_stock" value="1" @checked(request()->boolean('in_stock')) class="checkbox">
                                In stock only
                            </label>

                            <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                                <input type="checkbox" name="on_sale" value="1" @checked(request()->boolean('on_sale')) class="checkbox">
                                On promotion
                            </label>

                            <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                                <input type="checkbox" name="coming_soon" value="1" @checked(request()->boolean('coming_soon')) class="checkbox">
                                Coming soon
                            </label>

                            <button type="submit" class="btn btn-primary btn-sm w-full">
                                <x-icon name="filter" class="size-4" /> Apply
                            </button>
                            <a href="{{ route('catalog.index') }}" class="btn btn-ghost btn-sm w-full">Clear all</a>
                        </form>
                    </section>
                </div>
            </aside>

            <div class="lg:col-span-3">
                <div class="mb-4 flex flex-wrap items-center gap-3">
                    <p class="text-sm text-ink-600">
                        <span class="font-semibold text-ink-900">{{ number_format($products->total()) }}</span>
                        {{ Str::plural('item', $products->total()) }}
                        @if (request('q'))
                            matching <span class="font-semibold text-ink-900">“{{ request('q') }}”</span>
                        @endif
                    </p>

                    <form method="GET" class="ms-auto">
                        @foreach (request()->except(['sort', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ is_array($value) ? '' : $value }}">
                        @endforeach
                        <select name="sort" data-autosubmit class="select select-sm" aria-label="Sort goods">
                            @foreach ([
                                'name' => 'Name A → Z',
                                'price_asc' => 'Price: low to high',
                                'price_desc' => 'Price: high to low',
                                'newest' => 'Newest arrivals',
                                'popular' => 'Most viewed',
                            ] as $value => $label)
                                <option value="{{ $value }}" @selected(request('sort', 'name') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @if ($products->isEmpty())
                    <x-empty-state icon="search" title="No goods matched"
                                  description="Try a different keyword, or browse a classification from the menu.">
                        <x-slot:action>
                            <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-sm">Reset search</a>
                        </x-slot:action>
                    </x-empty-state>
                @else
                    <div class="grid gap-3 grid-cards sm:gap-4">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $products->links('pagination::tailwind-simple') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.public>
