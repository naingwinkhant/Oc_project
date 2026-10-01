<x-layouts.guest title="Create account" eyebrow="Join the team">
    <p class="mb-6 text-sm leading-relaxed text-ink-500">
        For an administrator, manager or member of staff who does not have an
        account yet. A new account waits to be accepted before anyone can sign in
        with it.
    </p>

    <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="label">Name <span class="text-rose-500">*</span></label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="120" autofocus
                   autocomplete="name" placeholder="Jenna Cruz"
                   class="input @if ($errors->has('name')) input-error @endif">
            @error('name')
                <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="label">Email <span class="text-rose-500">*</span></label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="190"
                   autocomplete="email" placeholder="jenna@goldengate.com.mm"
                   class="input @if ($errors->has('email')) input-error @endif">
            <p class="mt-1.5 text-xs text-ink-400">This is what you sign in with.</p>
            @error('email')
                <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="label">Password <span class="text-rose-500">*</span></label>
            <input id="password" name="password" type="password" required minlength="8"
                   autocomplete="new-password" placeholder="••••••••"
                   class="input @if ($errors->has('password')) input-error @endif">
            <p class="mt-1.5 text-xs text-ink-400">At least 8 characters.</p>
            @error('password')
                <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="label">Confirm password <span class="text-rose-500">*</span></label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   minlength="8" autocomplete="new-password" placeholder="••••••••"
                   class="input @if ($errors->has('password')) input-error @endif">
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-full">
            <x-icon name="user" class="size-4" />
            Create account
        </button>
    </form>

    <p class="mt-5 text-center text-sm text-ink-500">
        Already have one? <a href="{{ route('staff.login.php') }}" class="link">Sign in</a>
    </p>
</x-layouts.guest>