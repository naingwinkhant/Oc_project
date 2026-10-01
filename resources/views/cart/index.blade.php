<x-layouts.public title="Your cart" description="Review the goods in your cart">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">Your cart</h1>
                <p class="mt-0.5 text-sm text-ink-500">
                    {{ $summary['count'] }} {{ Str::plural('item', $summary['count']) }} ready to check out
                </p>
            </div>

            @if (! $items->isEmpty())
                <form method="POST" action="{{ route('cart.clear') }}"
                      onsubmit="return confirm('Empty your whole cart?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                        <x-icon name="trash" class="size-4" />
                        Empty cart
                    </button>
                </form>
            @endif
        </div>

        @if ($items->isEmpty())
            <div class="card">
                <x-empty-state icon="cart" title="Your cart is empty"
                              description="Browse the catalogue and add the goods you need — we deliver across Yangon and beyond.">
                    <x-slot:action>
                        <a href="{{ route('catalog.index') }}" class="btn btn-primary">
                            <x-icon name="store" class="size-4" /> Start shopping
                        </a>
                    </x-slot:action>
                </x-empty-state>
            </div>
        @else
            @if ($blocked->isNotEmpty())
                <div class="mb-5 rounded-lg bg-rose-50 p-4 ring-1 ring-rose-600/20">
                    <p class="flex items-center gap-2 text-sm font-semibold text-rose-800">
                        <x-icon name="alert" class="size-4 shrink-0" />
                        Some items can no longer be sold
                    </p>
                    <ul class="mt-2 space-y-1 text-xs text-rose-700">
                        @foreach ($blocked as $line)
                            <li>
                                <span class="font-semibold">{{ $line['product']->name }}</span> — {{ $line['reason'] }}
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-2 text-xs text-rose-700">Remove them to continue to checkout.</p>
                </div>
            @endif
            <div class="grid items-start gap-5 lg:grid-cols-3">

                <div class="card overflow-hidden lg:col-span-2">
                    <ul class="divide-y divide-ink-100">
                        @foreach ($items as $item)
                            @php $product = $item['product']; @endphp
                            <li class="flex gap-3 p-4 sm:gap-4 sm:p-5">
                                <a href="{{ route('catalog.product', $product) }}" class="shrink-0">
                                    @if ($product->imageUrl())
                                        <img src="{{ $product->imageUrl() }}" alt="" loading="lazy"
                                             class="size-20 rounded-lg object-cover ring-1 ring-ink-200 sm:size-24">
                                    @else
                                        <span class="grid size-20 place-items-center rounded-lg bg-ink-100 text-ink-400 sm:size-24">
                                            <x-icon name="box" class="size-7" />
                                        </span>
                                    @endif
                                </a>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <a href="{{ route('catalog.product', $product) }}"
                                                       class="line-clamp-2 text-sm font-semibold text-ink-900 hover:text-brand-700">
                                                        {{ $product->name }}
                                                    </a>
                                                    <p class="mt-0.5 font-mono text-[0.6875rem] text-ink-400">{{ $product->sku }}</p>
                                                    <div class="mt-1 flex flex-wrap gap-1.5">
                                                        <span class="badge {{ $product->stockStatusTone() }}">{{ $product->stockStatusLabel() }}</span>
                                                        @if ($product->expires_at)
                                                            <span class="badge {{ $product->expiryStatusTone() }}">{{ $product->expiryStatusLabel() }}</span>
                                                        @endif
                                                    </div>
                                                    <x-freshness :product="$product" variant="compact" class="mt-1" />
                                                </div>

                                                <form method="POST" action="{{ route('cart.destroy', $product->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-icon hover:bg-rose-50 hover:text-rose-600"
                                                            title="Remove {{ $product->name }}" aria-label="Remove item">
                                                        <x-icon name="trash" class="size-4" />
                                                    </button>
                                                </form>
                                            </div>

                                    <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
                                        <form method="POST" action="{{ route('cart.update') }}"
                                              class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                                            @php $maxQuantity = min(99, max(1, (int) $product->stock)); @endphp

                                            <label class="sr-only" for="qty-{{ $product->id }}">Quantity</label>
                                            <div class="flex items-center rounded-lg border border-ink-300 bg-surface">
                                                <button type="button" class="grid size-8 place-items-center rounded-l-lg text-ink-500 transition hover:bg-ink-100"
                                                        data-step="-1" data-target="qty-{{ $product->id }}" aria-label="Decrease quantity">
                                                    <x-icon name="minus" class="size-3.5" />
                                                </button>
                                                <input id="qty-{{ $product->id }}" type="number" name="quantity"
                                                       value="{{ $item['quantity'] }}" min="0" max="{{ $maxQuantity }}"
                                                       class="w-12 border-x border-ink-200 py-1.5 text-center text-sm font-semibold tabular-nums focus:outline-none">
                                                <button type="button" class="grid size-8 place-items-center rounded-r-lg text-ink-500 transition hover:bg-ink-100"
                                                        data-step="1" data-target="qty-{{ $product->id }}" aria-label="Increase quantity">
                                                    <x-icon name="plus" class="size-3.5" />
                                                </button>
                                            </div>

                                            <button type="submit" class="btn btn-ghost btn-sm">Update</button>
                                        </form>

                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-blue-700 tabular-nums">
                                                {{ \App\Support\Money::format($item['line_total']) }}
                                            </p>
                                            <p class="text-[0.6875rem] text-ink-400 tabular-nums">
                                                @if ($product->hasDiscount())
                                                    <s class="text-rose-600">{{ \App\Support\Money::format($product->price) }}</s>
                                                    <span class="ms-1">× {{ $item['quantity'] }}</span>
                                                @else
                                                    {{ \App\Support\Money::format($product->price) }} × {{ $item['quantity'] }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <aside class="card lg:sticky lg:top-36">
                    <div class="card-header">
                        <h2 class="card-title">Order summary</h2>
                    </div>
                    <div class="card-body space-y-3">
                        {{-- The amount for every line: price x amount. --}}
                        <ul class="divide-y divide-ink-100 border-b border-ink-100 pb-1">
                            @foreach ($items as $item)
                                @php $line = $item['product']; @endphp
                                <li class="flex items-baseline justify-between gap-3 py-1.5 text-xs">
                                    <span class="min-w-0 flex-1 truncate text-ink-700">
                                        {{ $line->name }}
                                        <span class="text-ink-400">
                                            &times; {{ $item['quantity'] }} {{ $line->unit }}
                                        </span>
                                    </span>
                                    <span class="shrink-0 text-right tabular-nums">
                                        <span class="block font-semibold text-ink-900">
                                            {{ \App\Support\Money::format($item['line_total']) }}
                                        </span>
                                        <span class="block text-[0.625rem] text-ink-400">
                                            {{ \App\Support\Money::format($line->effectivePrice()) }} each
                                        </span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="flex items-baseline justify-between gap-3 text-sm">
                            <span class="text-ink-600">
                                Goods subtotal
                                <span class="block text-[0.6875rem] text-ink-400">
                                    sum of each item's price &times; its amount
                                </span>
                            </span>
                            <span class="text-end font-semibold text-ink-900 tabular-nums">
                                {{ $summary['subtotal_formatted'] }}
                                <span class="block text-[0.6875rem] font-normal text-ink-400">
                                    {{ $summary['count'] }} {{ Str::plural('unit', $summary['count']) }}
                                </span>
                            </span>
                        </div>

                        @if ($summary['savings'] > 0)
                            <div class="flex items-baseline justify-between gap-3 text-sm">
                                <span class="text-ink-600">
                                    Promotions
                                    <span class="block text-[0.6875rem] text-ink-400">
                                        was {{ $summary['undiscounted_formatted'] }}
                                    </span>
                                </span>
                                <span class="text-end font-semibold text-emerald-700 tabular-nums">
                                    &minus;{{ $summary['savings_formatted'] }}
                                </span>
                            </div>
                        @endif

                        <div class="flex items-baseline justify-between gap-3 text-sm">
                            <span class="text-ink-600">Delivery</span>
                            @if ($isFreeDelivery)
                                <span class="badge bg-emerald-50 text-emerald-700 ring-emerald-600/20">Free</span>
                            @else
                                <span class="text-end">
                                    <span class="block font-semibold text-ink-900 tabular-nums">
                                        {{ $summary['delivery_range']['min_formatted'] }} – {{ $summary['delivery_range']['max_formatted'] }}
                                    </span>
                                    <span class="block text-[0.6875rem] text-ink-400">by township</span>
                                </span>
                            @endif
                        </div>

                        <div class="flex items-baseline justify-between gap-3 border-t border-ink-200 pt-3">
                            <span class="text-sm font-semibold text-ink-900">Total</span>
                            <span class="text-end text-lg font-bold text-blue-700 tabular-nums">
                                {{ \App\Support\Money::format($summary['subtotal'] + ($isFreeDelivery ? 0 : $summary['delivery_range']['min'])) }}
                                <span class="block text-[0.6875rem] font-normal text-ink-400">
                                    plus delivery for your township
                                </span>
                            </span>
                        </div>
                        @unless ($isFreeDelivery)
                            <p class="rounded-lg bg-brand-50 p-2.5 text-xs text-brand-800">
                                Add <strong class="font-semibold">{{ \App\Support\Money::format($amountUntilFree) }}</strong>
                                more for free delivery anywhere in the country.
                            </p>
                        @endunless

                        @if ($blocked->isNotEmpty())
                            <span class="btn btn-secondary btn-lg w-full pointer-events-none opacity-50">
                                <x-icon name="alert" class="size-4" />
                                Remove unavailable items
                            </span>
                        @else
                            <a href="{{ route('checkout.create') }}" class="btn btn-primary btn-lg w-full">
                                <x-icon name="cart" class="size-4" />
                                Checkout
                            </a>
                        @endif

                        <p class="flex items-start gap-1.5 text-[0.6875rem] text-ink-400">
                            <x-icon name="shield" class="mt-px size-3 shrink-0" />
                            Pay by KBZPay, Wave Money, AyaPay, UAB Pay or cash on delivery.
                        </p>
                    </div>
                </aside>
            </div>
        @endif
    </div>
</x-layouts.public>
