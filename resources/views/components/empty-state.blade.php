@props([
    'icon' => 'inbox',
    'title' => 'Nothing here yet',
    'description' => null,
])

<div class="flex flex-col items-center justify-center px-6 py-14 text-center">
    <span class="grid size-14 place-items-center rounded-2xl bg-ink-100 text-ink-400">
        <x-icon :name="$icon" class="size-7" />
    </span>
    <h3 class="mt-4 text-sm font-semibold text-ink-900">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-ink-500">{{ $description }}</p>
    @endif
    @isset($action)
        <div class="mt-5">{{ $action }}</div>
    @endisset
</div>
