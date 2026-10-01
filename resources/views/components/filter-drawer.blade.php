@props([
    // Unique per page: the button, the panel and the overlay address each other.
    'id' => 'filter-drawer',
    'label' => 'Classifications & filters',
    'active' => 0,
    'icon' => 'sliders',
])

{{-- Phone only. From lg up the sidebar and the filter card do the same job. --}}
<div class="lg:hidden">
    <button type="button"
            class="btn-icon relative"
            data-toggle="{{ $id }},{{ $id }}-overlay"
            aria-controls="{{ $id }}"
            aria-expanded="false"
            aria-label="{{ $label }}">
        <x-icon :name="$icon" />

        @if ($active > 0)
            <span class="absolute -end-1 -top-1 grid min-w-4.5 place-items-center rounded-full bg-brand-600 px-1 text-[0.625rem] font-bold text-white tabular-nums ring-2 ring-white"
                  aria-hidden="true">{{ $active }}</span>
        @endif
    </button>

    <div id="{{ $id }}-overlay"
         data-overlay-lock="true"
         data-toggle="{{ $id }}-overlay!"
         class="fixed inset-0 z-40 hidden bg-ink-950/50 backdrop-blur-sm"></div>

    <div id="{{ $id }}"
         data-overlay-lock="true"
         role="dialog"
         aria-modal="true"
         aria-label="{{ $label }}"
         class="fixed inset-y-0 start-0 z-50 hidden w-80 max-w-[85vw] flex-col bg-surface shadow-2xl">
        <div class="flex h-16 shrink-0 items-center gap-2.5 border-b border-ink-200 px-4">
            <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-brand-50 text-brand-700">
                <x-icon :name="$icon" class="size-4.5" />
            </span>
            <p class="min-w-0 flex-1 truncate text-sm font-bold tracking-tight text-ink-900">{{ $label }}</p>
            <button type="button" class="btn-icon" data-toggle="{{ $id }},{{ $id }}-overlay!" aria-label="Close">
                <x-icon name="x" class="size-4" />
            </button>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain p-4">
            {{ $slot }}
        </div>
    </div>
</div>
