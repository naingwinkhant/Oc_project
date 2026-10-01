<x-layouts.app title="Manager" heading="Manager dashboard"
               description="The shop at a glance, and the day-to-day work you are responsible for">
    <div class="space-y-5">
        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            <x-stat-card label="Orders" :value="number_format($stats['orders'])" icon="clipboard" tone="brand"
                         hint="all time" />
            <x-stat-card label="Awaiting payment" :value="number_format($stats['awaitingPayment'])" icon="clock"
                         tone="amber" />
            <x-stat-card label="To pick and pack" :value="number_format($stats['toComplete'])" icon="box"
                         tone="emerald" hint="paid, not handed over" />
            <x-stat-card label="Low stock" :value="number_format($stats['lowStock'])" icon="alert" tone="rose" />
            <x-stat-card label="Waiting to be accepted" :value="number_format($stats['pendingAccounts'])" icon="users"
                         tone="violet" hint="new accounts" />
            <x-stat-card label="Unread order alerts" :value="number_format($stats['openAlerts'])" icon="bell"
                         tone="sky" />
        </section>

        <div class="grid items-start gap-4 xl:grid-cols-3">
            <section class="card overflow-hidden xl:col-span-2">
                <div class="card-header">
                    <h2 class="card-title">Latest orders</h2>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost btn-sm">
                        All orders <x-icon name="arrow-right" class="size-4" />
                    </a>
                </div>

                @if ($recentOrders->isEmpty())
                    <x-empty-state icon="clipboard" title="No orders yet"
                                  description="Orders placed through the catalogue appear here." />
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th class="hidden sm:table-cell">Customer</th>
                                    <th class="text-end">Total</th>
                                    <th>Status</th>
                                    <th class="hidden text-end lg:table-cell">Placed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}"
                                               class="font-mono text-xs font-semibold text-ink-900 hover:text-brand-700">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td class="hidden sm:table-cell">
                                            <p class="truncate text-sm text-ink-800">{{ $order->customer_name }}</p>
                                            <p class="truncate text-[0.6875rem] text-ink-400">{{ $order->township }}</p>
                                        </td>
                                        <td class="text-end text-sm font-semibold tabular-nums text-ink-900">
                                            {{ $order->totalFormatted() }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $order->status->tone() }}">{{ $order->status->label() }}</span>
                                        </td>
                                        <td class="hidden text-right text-xs text-ink-400 lg:table-cell">
                                            {{ $order->placed_at?->diffForHumans() ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <div class="space-y-4">
                <section class="card overflow-hidden @if ($stats['pendingAccounts'] > 0) ring-1 ring-inset ring-violet-600/20 @endif">
                    <div class="card-header bg-violet-50/60 dark:bg-violet-950/20">
                        <div class="flex min-w-0 items-center gap-2">
                            <x-icon name="users" class="size-4 shrink-0 text-violet-600" />
                            <h2 class="card-title">New accounts</h2>
                        </div>
                    </div>

                    @if ($stats['pendingAccounts'] > 0)
                        {{-- Both buttons here, not just a link: a manager can decide
                             without leaving this page, and each one says what it does
                             because rejecting cannot be undone. --}}
                        <div class="divide-y divide-ink-100 dark:divide-ink-200">
                            @foreach ($waitingAccounts as $person)
                                <div class="p-3.5">
                                    <div class="flex min-w-0 items-center gap-2.5">
                                        <span class="grid size-8 shrink-0 place-items-center rounded-full bg-violet-100 text-[0.6875rem] font-bold text-violet-700">
                                            {{ $person->initials() }}
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-semibold text-ink-900">{{ $person->name }}</p>
                                            <p class="truncate text-[0.6875rem] text-ink-400">
                                                {{ $person->email }} &#183; {{ $person->created_at?->diffForHumans() ?? 'just now' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-2.5 flex items-center gap-1.5">
                                        <form method="POST" action="{{ route('admin.approvals.accept', $person) }}"
                                              data-confirm="Accept {{ $person->name }} as staff? They will be able to sign in at the staff door.">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-primary btn-sm flex-1"
                                                    title="Accept: {{ $person->name }} becomes staff and can sign in">
                                                <x-icon name="check" class="size-3.5" /> Accept as staff
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.approvals.reject', $person) }}"
                                              data-confirm="Turn down {{ $person->name }} for good? They will never be able to sign in.">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-secondary btn-sm flex-1 text-rose-600 hover:bg-rose-50"
                                                    title="Reject: {{ $person->name }} can never sign in">
                                                <x-icon name="x" class="size-3.5" /> Reject
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <p class="border-t border-ink-100 bg-ink-50 px-3.5 py-2.5 text-[0.6875rem] leading-relaxed text-ink-600 dark:border-ink-200 dark:bg-ink-100 dark:text-ink-300">
                            <strong class="font-semibold text-ink-800 dark:text-ink-100">Accept</strong> makes them a
                            member of staff who can sign in.
                            <strong class="font-semibold text-ink-800 dark:text-ink-100">Reject</strong> closes the
                            account for good.
                        </p>

                        @if ($stats['pendingAccounts'] > $waitingAccounts->count())
                            <a href="{{ route('admin.approvals.index') }}" class="card-footer justify-center text-xs font-semibold text-brand-700 hover:text-brand-800">
                                See all {{ $stats['pendingAccounts'] }} waiting
                                <x-icon name="arrow-right" class="size-3.5" />
                            </a>
                        @endif
                    @else
                        <div class="card-body">
                            <p class="text-sm text-ink-500">Nothing waiting. Every account has been looked at.</p>
                        </div>
                    @endif
                </section>

                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title">Your work</h2>
                    </div>
                    <div class="card-body space-y-1.5">
                        @foreach ([
                            ['admin.orders.index', 'clipboard', 'Orders'],
                            ['admin.products.index', 'box', 'Goods'],
                            ['admin.categories.index', 'layers', 'Classifications'],
                            ['admin.suppliers.index', 'truck', 'Suppliers'],
                            ['admin.stock.index', 'clipboard', 'Stock movements'],
                            ['admin.notices.index', 'bell', 'Notices'],
                            ['admin.approvals.index', 'users', 'New account approvals'],
                        ] as [$route, $icon, $label])
                            <a href="{{ route($route) }}" class="nav-link">
                                <x-icon :name="$icon" class="size-4 shrink-0" />
                                <span class="truncate">{{ $label }}</span>
                                <x-icon name="chevron-right" class="ms-auto size-4 shrink-0 opacity-40" />
                            </a>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts.app>