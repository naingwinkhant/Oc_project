<x-layouts.guest title="Choose a password" eyebrow="Set up your account">
    {{-- The form is what the server sends, so it works with JavaScript off. The
         script swaps in the loading mark for a moment and then reveals this. --}}
    <div data-intro-content>
        <p class="mb-6 text-sm leading-relaxed text-ink-500">
            Hello {{ $user->name }}. Pick a password for
            <span class="font-medium text-ink-700">{{ $user->username }}</span>. This is the only
            time you type it.
        </p>

        <form method="POST" action="{{ route('set-password.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="password" class="label">New password <span class="text-rose-500">*</span></label>
                <input id="password" name="password" type="password" required minlength="8" autofocus
                       autocomplete="new-password" placeholder="••••••••"
                       class="input @if ($errors->has('password')) input-error @endif">
                <p class="mt-1.5 text-xs text-ink-400">At least 8 characters.</p>
                @error('password')
                    <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="label">Repeat it <span class="text-rose-500">*</span></label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       minlength="8" autocomplete="new-password" placeholder="••••••••"
                       class="input @if ($errors->has('password')) input-error @endif">
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-full">
                <x-icon name="shield" class="size-4" />
                Set my password
            </button>
        </form>
    </div>

    {{-- Hidden until the script decides to show it, so nothing flashes.
         Two seconds, as asked for. --}}
    <div data-intro-loading data-intro-delay="2000" hidden>
        <x-loading-orbit />
    </div>
</x-layouts.guest>
