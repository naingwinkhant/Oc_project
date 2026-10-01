<x-layouts.public title="Your account" description="Your orders and your details">
    <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <x-page-heading icon="user" title="Hello {{ Str::before($user->name, ' ') }}"
                       lede="Ordering never needs an account. This is just where you can see what you have already bought." />

        <div class="mt-6 space-y-5">
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Your details</h2>
                    <span class="badge {{ $user->role->color() }}">{{ $user->role->label() }}</span>
                </div>
                <div class="card-body grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Name</p>
                        <p class="mt-0.5 text-sm text-ink-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Email</p>
                        <p class="mt-0.5 text-sm text-ink-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Username</p>
                        <p class="mt-0.5 text-sm text-ink-900">{{ $user->username }}</p>
                    </div>
                    <div>
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Account</p>
                        <p class="mt-0.5">
                            <span class="badge {{ $user->status->tone() }}">{{ $user->status->label() }}</span>
                        </p>
                    </div>
                </div>
            </section>

            <section class="card overflow-hidden">
                <div class="card-header">
                    <h2 class="card-title">Your orders</h2>
                    <span class="text-xs text-ink-400">{{ $orders->count() }} shown, matched on {{ $user->email }}</span>
                </div>

                @if ($orders->isEmpty())
                    <x-empty-state icon="clipboard" title="No orders yet"
                                  description="Anything you order with this email address will be listed here.">
                        <x-slot:action>
                            <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-sm">Browse the goods</a>
                        </x-slot:action>
                    </x-empty-state>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th class="hidden sm:table-cell">Items</th>
                                    <th class="text-end">Total</th>
                                    <th>Status</th>
                                    <th class="hidden text-end lg:table-cell">Placed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('checkout.show', $order) }}"
                                               class="font-mono text-xs font-semibold text-ink-900 hover:text-brand-700">
                                                {{ $order->order_number }}
                                            </a>
                                            <p class="text-[0.6875rem] text-ink-400">{{ $order->township }}</p>
                                        </td>
                                        <td class="hidden sm:table-cell text-sm tabular-nums text-ink-600">
                                            {{ $order->itemCount() }}
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

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('catalog.index') }}" class="btn btn-primary">
                    <x-icon name="cart" class="size-4" /> Continue shopping
                </a>
                <a href="{{ route('settings') }}" class="btn btn-secondary">Settings</a>

                <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                    @csrf
                    <button type="submit" class="btn btn-ghost text-rose-600 hover:bg-rose-50">
                        <x-icon name="logout" class="size-4 rotate-180" /> Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.public>