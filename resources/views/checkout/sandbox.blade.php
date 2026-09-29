<x-layouts.guest title="Sandbox payment" eyebrow="No real money moves">
    <div class="mb-5">
        <div class="flex items-center gap-2">
            <span class="badge bg-amber-50 text-amber-700 ring-amber-600/20">Test mode</span>
            <span class="font-mono text-xs text-ink-400">{{ $order->order_number }}</span>
        </div>
        <h2 class="mt-2 text-lg font-bold tracking-tight text-ink-900">Simulated {{ $order->payment_gateway?->label() }} payment</h2>
        <p class="mt-1 text-sm text-ink-500">
            This page stands in for the provider's hosted checkout so the whole flow can be
            tested without merchant credentials.
        </p>
    </div>

    <div class="mb-5 rounded-xl bg-ink-50 p-4">
        <div class="flex items-center justify-between text-sm">
            <span class="text-ink-600">Order total</span>
            <span class="text-lg font-bold text-ink-900 tabular-nums">{{ $order->totalFormatted() }}</span>
        </div>
        <p class="mt-1 text-xs text-ink-400">
            {{ $order->itemCount() }} {{ Str::plural('item', $order->itemCount()) }} ·
            {{ $order->customer_name }} · {{ $order->township }}
        </p>
    </div>

    <form method="POST" action="{{ route('payments.sandbox.settle', $order) }}" class="space-y-3">
        @csrf
        <input type="hidden" name="signature" value="{{ $signature }}">

        <button type="submit" name="outcome" value="paid" class="btn btn-primary btn-lg w-full">
            <x-icon name="check" class="size-4" />
            Pay {{ $order->totalFormatted() }}
        </button>

        <button type="submit" name="outcome" value="failed" class="btn btn-secondary w-full">
            <x-icon name="x" class="size-4" />
            Simulate a decline
        </button>
    </form>

    <p class="mt-5 text-center text-[0.6875rem] text-ink-400">
        Settling this order writes a stock-out movement for every line item.
    </p>
</x-layouts.guest>
