<x-layouts.public :title="$product->name" :description="$product->brand ? $product->brand.' · '.$product->name : $product->name">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="card overflow-hidden">
                    <div class="grid max-h-[26rem] place-items-center bg-ink-50">
                        @if ($product->imageUrl())
                            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="size-full object-contain">
                        @else
                            <span class="grid size-full place-items-center text-ink-300">
                                <x-icon name="box" class="size-16" />
                            </span>
                        @endif
                    </div>
                </div>

                @if ($product->description)
                    <section class="card mt-4">
                        <div class="card-header">
                            <h2 class="card-title">Product details</h2>
                        </div>
                        <div class="card-body">
                            <p class="text-sm leading-relaxed whitespace-pre-line text-ink-600">{{ $product->description }}</p>
                        </div>
                    </section>
                @endif

                @if ($credit = $product->imageCredit())
                    <p class="mt-3 flex flex-wrap items-center gap-x-1.5 gap-y-0.5 text-[0.6875rem] text-ink-400">
                        <x-icon name="image" class="size-3" />
                        <span>Photo:</span>
                        <a href="{{ $credit['source'] }}" target="_blank" rel="noopener nofollow"
                           class="hover:text-brand-700 hover:underline">
                            {{ \Illuminate\Support\Str::limit($credit['author'] ?: 'Wikimedia Commons', 40) }}
                        </a>
                        @if ($credit['license'])
                            <span>· {{ $credit['license'] }}</span>
                        @endif
                        <span>· via Wikimedia Commons</span>
                    </p>
                @endif

                @if (! empty($product->attributes))
                    <section class="card mt-4">
                        <div class="card-header">
                            <h2 class="card-title">Specifications</h2>
                        </div>
                        <dl class="grid grid-cols-2 gap-px bg-ink-200 sm:grid-cols-3">
                            @foreach ($product->attributes as $key => $value)
                                <div class="bg-surface px-4 py-3">
                                    <dt class="text-[0.6875rem] font-semibold tracking-wider text-ink-400 uppercase">
                                        {{ str_replace('_', ' ', $key) }}
                                    </dt>
                                    <dd class="mt-1 text-sm font-medium text-ink-800">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>
                @endif
            </div>

            <aside class="space-y-4">
                <section class="card">
                    <div class="card-body">
                        @if ($product->category)
                            <a href="{{ route('catalog.show', $product->category) }}" class="mb-2 w-fit">
                                <span class="badge bg-brand-50 text-brand-700 ring-brand-600/15">{{ $product->category->name }}</span>
                            </a>
                        @endif

                        <h1 class="text-xl font-bold tracking-tight text-ink-900">{{ $product->name }}</h1>

                        @if ($product->brand)
                            <p class="mt-1 text-sm text-ink-500">by <span class="font-medium text-ink-700">{{ $product->brand }}</span></p>
                        @endif

                        <x-price :product="$product" size="lg" class="mt-3" />

                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            @if ($product->isComingSoon())
                                <span class="badge bg-violet-600 text-white ring-violet-700/30">Coming soon</span>
                            @endif
                            <span class="badge {{ $product->stockStatusTone() }}">{{ $product->stockStatusLabel() }}</span>
                            <span class="badge bg-ink-100 text-ink-600 ring-ink-500/10">
                                {{ $product->stock }} {{ $product->unit }}{{ $product->weight ? ' · '.$product->weight.' kg' : '' }}
                            </span>
                            @if ($product->expires_at)
                                <span class="badge {{ $product->expiryStatusTone() }}">{{ $product->expiryStatusLabel() }}</span>
                            @endif
                            @if ($product->is_featured)
                                <span class="badge bg-amber-50 text-amber-700 ring-amber-600/20">On promotion</span>
                            @endif
                        </div>

                        @if ($product->isExpired())
                            <p class="mt-4 rounded-lg bg-rose-50 p-3 text-xs text-rose-800 ring-1 ring-rose-600/20">
                                This batch expired on {{ $product->expires_at->format('j F Y') }} and is no longer for sale.
                            </p>
                        @endif

                        @if ($product->isComingSoon())
                            <p class="mt-4 rounded-lg bg-violet-50 p-3 text-xs text-violet-800 ring-1 ring-violet-600/20">
                                This batch is still on its way — it lands on the shelf on
                                <strong class="font-semibold">{{ $product->available_from->format('j F Y') }}</strong>.
                                Save it to your favourites to find it again.
                            </p>
                        @endif

                        <div class="mt-4 flex flex-wrap items-start gap-2">
                            @if ($product->isSellable() && ! $product->isOutOfStock())
                                <form method="POST" action="{{ route('cart.store') }}" class="flex flex-1 gap-2"
                                      data-add-to-cart="{{ $product->id }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    <label class="sr-only" for="qty">Quantity</label>
                                    <input id="qty" type="number" name="quantity" value="1" min="1"
                                           max="{{ min(99, max(1, (int) $product->stock)) }}"
                                           class="w-20 rounded-lg border border-ink-300 px-3 py-2.5 text-center text-sm font-semibold tabular-nums focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25 focus:outline-none">

                                    <button type="submit" class="btn btn-primary btn-lg flex-1">
                                        <x-icon name="cart" class="size-4" />
                                        Add to cart
                                    </button>
                                </form>
                            @endif

                            <x-favourite-button :product="$product" size="lg"
                                                :active="app(\App\Cart\FavouriteService::class)->has($product->id)" />
                        </div>

                        @unless ($product->is_active)
                            <p class="mt-4 rounded-lg bg-amber-50 p-3 text-xs text-amber-800 ring-1 ring-amber-600/20">
                                This item is currently unavailable.
                            </p>
                        @endunless

                        <dl class="mt-5 space-y-2.5 border-t border-ink-100 pt-4 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-ink-500">SKU</dt>
                                <dd class="font-mono text-xs font-semibold text-ink-800" data-copy="{{ $product->sku }}" role="button" tabindex="0">
                                    {{ $product->sku }}
                                    <span data-copy-label class="text-[0.625rem] font-normal text-ink-400"></span>
                                </dd>
                            </div>
                            @if ($product->barcode)
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-ink-500">Barcode</dt>
                                    <dd class="font-mono text-xs font-semibold text-ink-800">{{ $product->barcode }}</dd>
                                </div>
                            @endif
                            @if ($product->weight)
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-ink-500">Weight</dt>
                                    <dd class="font-semibold text-ink-800 tabular-nums">{{ $product->weight }} kg</dd>
                                </div>
                            @endif
                            @if ($product->produced_at || $product->expires_at || $product->isComingSoon())
                                <div class="flex flex-col gap-1 rounded-lg bg-ink-50 p-3 sm:flex-row sm:items-center sm:justify-between sm:gap-3">
                                    <dt class="text-ink-500">Freshness</dt>
                                    <dd class="sm:text-end">
                                        <x-freshness :product="$product" />
                                    </dd>
                                </div>
                            @endif
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-ink-500">Classification</dt>
                                <dd class="font-semibold text-ink-800">{{ $product->category?->name ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>
                </section>

                @if ($movements->isNotEmpty())
                    <section class="card">
                        <div class="card-header">
                            <h2 class="card-title">Recent stock activity</h2>
                        </div>
                        <ul class="divide-y divide-ink-100">
                            @foreach ($movements as $movement)
                                <li class="flex items-center justify-between gap-3 px-4 py-2.5 sm:px-5">
                                    <span class="text-xs font-medium text-ink-700">{{ $movement->type->label() }}</span>
                                    <span @class([
                                        'text-sm font-bold tabular-nums',
                                        'text-emerald-600' => $movement->isPositive(),
                                        'text-rose-600' => ! $movement->isPositive(),
                                    ])>{{ $movement->isPositive() ? '+' : '' }}{{ $movement->quantity }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </aside>
        </div>

        @if ($related->isNotEmpty())
            <section class="mt-10">
                <h2 class="mb-4 text-lg font-bold tracking-tight text-ink-900">You might also like</h2>
                <div class="grid gap-3 grid-cards sm:gap-4">
                    @foreach ($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.public>
