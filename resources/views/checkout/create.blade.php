<x-layouts.public title="Checkout" description="Delivery details and payment">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <h1 class="mb-5 text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">Checkout</h1>

        <form method="POST" action="{{ route('checkout.store') }}" class="grid items-start gap-5 lg:grid-cols-3">
            @csrf

            <div class="space-y-5 lg:col-span-2">
                <section class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Delivery details</h2>
                            <p class="text-xs text-ink-400">Where should we bring your goods?</p>
                        </div>
                    </div>
                    <div class="card-body grid gap-4 sm:grid-cols-2">
                        <x-form-field field="customer_name" label="Full name" :value="old('customer_name', $user?->name)"
                                      required placeholder="Aung Kyaw" />

                        <x-form-field field="phone" label="Mobile number" :value="old('phone', $user?->phone)"
                                      required placeholder="09 380 000 00" />

                        <div class="sm:col-span-2">
                            <x-form-field field="email" label="Email address" type="email"
                                          :value="old('email', $user?->email)" required placeholder="you@example.com" />
                        </div>

                        <x-form-field field="township" label="Township" type="select" required>
                            <option value="">Choose a township…</option>
                            @foreach ($townships as $township)
                                <option value="{{ $township }}" @selected(old('township') === $township)>
                                    {{ $township }}
                                </option>
                            @endforeach
                        </x-form-field>

                        <div class="sm:col-span-2" data-delivery-quote hidden>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 rounded-lg border border-brand-200 bg-brand-50 p-3 text-sm text-brand-900">
                                <x-icon name="truck" class="size-4 shrink-0 text-brand-600" />
                                <span data-delivery-zone class="font-semibold"></span>
                                <span class="text-brand-700">·</span>
                                <span data-delivery-eta class="text-brand-800"></span>
                                <span class="ms-auto font-bold tabular-nums" data-delivery-fee></span>
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <x-form-field field="delivery_address" label="Street address" type="textarea" :rows="2"
                                          :value="old('delivery_address')" required
                                          placeholder="No. 12, Baho Road, Kamayut" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-form-field field="note" label="Note for the rider" type="textarea" :rows="2"
                                          :value="old('note')" placeholder="Call when you arrive, deliver to the 3rd floor…" />
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Payment method</h2>
                            <p class="text-xs text-ink-400">Myanmar online banking and wallets</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach ($gateways as $gateway)
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-ink-200 p-3.5 transition
                                              hover:border-brand-400 hover:bg-brand-50/40
                                              has-checked:border-brand-500 has-checked:bg-brand-50">
                                    <input type="radio" name="payment_gateway" value="{{ $gateway->value }}"
                                           class="checkbox mt-0.5" @checked(old('payment_gateway', $gateways->first()?->value) === $gateway->value)>

                                    <span class="min-w-0 flex-1">
                                        <span class="flex flex-wrap items-center gap-2">
                                            <span class="text-sm font-semibold text-ink-900">{{ $gateway->label() }}</span>
                                            @if ($gateway === \App\Enums\PaymentGateway::Sandbox)
                                                <span class="badge bg-ink-100 text-ink-600 ring-ink-500/10">Test mode</span>
                                            @endif
                                        </span>
                                        <span class="mt-0.5 block text-xs leading-relaxed text-ink-500">{{ $gateway->blurb() }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        @error('payment_gateway')
                            <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
                        @enderror
                    </div>
                </section>
            </div>

            <aside class="card lg:sticky lg:top-36">
                <div class="card-header">
                    <h2 class="card-title">Your order</h2>
                    <a href="{{ route('cart.index') }}" class="btn btn-ghost btn-sm">Edit</a>
                </div>

                <ul class="max-h-64 divide-y divide-ink-100 overflow-y-auto">
                    @foreach ($items as $item)
                        <li class="flex items-center gap-3 px-4 py-2.5 sm:px-5">
                            <span class="grid size-8 shrink-0 place-items-center rounded-md bg-brand-50 text-xs font-bold text-brand-700 tabular-nums">
                                {{ $item['quantity'] }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-medium text-ink-800">{{ $item['product']->name }}</p>
                                {{-- The amount, shown as the arithmetic behind it. --}}
                                <p class="text-[0.6875rem] text-ink-500 tabular-nums">
                                    {{ \App\Support\Money::format($item['product']->effectivePrice()) }}
                                    &times; {{ $item['quantity'] }}
                                    {{ $item['product']->unit }}
                                </p>
                                @if ($item['product']->hasDiscount())
                                    <p class="text-[0.6875rem] tabular-nums">
                                        <s class="text-rose-600">{{ \App\Support\Money::format($item['product']->price) }}</s>
                                        <span class="ms-1 font-medium text-blue-700">
                                            {{ $item['product']->discountPercent() }}% off
                                        </span>
                                    </p>
                                @endif
                                <x-freshness :product="$item['product']" variant="compact" />
                            </div>
                            <span class="shrink-0 text-sm font-bold text-blue-700 tabular-nums">
                                {{ \App\Support\Money::format($item['line_total']) }}
                            </span>
                        </li>
                    @endforeach
                </ul>

                <div class="card-body space-y-3 border-t border-ink-200"
                     data-delivery-calculator
                     data-subtotal="{{ $summary['subtotal'] }}"
                     data-currency="{{ \App\Support\Money::symbol() }}"
                     data-map='@json($townshipMap)'
                     data-zones='@json(collect($zones)->pluck("label", "key"))'>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-ink-600">Subtotal</span>
                        <span class="font-semibold text-ink-900 tabular-nums">{{ $summary['subtotal_formatted'] }}</span>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-ink-600">Delivery</span>
                        <span class="font-semibold text-ink-900 tabular-nums" data-delivery-summary>
                            {{ $summary['delivery_range']['min_formatted'] }} – {{ $summary['delivery_range']['max_formatted'] }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-t border-ink-200 pt-3">
                        <span class="text-sm font-semibold text-ink-900">Total to pay</span>
                        <span class="text-lg font-bold text-blue-700 tabular-nums" data-order-total>
                            {{ \App\Support\Money::format($summary['subtotal'] + $summary['delivery_range']['min']) }}
                        </span>
                    </div>

                    <p class="text-[0.6875rem] text-ink-400">
                        Delivery is quoted from your township at checkout.
                    </p>

                    <button type="submit" class="btn btn-primary btn-lg w-full">
                        <x-icon name="shield" class="size-4" />
                        Place order
                    </button>

                    <p class="text-center text-[0.6875rem] text-ink-400">
                        All prices in {{ \App\Support\Money::code() }} (Myanmar kyat).
                    </p>
                </div>
            </aside>
        </form>
    </div>
</x-layouts.public>
