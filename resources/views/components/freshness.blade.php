@props(['product', 'variant' => 'detail', 'class' => ''])

@php
    $fresh = $product->freshness();
    $tone = $product->isExpired()
        ? 'text-rose-700'
        : ($product->isComingSoon() ? 'text-violet-700' : ($product->expiryStatus() === 'expiring' ? 'text-amber-700' : 'text-ink-500'));
@endphp

@if ($variant === 'detail')
    <div class="text-xs leading-relaxed {{ $class }}">
        <p class="flex flex-wrap items-center gap-x-3 gap-y-1 font-medium text-ink-700">
            @if ($fresh['packed'])
                <span class="inline-flex items-center gap-1.5">
                    <x-icon name="calendar" class="size-3.5 shrink-0 opacity-60" />
                    Packed {{ $fresh['packed'] }}
                </span>
            @endif

            @if ($fresh['best_before'])
                <span class="inline-flex items-center gap-1.5">
                    <x-icon name="clock" class="size-3.5 shrink-0 opacity-60" />
                    Best before {{ $fresh['best_before'] }}
                </span>
            @endif
        </p>

        <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 font-medium {{ $tone }}">
            @if ($fresh['shelf_life'])
                <span>{{ $fresh['shelf_life'] }}</span>
            @endif

            @if ($fresh['remaining'])
                <span>{{ $fresh['remaining'] }}</span>
            @endif

            @if ($fresh['note'])
                <span>{{ $fresh['note'] }}</span>
            @endif
        </p>
    </div>
@else
    @php $line = $product->freshnessLine(); @endphp

    @if ($line)
        <p class="text-[0.6875rem] leading-snug {{ $tone }} {{ $class }}">{{ $line }}</p>
    @endif
@endif
