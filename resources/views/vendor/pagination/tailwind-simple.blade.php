@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex flex-col items-center justify-between gap-3 sm:flex-row">
        <p class="text-xs text-ink-500">
            Showing
            <span class="font-semibold text-ink-800">{{ $paginator->firstItem() }}</span>
            to
            <span class="font-semibold text-ink-800">{{ $paginator->lastItem() }}</span>
            of
            <span class="font-semibold text-ink-800">{{ number_format($paginator->total()) }}</span>
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="btn btn-secondary btn-sm pointer-events-none opacity-40">
                    <x-icon name="chevron-left" class="size-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-secondary btn-sm" aria-label="Previous page">
                    <x-icon name="chevron-left" class="size-4" />
                </a>
            @endif

            @foreach ($elements ?? [] as $element)
                @if (is_string($element))
                    <span class="btn btn-secondary btn-sm pointer-events-none opacity-40">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="btn btn-primary btn-sm tabular-nums">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="btn btn-secondary btn-sm tabular-nums">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-secondary btn-sm" aria-label="Next page">
                    <x-icon name="chevron-right" class="size-4" />
                </a>
            @else
                <span class="btn btn-secondary btn-sm pointer-events-none opacity-40">
                    <x-icon name="chevron-right" class="size-4" />
                </span>
            @endif
        </div>
    </nav>
@endif
