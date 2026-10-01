@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Orders', 'url' => route('admin.orders.index')],
        ['label' => $order->order_number, 'url' => route('admin.orders.show', $order)],
        ['label' => 'Edit details'],
    ]" />
@endsection

<x-layouts.app :title="'Edit '.$order->order_number" heading="Edit delivery details"
               description="Only who it goes to and where. The items and the total are what the shopper agreed to and what was charged.">
    <div class="grid items-start gap-5 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title">Where it goes</h2>
                        <span class="badge {{ $order->status->tone() }}">{{ $order->status->label() }}</span>
                    </div>

                    <div class="card-body grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="customer_name" class="label">Full name <span class="text-rose-500">*</span></label>
                            <input id="customer_name" name="customer_name" type="text" required maxlength="120"
                                   value="{{ old('customer_name', $order->customer_name) }}"
                                   class="input @if ($errors->has('customer_name')) input-error @endif">
                            @error('customer_name')
                                <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="label">Phone <span class="text-rose-500">*</span></label>
                            <input id="phone" name="phone" type="text" required maxlength="30"
                                   value="{{ old('phone', $order->phone) }}"
                                   class="input @if ($errors->has('phone')) input-error @endif">
                            @error('phone')
                                <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="delivery_address" class="label">Delivery address <span class="text-rose-500">*</span></label>
                            <textarea id="delivery_address" name="delivery_address" rows="2" required maxlength="255"
                                      class="input @if ($errors->has('delivery_address')) input-error @endif">{{ old('delivery_address', $order->delivery_address) }}</textarea>
                            @error('delivery_address')
                                <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="township" class="label">Township <span class="text-rose-500">*</span></label>
                            <select id="township" name="township" required
                                    class="select @if ($errors->has('township')) input-error @endif">
                                @foreach ($townships as $township)
                                    <option value="{{ $township }}" @selected(old('township', $order->township) === $township)>{{ $township }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1.5 text-xs text-ink-400">Changing the township changes the delivery fee that was quoted.</p>
                            @error('township')
                                <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title">What cannot be changed here</h2>
                    </div>
                    <div class="card-body text-sm leading-relaxed text-ink-600">
                        <p>
                            The items, the unit prices and the total are not editable on an order, because
                            they are what the shopper agreed to and what was charged. To put something
                            right after payment, refund the order and place a new one.
                        </p>
                    </div>
                </section>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="submit" class="btn btn-primary">
                        <x-icon name="check" class="size-4" /> Save changes
                    </button>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        <div class="space-y-5">
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Order</h2>
                </div>
                <div class="card-body space-y-3 text-sm">
                    <div class="flex justify-between gap-3">
                        <span class="text-ink-500">Number</span>
                        <span class="font-mono font-semibold text-ink-900">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-ink-500">Placed</span>
                        <span class="text-ink-900">{{ $order->placed_at?->format('j M Y, g:i A') }}</span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-ink-500">Items</span>
                        <span class="text-ink-900">{{ $order->itemCount() }}</span>
                    </div>
                    <div class="flex justify-between gap-3 border-t border-ink-100 pt-3">
                        <span class="text-ink-500">Total</span>
                        <span class="font-bold text-ink-900 tabular-nums">{{ $order->totalFormatted() }}</span>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>