<x-layouts.public title="History" description="What you have looked at on this device">
    <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">History</h1>
                <p class="mt-0.5 text-sm text-ink-500">
                    {{ $viewedCount }} {{ Str::plural('item', $viewedCount) }} browsed on this device.
                </p>
            </div>

            @if ($viewedCount > 0)
                <form method="POST" action="{{ route('history.clear') }}"
                      onsubmit="return confirm('Clear your browsing history?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                        <x-icon name="trash" class="size-4" /> Clear browsing history
                    </button>
                </form>
            @endif
        </div>

        <div class="space-y-8">
            {{-- Browsed --}}
            <section>
                <h2 class="section-title mb-3">Recently viewed</h2>

                @if ($viewed->isEmpty())
                    <x-empty-state icon="clock" title="Nothing browsed yet"
                                  description="Items you open will show up here so you can pick up where you left off.">
                        <x-slot:action>
                            <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-sm">Browse the catalogue</a>
                        </x-slot:action>
                    </x-empty-state>
                @else
                    <div class="grid gap-3 grid-cards sm:gap-4">
                        @foreach ($viewed as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- Orders. Nobody has to sign in to order, so there is no account
                 to look them up by; the till shows staff every order instead. --}}
            <section>
                <h2 class="section-title mb-3">Your orders</h2>

                @guest
                    <x-empty-state icon="clipboard" title="Orders need no account"
                                  description="You can order straight from the catalogue. Keep the order number from your receipt to check on it.">
                        <x-slot:action>
                            <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-sm">Browse the goods</a>
                        </x-slot:action>
                    </x-empty-state>
                @else
                    <x-empty-state icon="clipboard" title="Every order is in the dashboard"
                                  description="Orders are not tied to an account, so none of them are listed here. Use the dashboard to see and manage them.">
                        <x-slot:action>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-sm">Open orders</a>
                        </x-slot:action>
                    </x-empty-state>
                @endguest
            </section>
        </div>
    </div>
</x-layouts.public>
