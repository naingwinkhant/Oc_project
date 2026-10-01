<x-layouts.public :title="'Order '.$order->order_number" description="Order confirmation and payment">
    <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <div class="mb-6 text-center">
            @if ($order->isPaid())
                <span class="mx-auto grid size-14 place-items-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-emerald-600/20">
                    <x-icon name="check" class="size-7" />
                </span>
                <h1 class="mt-3 text-2xl font-bold tracking-tight text-ink-900">Payment received — thank you!</h1>
                <p class="mt-1 text-sm text-ink-500">
                    Order <span class="font-mono font-semibold text-ink-800">{{ $order->order_number }}</span> is confirmed.
                    We will call {{ $order->phone }} before delivery.
                </p>
            @elseif ($order->status === \App\Enums\OrderStatus::Cancelled)
                <span class="mx-auto grid size-14 place-items-center rounded-full bg-ink-100 text-ink-500">
                    <x-icon name="x" class="size-7" />
                </span>
                <h1 class="mt-3 text-2xl font-bold tracking-tight text-ink-900">Order cancelled</h1>
                <p class="mt-1 text-sm text-ink-500">
                    Order <span class="font-mono font-semibold text-ink-800">{{ $order->order_number }}</span> was cancelled. Nothing was charged.
                </p>
            @else
                <span class="mx-auto grid size-14 place-items-center rounded-full bg-amber-50 text-amber-600 ring-1 ring-amber-600/20">
                    <x-icon name="clock" class="size-7" />
                </span>
                <h1 class="mt-3 text-2xl font-bold tracking-tight text-ink-900">Complete your payment</h1>
                <p class="mt-1 text-sm text-ink-500">
                    Order <span class="font-mono font-semibold text-ink-800">{{ $order->order_number }}</span>
                    is waiting for {{ strtolower($order->payment_gateway?->label() ?? 'payment') }}.
                </p>
            @endif
        </div>

        @isset($gatewayError)
            <div class="mb-5 rounded-lg bg-amber-50 p-4 text-sm text-amber-800 ring-1 ring-amber-600/20">
                <p class="font-semibold">Payment could not be started</p>
                <p class="mt-1">{{ $gatewayError }}</p>
            </div>
        @endisset

        @if ($order->isPending())
            <section class="card mb-5 overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Pay {{ $summary_total = \App\Support\Money::format($order->total) }}</h2>
                        <p class="text-xs text-ink-400">Reference {{ $order->payment_reference ?? $order->order_number }}</p>
                    </div>
                    <span class="badge {{ $order->payment_gateway?->tone() }}">{{ $order->payment_gateway?->label() }}</span>
                </div>

                <div class="card-body">
                    @if ($intent && $intent['redirect_url'])
                        @if ($order->payment_gateway === \App\Enums\PaymentGateway::Sandbox)
                            <a href="{{ $intent['redirect_url'] }}" class="btn btn-primary btn-lg w-full">
                                <x-icon name="arrow-right" class="size-4" />
                                Continue to the payment page
                            </a>
                        @else
                            <a href="{{ $intent['redirect_url'] }}" class="btn btn-primary btn-lg w-full" rel="noopener">
                                <x-icon name="arrow-right" class="size-4" />
                                Pay with {{ $order->payment_gateway->label() }}
                            </a>
                        @endif
                    @elseif ($order->payment_gateway === \App\Enums\PaymentGateway::Sandbox)
                        <a href="{{ route('payments.sandbox', $order) }}" class="btn btn-primary btn-lg w-full">
                            <x-icon name="arrow-right" class="size-4" /> Open the sandbox payment page
                        </a>
                    @elseif ($order->payment_gateway === \App\Enums\PaymentGateway::Cash)
                        <p class="rounded-lg bg-emerald-50 p-4 text-sm text-emerald-800 ring-1 ring-emerald-600/20">
                            Pay the rider in cash when your goods arrive. Nothing more to do — we will call
                            {{ $order->phone }} to confirm.
                        </p>
                    @endif

                    @if ($intent && ! empty($intent['instructions']))
                        <ul class="mt-4 space-y-2">
                            @foreach ($intent['instructions'] as $line)
                                <li class="flex items-start gap-2 text-xs text-ink-500">
                                    <x-icon name="check" class="mt-px size-3.5 shrink-0 text-brand-600" />
                                    <span>{{ $line }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <form method="POST" action="{{ route('checkout.cancel', $order) }}" class="mt-5 border-t border-ink-100 pt-4"
                          onsubmit="return confirm('Cancel this order?')">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                            Cancel this order
                        </button>
                    </form>
                </div>
            </section>
        @endif

        <div class="grid items-start gap-5 sm:grid-cols-2">
            <section class="card overflow-hidden">
                <div class="card-header">
                    <h2 class="card-title">Items</h2>
                </div>
                <ul class="divide-y divide-ink-100">
                    @foreach ($order->items as $item)
                        <li class="flex items-center gap-3 px-4 py-2.5 sm:px-5">
                            <span class="grid size-8 shrink-0 place-items-center rounded-md bg-brand-50 text-xs font-bold text-brand-700 tabular-nums">
                                {{ $item->quantity }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-medium text-ink-800">{{ $item->name }}</p>
                                <p class="font-mono text-[0.6875rem] text-ink-400">{{ $item->sku }}</p>
                                {{-- The amount, shown as the arithmetic behind it. --}}
                                <p class="text-[0.6875rem] text-ink-500 tabular-nums">
                                    {{ $item->unitPriceFormatted() }} &times; {{ $item->quantity }} {{ $item->unit }}
                                </p>
                                @if ($item->product)
                                    <x-freshness :product="$item->product" variant="compact" />
                                @endif
                            </div>
                            <span class="shrink-0 text-sm font-bold text-ink-900 tabular-nums">
                                {{ $item->lineTotalFormatted() }}
                            </span>
                        </li>
                    @endforeach
                </ul>
                <div class="space-y-2 border-t border-ink-200 p-4 sm:p-5">
                    <div class="flex justify-between text-sm">
                        <span class="text-ink-600">Subtotal</span>
                        <span class="font-semibold tabular-nums">{{ \App\Support\Money::format($order->subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-ink-600">Delivery</span>
                        <span class="font-semibold tabular-nums">{{ \App\Support\Money::format($order->delivery_fee) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-ink-200 pt-2 text-base font-bold">
                        <span>Total</span>
                        <span class="tabular-nums">{{ $order->totalFormatted() }}</span>
                    </div>
                </div>
            </section>

            <section class="card overflow-hidden">
                <div class="card-header">
                    <h2 class="card-title">Delivery &amp; payment</h2>
                </div>
                <div class="card-body space-y-3 text-sm">
                    <div>
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Deliver to</p>
                        <p class="mt-0.5 font-medium text-ink-900">{{ $order->customer_name }}</p>
                        <p class="text-ink-600">{{ $order->delivery_address }}</p>
                        <p class="text-ink-600">{{ $order->township }}</p>
                    </div>
                    <div class="border-t border-ink-100 pt-3">
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Contact</p>
                        <p class="mt-0.5 text-ink-700">{{ $order->phone }}</p>
                        <p class="text-ink-600">{{ $order->email }}</p>
                    </div>
                    @if ($order->note)
                        <div class="border-t border-ink-100 pt-3">
                            <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Note</p>
                            <p class="mt-0.5 text-ink-600">{{ $order->note }}</p>
                        </div>
                    @endif
                    <div class="border-t border-ink-100 pt-3">
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Status</p>
                        <div class="mt-1.5 flex flex-wrap gap-2">
                            <span class="badge {{ $order->status->tone() }}">{{ $order->status->label() }}</span>
                            @if ($order->payment_gateway)
                                <span class="badge {{ $order->payment_gateway->tone() }}">{{ $order->payment_gateway->label() }}</span>
                            @endif
                        </div>
                        @if ($order->paid_at)
                            <p class="mt-2 text-xs text-ink-500">Paid {{ $order->paid_at->diffForHumans() }}</p>
                        @endif
                    </div>
                </div>
            </section>
        </div>

        <div class="mt-6 flex justify-center">
            <a href="{{ route('catalog.index') }}" class="btn btn-secondary">Continue shopping</a>
        </div>
    </div>
</x-layouts.public>
