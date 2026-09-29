@props([
    'product' => null,
    'price' => null,
    'salePrice' => null,
    'unit' => null,
    'size' => 'md',
    'showUnit' => true,
])

@php
    $list = $product?->listPrice() ?? (int) ($price ?? 0);
    $sale = $product?->hasDiscount() ? $product->effectivePrice() : ($salePrice !== null ? (int) $salePrice : null);
    $hasDiscount = $sale !== null && $sale > 0 && $sale < $list;
    $unit ??= $product?->unit;

    $sizes = [
        'sm' => ['current' => 'text-sm font-semibold', 'original' => 'text-[0.6875rem]', 'unit' => 'text-[0.625rem]'],
        'md' => ['current' => 'text-lg font-bold', 'original' => 'text-xs', 'unit' => 'text-[0.6875rem]'],
        'lg' => ['current' => 'text-3xl font-bold', 'original' => 'text-sm', 'unit' => 'text-sm'],
    ];
    $scale = $sizes[$size] ?? $sizes['md'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex flex-wrap items-baseline gap-x-2 gap-y-0.5']) }}>
    {{-- What the customer pays: blue --}}
    <span class="{{ $scale['current'] }} tracking-tight text-blue-700 tabular-nums">
        {{ \App\Support\Money::format($hasDiscount ? $sale : $list) }}
        @if ($showUnit && $unit)
            <span class="{{ $scale['unit'] }} font-medium text-ink-400">/ {{ $unit }}</span>
        @endif
    </span>

    {{-- The original price, struck through in red when discounted --}}
    @if ($hasDiscount)
        <s class="{{ $scale['original'] }} font-medium text-rose-600 tabular-nums">
            {{ \App\Support\Money::format($list) }}
        </s>
        <span class="badge bg-rose-50 text-rose-700 ring-rose-600/20">
            −{{ $product?->discountPercent() ?? (int) round(($list - $sale) / $list * 100) }}%
        </span>
    @endif
</span>
