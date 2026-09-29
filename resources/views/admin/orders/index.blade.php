@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Orders'],
    ]" />
@endsection

<x-layouts.app title="Orders" heading="Customer orders" description="Everything bought through the catalogue">
    <div class="space-y-4">
        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Orders" :value="number_format($stats['total'])" icon="clipboard" tone="brand" />
            <x-stat-card label="Awaiting payment" :value="number_format($stats['pending'])" icon="clock" tone="amber" />
            <x-stat-card label="Paid" :value="number_format($stats['paid'])" icon="check" tone="emerald" />
            <x-stat-card label="Revenue" :value="\App\Support\Money::compact($stats['revenue'])" icon="chart" tone="sky" />
        </section>

        <form method="GET" class="card">
            <div class="card-body grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="relative lg:col-span-2">
                    <x-icon name="search" class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ request('q') }}" data-live-search
                           placeholder="Order number, customer or phone…" class="input ps-9" aria-label="Search orders">
                </div>

                <select name="status" class="select" data-autosubmit aria-label="Filter by status">
                    <option value="">Any status</option>
                    @foreach (\App\Enums\OrderStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>

                <select name="gateway" class="select" data-autosubmit aria-label="Filter by payment method">
                    <option value="">Any payment method</option>
                    @foreach (\App\Enums\PaymentGateway::cases() as $gateway)
                        <option value="{{ $gateway->value }}" @selected(request('gateway') === $gateway->value)>{{ $gateway->label() }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <section class="card overflow-hidden">
            <div class="card-header">
                <h2 class="card-title">{{ number_format($orders->total()) }} {{ Str::plural('order', $orders->total()) }}</h2>
            </div>

            @if ($orders->isEmpty())
                <x-empty-state icon="cart" title="No orders yet"
                              description="Orders placed through the catalogue will appear here." />
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th class="hidden sm:table-cell">Customer</th>
                                <th class="hidden md:table-cell">Payment</th>
                                <th class="text-end">Total</th>
                                <th>Status</th>
                                <th class="hidden lg:table-cell text-end">Placed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                           class="font-mono text-xs font-semibold text-ink-900 hover:text-brand-700">
                                            {{ $order->order_number }}
                                        </a>
                                        <p class="text-[0.6875rem] text-ink-400">
                                            {{ $order->itemCount() }} {{ Str::plural('item', $order->itemCount()) }}
                                        </p>
                                    </td>
                                    <td class="hidden sm:table-cell">
                                        <p class="truncate text-sm font-medium text-ink-800">{{ $order->customer_name }}</p>
                                        <p class="truncate text-[0.6875rem] text-ink-400">{{ $order->phone }} · {{ $order->township }}</p>
                                    </td>
                                    <td class="hidden md:table-cell">
                                        @if ($order->payment_gateway)
                                            <span class="badge {{ $order->payment_gateway->tone() }}">{{ $order->payment_gateway->label() }}</span>
                                        @else
                                            <span class="text-xs text-ink-400">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span class="text-sm font-semibold text-ink-900 tabular-nums">
                                            {{ $order->totalFormatted() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $order->status->tone() }}">{{ $order->status->label() }}</span>
                                    </td>
                                    <td class="hidden whitespace-nowrap text-right text-xs text-ink-400 lg:table-cell">
                                        {{ $order->placed_at?->diffForHumans() ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-ink-200 px-4 py-3 sm:px-5">
                    {{ $orders->links('pagination::tailwind-simple') }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
