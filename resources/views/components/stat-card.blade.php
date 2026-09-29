@props([
    'label',
    'value',
    'icon' => 'chart',
    'tone' => 'brand',
    'hint' => null,
    'trend' => null,
])

@php
    $tones = [
        'brand' => 'bg-brand-50 text-brand-700 ring-brand-600/15',
        'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/15',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-600/15',
        'rose' => 'bg-rose-50 text-rose-700 ring-rose-600/15',
        'sky' => 'bg-sky-50 text-sky-700 ring-sky-600/15',
        'violet' => 'bg-violet-50 text-violet-700 ring-violet-600/15',
    ];
@endphp

<div class="card p-4 transition-shadow duration-200 hover:shadow-raise sm:p-5">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-semibold tracking-wide text-ink-500 uppercase">{{ $label }}</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-ink-900 tabular-nums sm:text-[1.75rem]">{{ $value }}</p>
        </div>
        <span class="grid size-10 shrink-0 place-items-center rounded-lg ring-1 ring-inset {{ $tones[$tone] ?? $tones['brand'] }}">
            <x-icon :name="$icon" class="size-5" />
        </span>
    </div>

    @if ($hint || $trend)
        <p class="mt-3 flex items-center gap-1.5 text-xs text-ink-500">
            @if ($trend)
                <span @class([
                    'inline-flex items-center gap-0.5 font-semibold whitespace-nowrap',
                    'text-emerald-600' => str_starts_with((string) $trend, '+'),
                    'text-rose-600' => str_starts_with((string) $trend, '-'),
                ])>
                    <x-icon :name="str_starts_with((string) $trend, '-') ? 'arrow-down' : 'arrow-up'" class="size-3" />
                    {{ ltrim((string) $trend, '+-') }}
                </span>
            @endif
            {{ $hint }}
        </p>
    @endif
</div>
