@php
    $messages = array_filter([
        'status' => session('status'),
        'success' => session('success'),
        'error' => session('error'),
    ]);
@endphp

@if ($messages)
    <div class="mb-5 space-y-3">
        @foreach ($messages as $type => $message)
            @php
                [$tone, $icon] = match ($type) {
                    'error' => ['bg-rose-50 text-rose-800 ring-rose-600/20', 'alert'],
                    'success' => ['bg-emerald-50 text-emerald-800 ring-emerald-600/20', 'check'],
                    default => ['bg-brand-50 text-brand-800 ring-brand-600/20', 'info'],
                };
            @endphp
            <div id="flash-{{ $type }}" data-flash class="animate-in-up flex items-start gap-3 rounded-lg px-4 py-3 text-sm font-medium ring-1 ring-inset transition-all duration-300 {{ $tone }}">
                <x-icon :name="$icon" class="mt-px size-4 shrink-0" />
                <p class="flex-1">{{ $message }}</p>
                <button type="button" class="-me-1 shrink-0 rounded p-0.5 opacity-60 transition hover:opacity-100" data-dismiss="flash-{{ $type }}" aria-label="Dismiss">
                    <x-icon name="x" class="size-4" />
                </button>
            </div>
        @endforeach
    </div>
@endif
