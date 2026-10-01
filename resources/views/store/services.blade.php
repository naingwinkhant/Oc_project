<x-layouts.public title="Services" description="What the store does: zoned delivery, how to pay, freshness checks, changes and returns.">
    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <x-page-heading icon="truck" title="Services"
                       lede="Everything the store does for you, and nothing it will not do. No account is needed to read this or to order." />

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            @foreach ($services as $service)
                <article class="card">
                    <div class="card-body flex gap-3.5">
                        <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-brand-50 text-brand-700 ring-1 ring-brand-600/15">
                            <x-icon :name="$service['icon']" class="size-5" />
                        </span>

                        <div class="min-w-0">
                            <h2 class="text-sm font-semibold text-ink-900">{{ $service['title'] }}</h2>
                            <p class="mt-1 text-sm leading-relaxed text-ink-600">{{ $service['body'] }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- The rules the till actually enforces, from the same config the
             checkout reads. --}}
        <section class="card mt-6">
            <div class="card-header">
                <h2 class="card-title">The rules we sell by</h2>
                <p class="text-xs text-ink-400">The same list is on your settings page.</p>
            </div>

            <div class="card-body space-y-5">
                @foreach ($rules as $rule)
                    <div>
                        <h3 class="text-sm font-semibold text-ink-900">{{ $rule['title'] }}</h3>
                        <ul class="mt-2 space-y-1.5">
                            @foreach ($rule['lines'] as $line)
                                <li class="flex items-start gap-2 text-xs leading-relaxed text-ink-600">
                                    <x-icon name="check" class="mt-0.5 size-3.5 shrink-0 text-brand-600" />
                                    <span>{{ $line }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <a href="{{ route('catalog.index') }}" class="btn btn-primary">Browse the goods</a>
            <a href="{{ route('information') }}" class="btn btn-secondary">Store information</a>
        </div>
    </div>
</x-layouts.public>
