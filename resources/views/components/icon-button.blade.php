@props([
    'icon' => 'cart',
    'href' => null,
    'count' => 0,
    'label' => 'Cart',
    'active' => false,
])

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => 'btn btn-secondary relative shrink-0 px-2.5 sm:px-3']) }}
   title="{{ $label }}{{ $count > 0 ? ' — '.$count.' item'.($count === 1 ? '' : 's') : '' }}"
   aria-label="{{ $label }}">
    <span class="grid size-5 shrink-0 place-items-center">
        <x-icon :name="$icon" @class([
            'fill-rose-600 text-rose-600' => $active,
            'fill-none' => ! $active,
        ]) />
    </span>

    <span class="hidden lg:inline">{{ $label }}</span>

    @if ($count > 0)
        <span @class([
            'absolute -top-1.5 -end-1.5 grid min-w-4.5 place-items-center rounded-full px-1 text-[0.625rem] leading-4 font-bold text-white tabular-nums',
            'bg-rose-600' => $active,
            'bg-brand-600' => ! $active,
        ])>
            {{ $count > 99 ? '99+' : $count }}
        </span>
    @endif
</a>
