<x-layouts.public :title="$category->name" :description="$category->description ?? 'Browse '.$category->name">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <header class="card mb-6 overflow-hidden">
            <div class="flex flex-wrap items-center gap-4 p-5 sm:p-6">
                @if ($category->imageUrl())
                    <img src="{{ $category->imageUrl() }}" alt="" class="size-16 shrink-0 rounded-xl object-cover sm:size-20">
                @else
                    <span class="grid size-16 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-700 sm:size-20">
                        <x-icon name="tag" class="size-8 sm:size-9" />
                    </span>
                @endif

                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-bold tracking-tight text-ink-900">{{ $category->name }}</h1>
                    @if ($category->description)
                        <p class="mt-1 text-sm text-ink-500">{{ $category->description }}</p>
                    @endif
                    <p class="mt-1.5 text-xs text-ink-400">
                        {{ number_format($products->total()) }} {{ Str::plural('item', $products->total()) }} available
                    </p>
                </div>
            </div>

            @if ($children->isNotEmpty())
                <div class="flex gap-2 overflow-x-auto border-t border-ink-200 bg-ink-50/60 px-4 py-3 sm:px-6">
                    @foreach ($children as $child)
                        <a href="{{ route('catalog.show', $child) }}"
                           class="btn btn-secondary btn-sm shrink-0 whitespace-nowrap">
                            <x-icon name="folder" class="size-3.5" />
                            {{ $child->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </header>

        <div class="mb-4 flex flex-wrap items-center gap-3">
            <x-filter-drawer id="category-filters" label="Filters"
                             :active="(int) request()->boolean('in_stock')
                                 + (int) request()->boolean('on_sale')
                                 + (int) request()->boolean('coming_soon')
                                 + (int) request()->filled('q')">
                <form method="GET" class="space-y-3">
                    @if (request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif

                    <p class="section-title">Refine</p>

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

                    <div class="flex gap-2 pt-1">
                        <button type="submit" class="btn btn-primary flex-1">Apply filters</button>
                        <a href="{{ route('catalog.show', $category) }}" class="btn btn-ghost">Clear</a>
                    </div>
                </form>

                @if ($children->isNotEmpty())
                    <p class="section-title mt-5 mb-2">Aisles</p>

                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($children as $child)
                            <a href="{{ route('catalog.show', $child) }}"
                               @class([
                                   'inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold transition',
                                   'bg-brand-600 text-white' => $child->id === $category->id,
                                   'bg-ink-100 text-ink-700 hover:bg-ink-200' => $child->id !== $category->id,
                               ])>
                                <x-icon :name="$child->iconName()" class="size-3.5 shrink-0" />
                                {{ $child->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-filter-drawer>

            <p class="text-sm text-ink-600">
                <span class="font-semibold text-ink-900">{{ number_format($products->total()) }}</span>
                {{ Str::plural('item', $products->total()) }}
            </p>

            <form method="GET" class="ms-auto flex items-center gap-2">
                @if (request()->boolean('in_stock'))
                    <input type="hidden" name="in_stock" value="1">
                @endif
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
            <x-empty-state icon="box" title="Nothing here yet"
                          description="This classification has no visible goods right now. Check back soon." />
        @else
            <div class="grid gap-3 grid-cards sm:gap-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" :show-category="false" />
                @endforeach
            </div>

            <div class="mt-6">
                {{ $products->links('pagination::tailwind-simple') }}
            </div>
        @endif
    </div>
</x-layouts.public>
