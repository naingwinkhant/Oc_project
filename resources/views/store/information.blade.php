<x-layouts.public title="Information" description="About the store, where to find us, and when we are open.">
    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <x-page-heading icon="info" title="Information"
                       :lede="'Where we are, when we are open, and how to reach us. Serving Yangon since '.$info['founded'].'.'" />

        <div class="mt-6 grid gap-4 lg:grid-cols-3">
            <section class="card lg:col-span-2">
                <div class="card-header">
                    <h2 class="card-title">About the store</h2>
                </div>
                <div class="card-body space-y-3 text-sm leading-relaxed text-ink-600">
                    @foreach ($info['about'] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            </section>

            <div class="space-y-4">
                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title">Opening hours</h2>
                    </div>
                    <div class="card-body">
                        <dl class="space-y-2.5">
                            @foreach ($info['hours'] as $slot)
                                <div class="flex items-baseline justify-between gap-3 text-sm">
                                    <dt class="text-ink-600">{{ $slot['label'] }}</dt>
                                    <dd class="shrink-0 text-end font-semibold tabular-nums text-ink-900">{{ $slot['value'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title">Reach us</h2>
                    </div>
                    <div class="card-body space-y-3 text-sm">
                        <p class="flex items-start gap-2.5 text-ink-600">
                            <x-icon name="phone" class="mt-0.5 size-4 shrink-0 text-ink-400" />
                            <a href="tel:{{ preg_replace('/\s+/', '', $info['phone']) }}" class="link">{{ $info['phone'] }}</a>
                        </p>
                        <p class="flex items-start gap-2.5 text-ink-600">
                            <x-icon name="mail" class="mt-0.5 size-4 shrink-0 text-ink-400" />
                            <a href="mailto:{{ $info['email'] }}" class="link">{{ $info['email'] }}</a>
                        </p>
                        <p class="flex items-start gap-2.5 text-ink-600">
                            <x-icon name="map-pin" class="mt-0.5 size-4 shrink-0 text-ink-400" />
                            <span>{{ $info['address'] }}</span>
                        </p>
                    </div>
                </section>
            </div>
        </div>

        @if ($info['branches'])
            <section class="card mt-4">
                <div class="card-header">
                    <h2 class="card-title">Branches</h2>
                </div>
                <div class="card-body grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($info['branches'] as $branch)
                        <div>
                            <h3 class="text-sm font-semibold text-ink-900">{{ $branch['name'] }}</h3>
                            <p class="mt-1 text-xs leading-relaxed text-ink-600">{{ $branch['address'] }}</p>
                            <p class="mt-1 text-xs text-ink-500">{{ $branch['phone'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="card mt-4">
            <div class="card-header">
                <h2 class="card-title">Payment</h2>
            </div>
            <div class="card-body text-sm leading-relaxed text-ink-600">
                <p>{{ $info['paymentNote'] }}</p>
                <a href="{{ route('services') }}" class="link mt-2 inline-block">See the full list of services</a>
            </div>
        </section>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <a href="{{ route('catalog.index') }}" class="btn btn-primary">Browse the goods</a>
            <a href="{{ route('services') }}" class="btn btn-secondary">How we deliver</a>
        </div>
    </div>
</x-layouts.public>
