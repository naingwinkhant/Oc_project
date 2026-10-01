@php
    $messages = array_filter([
        'status' => session('status'),
        'success' => session('success'),
        'error' => session('error'),
    ]);
@endphp

@if ($messages || session('setPasswordLink'))
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

        {{-- Shown once, right after an account is created: the one-time link the
             new person uses to choose their own password. --}}
        @if ($link = session('setPasswordLink'))
            <div id="flash-set-password" class="animate-in-up rounded-lg bg-brand-50 px-4 py-3 text-sm ring-1 ring-inset ring-brand-600/20">
                <p class="flex items-center gap-2 font-semibold text-brand-900">
                    <x-icon name="shield" class="size-4 shrink-0" />
                    Set-password link for the new account
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-brand-800">
                    One use only, and good for an hour. Send it to them; it will not be shown again.
                </p>
                <div class="mt-2.5 flex flex-wrap items-center gap-2">
                    <input type="text" readonly value="{{ $link }}"
                           class="input input-sm min-w-0 flex-1 font-mono text-xs"
                           onclick="this.select()" aria-label="Set-password link">
                    <button type="button" class="btn btn-secondary btn-sm shrink-0" data-copy="{{ $link }}">
                        <x-icon name="clipboard" class="size-4" /> <span data-copy-label>Copy</span>
                    </button>
                </div>
            </div>
        @endif
    </div>
@endif
