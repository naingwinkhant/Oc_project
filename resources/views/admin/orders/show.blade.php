@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Orders', 'url' => route('admin.orders.index')],
        ['label' => $order->order_number],
    ]" />
@endsection

<x-layouts.app :title="'Order '.$order->order_number" heading="Order details" :description="$order->customer_name.' · '.$order->placed_at?->format('j M Y, g:i A')">
    <div class="grid items-start gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <section class="card overflow-hidden">
                <div class="card-header">
                    <h2 class="card-title">Items</h2>
                    <span class="text-xs text-ink-400">{{ $order->itemCount() }} {{ Str::plural('unit', $order->itemCount()) }}</span>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Goods</th>
                                <th class="text-end">Unit price</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Line total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        <p class="text-sm font-semibold text-ink-900">{{ $item->name }}</p>
                                        <p class="font-mono text-[0.6875rem] text-ink-400">{{ $item->sku }}</p>
                                        @if ($item->product)
                                            <x-freshness :product="$item->product" variant="compact" />
                                        @endif
                                    </td>
                                    <td class="text-end text-sm tabular-nums">{{ $item->unitPriceFormatted() }}</td>
                                    <td class="text-end text-sm tabular-nums">{{ $item->quantity }} {{ $item->unit }}</td>
                                    <td class="text-end text-sm font-semibold tabular-nums">{{ $item->lineTotalFormatted() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
                    <h2 class="card-title">Payment attempts</h2>
                </div>
                @if ($order->payments->isEmpty())
                    <x-empty-state icon="credit" title="No payment recorded" />
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Gateway</th>
                                    <th class="hidden sm:table-cell">Reference</th>
                                    <th class="text-end">Amount</th>
                                    <th>Status</th>
                                    <th class="hidden lg:table-cell text-end">When</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->payments as $payment)
                                    <tr>
                                        <td>
                                            <span class="badge {{ $payment->gateway->tone() }}">{{ $payment->gateway->label() }}</span>
                                        </td>
                                        <td class="hidden font-mono text-xs text-ink-500 sm:table-cell">
                                            {{ $payment->gateway_reference ?? '—' }}
                                        </td>
                                        <td class="text-end text-sm tabular-nums">{{ $payment->amountFormatted() }}</td>
                                        <td>
                                            <span class="badge {{ $payment->status->tone() }}">{{ $payment->status->label() }}</span>
                                        </td>
                                        <td class="hidden whitespace-nowrap text-right text-xs text-ink-400 lg:table-cell">
                                            {{ $payment->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <div class="space-y-5">
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Status</h2>
                    <span class="badge {{ $order->status->tone() }}">{{ $order->status->label() }}</span>
                </div>
                <div class="card-body space-y-3 text-sm">
                    @if ($order->paid_at)
                        <p class="text-xs text-emerald-700">Paid {{ $order->paid_at->diffForHumans() }}</p>
                    @else
                        <form method="POST" action="{{ route('admin.orders.status', $order) }}"
                              onsubmit="return confirm('Cancel this order?')">
                            @csrf
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn btn-secondary btn-sm w-full text-rose-600 hover:bg-rose-50">
                                <x-icon name="x" class="size-3.5" /> Cancel order
                            </button>
                        </form>
                    @endif

                    <div class="border-t border-ink-100 pt-3">
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Payment method</p>
                        @if ($order->payment_gateway)
                            <span class="badge mt-1 {{ $order->payment_gateway->tone() }}">{{ $order->payment_gateway->label() }}</span>
                        @else
                            <p class="mt-1 text-ink-400">—</p>
                        @endif
                    </div>
                </div>
            </section>

            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Customer</h2>
                </div>
                <div class="card-body space-y-3 text-sm">
                    <div>
                        <p class="font-medium text-ink-900">{{ $order->customer_name }}</p>
                        @if ($order->user)
                            <p class="text-xs text-ink-400">
                                &#64;{{ $order->user->username }} · {{ $order->user->name }}
                            </p>
                        @endif
                    </div>
                    <div class="border-t border-ink-100 pt-3 text-ink-600">
                        <p>{{ $order->phone }}</p>
                        <p>{{ $order->email }}</p>
                    </div>
                    <div class="border-t border-ink-100 pt-3 text-ink-600">
                        <p class="text-xs leading-relaxed">{{ $order->delivery_address }}</p>
                        <p class="mt-0.5 text-xs">{{ $order->township }}</p>
                    </div>
                    @if ($order->note)
                        <div class="border-t border-ink-100 pt-3">
                            <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Note</p>
                            <p class="mt-0.5 text-xs text-ink-600">{{ $order->note }}</p>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
