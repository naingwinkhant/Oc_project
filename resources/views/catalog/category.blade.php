<x-layouts.public :title="$category->name" :description="$category->description ?? 'Browse '.$category->name">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <header class="card mb-6 overflow-hidden">
            <div class="flex flex-wrap items-center gap-4 p-5 sm:p-6">
                @if ($category->imageUrl())
                    <img src="{{ $category->imageUrl() }}" alt="" class="size-16 shrink-0 rounded-xl object-cover sm:size-20">
                @else
                    <span class="grid size-16 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-600 sm:size-20">
                        <x-icon name="tag" class="size-8" />
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

            <form method="GET" class="flex items-center gap-2">
                <label class="flex cursor-pointer items-center gap-2 text-sm text-ink-600">
                    <input type="checkbox" name="in_stock" value="1" @checked(request()->boolean('in_stock')) data-autosubmit class="checkbox">
                    In stock
                </label>
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
