@props(['node'])

@php
    $children = $node['children'] ?? [];
    $badge = fn (int $count) => $count > 0
        ? "rounded-full bg-ink-100 px-1.5 py-0.5 text-[0.625rem] font-semibold text-ink-600 tabular-nums"
        : "rounded-full bg-amber-50 px-1.5 py-0.5 text-[0.625rem] font-semibold text-amber-700 tabular-nums";
@endphp

<div class="group flex items-center gap-2">
    @if (count($children))
        <button type="button" data-tree-toggle="cat-{{ $node['id'] }}"
                class="grid size-6 shrink-0 place-items-center rounded text-ink-400 transition hover:bg-ink-200/70 hover:text-ink-700"
                aria-label="Toggle sub-classifications">
            <x-icon name="chevron-right" data-tree-chevron class="size-3.5 transition-transform duration-150" />
        </button>
    @else
        <span class="size-6 shrink-0"></span>
    @endif

    <span @class([
        'grid size-8 shrink-0 place-items-center rounded-lg',
        'bg-brand-100 text-brand-700' => ($node['depth'] ?? 0) === 0,
        'bg-ink-100 text-ink-500' => ($node['depth'] ?? 0) > 0,
    ])>
        <x-icon :name="($node['depth'] ?? 0) === 0 ? 'tag' : 'folder'" class="size-4" />
    </span>

    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
            <p class="truncate text-sm font-semibold text-ink-900">{{ $node['name'] }}</p>
            @unless ($node['is_active'])
                <span class="badge bg-ink-100 text-ink-500 ring-ink-500/10">Hidden</span>
            @endunless
        </div>
        <p class="truncate font-mono text-[0.6875rem] text-ink-400">/{{ $node['slug'] }}</p>
    </div>

    <span class="{{ $badge($node['total_products_count'] ?? 0) }} hidden shrink-0 sm:inline" title="Items in this branch">
        {{ $node['total_products_count'] ?? 0 }} items
    </span>

    <div class="flex shrink-0 items-center gap-0.5 opacity-100 transition sm:opacity-0 sm:group-hover:opacity-100 sm:group-focus-within:opacity-100">
        <a href="{{ route('catalog.show', $node['id']) }}" class="btn-icon" title="View in catalogue" target="_blank" rel="noopener">
            <x-icon name="eye" class="size-4" />
        </a>
        <a href="{{ route('admin.categories.create', ['parent' => $node['id']]) }}" class="btn-icon" title="Add sub-classification">
            <x-icon name="plus" class="size-4" />
        </a>
        <a href="{{ route('admin.categories.edit', $node['id']) }}" class="btn-icon" title="Edit">
            <x-icon name="pencil" class="size-4" />
        </a>
        <x-delete-button name="" icon="trash" label="Delete {{ $node['name'] }}"
                        :action="route('admin.categories.destroy', $node['id'])"
                        :confirm="'Delete '.$node['name'].'? Sub-classifications will move to the top level.'"
                        class="btn-icon text-rose-600 hover:bg-rose-50 hover:text-rose-700" />
    </div>
</div>

@if (count($children))
    <ul id="cat-{{ $node['id'] }}" class="mt-1.5 ms-8 space-y-1.5 border-s border-ink-200 ps-3">
        @foreach ($children as $child)
            <li>
                <x-admin.category-node :node="$child" />
            </li>
        @endforeach
    </ul>
@endif
