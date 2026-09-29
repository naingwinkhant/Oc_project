@props(['node', 'level' => 0])

@php
    $children = $node['children'] ?? [];
    $isActive = request()->routeIs('catalog.show') && request()->route('category')?->id === $node['id'];
    $hasActiveDescendant = $isActive || collect($children)->contains(fn ($c) => $c['path'] && str_starts_with((string) ($c['path'] ?? ''), $node['path'].$node['id'].'/'));
@endphp

<li>
    <div class="group flex items-center gap-1 rounded-lg transition-colors hover:bg-ink-50"
         style="padding-left: {{ $level * 0.75 }}rem">

        @if (count($children))
            <button type="button" data-tree-toggle="branch-{{ $node['id'] }}"
                    class="grid size-6 shrink-0 place-items-center rounded text-ink-400 hover:bg-ink-200/70 hover:text-ink-700"
                    aria-label="Toggle sub-classifications">
                <x-icon name="chevron-right" data-tree-chevron class="size-3.5 transition-transform duration-150" />
            </button>
        @else
            <span class="size-6 shrink-0"></span>
        @endif

        <a href="{{ route('catalog.show', $node['id']) }}"
           @class([
               'flex min-w-0 flex-1 items-center gap-2 rounded-md py-2 pr-2 text-sm transition-colors',
               'font-semibold text-brand-700' => $isActive,
               'text-ink-700 hover:bg-ink-100 hover:text-ink-900' => ! $isActive,
           ])
           @if ($isActive) aria-current="page" @endif>
            <x-icon name="tag" @class(['size-4 shrink-0', 'text-brand-600' => $isActive, 'text-ink-400' => ! $isActive]) />
            <span class="truncate">{{ $node['name'] }}</span>
            <span class="ms-auto shrink-0 rounded-full bg-ink-100 px-1.5 py-0.5 text-[0.625rem] font-semibold text-ink-500 tabular-nums">
                {{ $node['total_products_count'] ?? 0 }}
            </span>
        </a>
    </div>

    @if (count($children))
        <ul id="branch-{{ $node['id'] }}" @class(['ms-4 mt-0.5 space-y-0.5 border-s border-ink-200 ps-1.5', 'hidden' => ! $hasActiveDescendant])>
            @foreach ($children as $child)
                <x-category-tree-node :node="$child" :level="$level + 1" />
            @endforeach
        </ul>
    @endif
</li>
