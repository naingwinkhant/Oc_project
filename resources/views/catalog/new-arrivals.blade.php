<x-layouts.public title="New arrivals" description="The latest additions to the shelves">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <section class="mb-6 overflow-hidden rounded-card bg-sky-700 p-5 text-white shadow-pop sm:p-7">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold tracking-wider text-sky-200 uppercase">Just landed</p>
                    <h1 class="mt-1 text-xl font-bold tracking-tight sm:text-2xl">New arrivals</h1>
                    <p class="mt-1 max-w-xl text-sm text-sky-100">
                        {{ number_format($products->total()) }} {{ Str::plural('item', $products->total()) }} added to the
                        shelves in the last few weeks.
                    </p>
                </div>
                <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-white/15 backdrop-blur">
                    <x-icon name="sparkles" class="size-6" />
                </span>
            </div>
        </section>

        <div class="mb-4 flex flex-wrap items-center gap-3">
            <p class="text-sm text-ink-600">
                Sorted by <span class="font-semibold text-ink-900">newest first</span>
            </p>

            <form method="GET" class="ms-auto">
                <select name="sort" data-autosubmit class="select select-sm" aria-label="Sort new arrivals">
                    @foreach ([
                        'newest' => 'Newest first',
                        'name' => 'Name A → Z',
                        'price_asc' => 'Price: low to high',
                        'price_desc' => 'Price: high to low',
                        'popular' => 'Most viewed',
                    ] as $value => $label)
                        <option value="{{ $value }}" @selected(request('sort', 'newest') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>

            <form method="GET">
                <label class="flex cursor-pointer items-center gap-2 text-sm text-ink-600">
                    <input type="checkbox" name="in_stock" value="1" @checked(request()->boolean('in_stock')) data-autosubmit class="checkbox">
                    In stock
                </label>
            </form>
        </div>

        @if ($products->isEmpty())
            <x-empty-state icon="sparkles" title="No new arrivals yet"
                          description="Fresh stock lands every few days — check back soon." />
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
</x-layouts.public>
