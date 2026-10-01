@props([
    'icon' => 'store',
    'title',
    'lede' => null,
])

{{-- Heading for the plain customer pages: Services, Information. No breadcrumb,
     because the storefront does not use them. --}}
<div class="flex items-start gap-4">
    <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-brand-600 text-white shadow-raise">
        <x-icon :name="$icon" class="size-6" />
    </span>

    <div class="min-w-0">
        <h1 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">{{ $title }}</h1>
        @if ($lede)
            <p class="mt-1 max-w-2xl text-sm leading-relaxed text-ink-500">{{ $lede }}</p>
        @endif
    </div>
</div>
