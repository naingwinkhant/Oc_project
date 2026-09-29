@props(['product', 'showCategory' => true, 'favourited' => null])

@php
    $href = route('catalog.product', $product);
    $isFavourite = $favourited ?? app(\App\Cart\FavouriteService::class)->has($product->id);
@endphp

<article class="group card flex flex-col overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-pop">
    <a href="{{ $href }}" class="relative block aspect-4/3 overflow-hidden bg-ink-50">
        @if ($product->imageUrl())
            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" loading="lazy"
                 class="size-full object-cover transition-transform duration-500 group-hover:scale-105">
        @else
            <span class="grid size-full place-items-center text-ink-300">
                <x-icon name="box" class="size-10" />
            </span>
        @endif

        <div class="absolute end-2 top-2 z-10">
            <x-favourite-button :product="$product" :active="$isFavourite" />
        </div>

        <div class="absolute start-2 top-2 flex flex-wrap gap-1.5">
            @if ($product->isNewArrival())
                <span class="badge bg-sky-600 text-white ring-sky-700/30">New</span>
            @endif
            @if ($product->isComingSoon())
                <span class="badge bg-violet-600 text-white ring-violet-700/30">Coming soon</span>
            @endif
            @if ($product->isExpired())
                <span class="badge bg-rose-600 text-white ring-rose-700/30">Expired</span>
            @endif
            @if ($product->is_featured)
                <span class="badge bg-amber-400 text-amber-950 ring-amber-500/30">Featured</span>
            @endif
            @unless ($product->is_active)
                <span class="badge bg-ink-900/85 text-white ring-ink-900/20">Hidden</span>
            @endunless
        </div>
    </a>

    <div class="flex flex-1 flex-col p-3.5">
        @if ($showCategory && $product->category)
            <a href="{{ route('catalog.show', $product->category) }}" class="mb-1.5 w-fit">
                <span class="badge bg-ink-100 text-ink-600 ring-ink-500/10">{{ $product->category->name }}</span>
            </a>
        @endif

        <h3 class="line-clamp-2 text-sm font-semibold leading-snug text-ink-900">
            <a href="{{ $href }}" class="transition-colors hover:text-brand-700">{{ $product->name }}</a>
        </h3>

        <p class="mt-1 truncate font-mono text-[0.6875rem] text-ink-400">{{ $product->sku }}</p>

        <div class="mt-auto flex items-end justify-between gap-2 pt-3">
            <x-price :product="$product" size="sm" class="min-w-0" />

            @if ($product->isSellable())
                <form method="POST" action="{{ route('cart.store') }}" class="shrink-0">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-soft btn-sm" title="Add {{ $product->name }} to cart">
                        <x-icon name="cart" class="size-3.5" />
                        <span class="sr-only">Add to cart</span>
                    </button>
                </form>
            @endif
        </div>

        <div class="mt-2 flex flex-wrap items-center gap-1.5">
            @if ($product->isComingSoon())
                <span class="badge bg-violet-50 text-violet-700 ring-violet-600/20">
                    On the shelf {{ $product->available_from->format('j M') }}
                </span>
            @else
                <span class="badge {{ $product->stockStatusTone() }}">{{ $product->stockStatusLabel() }}</span>
                @if ($product->expires_at && $product->expiryStatus() !== 'fresh')
                    <span class="badge {{ $product->expiryStatusTone() }}">{{ $product->expiryStatusLabel() }}</span>
                @endif
            @endif
        </div>

        <x-freshness :product="$product" variant="compact" class="mt-1.5" />
    </div>
</article>
