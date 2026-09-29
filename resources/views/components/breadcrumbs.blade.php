@props([
    // Each crumb: ['label' => 'Bakery', 'url' => 'https://…']. The last one is
    // the current page, so it renders as plain text and is never linked.
    'items' => [],
    'back' => true,
    'class' => '',
])

@if (filled($items))
    @php
        $last = array_key_last($items);
        // Where the back arrow goes when the browser has no history to pop.
        $fallback = collect($items)->slice(0, -1)->filter(fn ($crumb) => filled($crumb['url'] ?? null))->last();
    @endphp

    <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 {{ $class }}" aria-label="Breadcrumb">
        @if ($back)
            <a href="{{ $fallback['url'] ?? url('/') }}" data-back
               class="btn-icon size-7 shrink-0 -ms-1"
               title="Go back" aria-label="Go back">
                <x-icon name="arrow-left" class="size-4" />
            </a>
        @endif

        <ol class="flex flex-wrap items-center gap-1.5 text-xs">
            @foreach ($items as $index => $crumb)
                <li class="flex min-w-0 items-center gap-1.5">
                    @if ($index !== $last && filled($crumb['url'] ?? null))
                        <a href="{{ $crumb['url'] }}"
                           class="truncate text-ink-500 transition-colors hover:text-brand-700">
                            {{ $crumb['label'] }}
                        </a>
                    @else
                        <span @class([
                            'truncate',
                            'font-semibold text-ink-900' => $index === $last,
                            'text-ink-500' => $index !== $last,
                        ])>{{ $crumb['label'] }}</span>
                    @endif

                    @unless ($loop->last)
                        <x-icon name="chevron-right" class="size-3 shrink-0 text-ink-300" />
                    @endunless
                </li>
            @endforeach
        </ol>
    </nav>
@endif
