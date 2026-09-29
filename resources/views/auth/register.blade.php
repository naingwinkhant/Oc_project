<x-layouts.guest title="Create account" eyebrow="Join the team">
    <h2 class="mb-1 text-lg font-bold tracking-tight text-ink-900">Create a staff account</h2>
    <p class="mb-6 text-sm text-ink-500">
        New accounts start with the Staff role. An administrator can promote you later.
    </p>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="username" class="label">Username <span class="text-rose-500">*</span></label>
            <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus
                   autocomplete="username" autocapitalize="none" spellcheck="false" placeholder="jennac"
                   class="input @if ($errors->has('username')) input-error @endif">
            @error('username')
                <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
            @else
                <p class="mt-1.5 text-xs text-ink-400">Letters, numbers, dots, dashes and underscores. 3–40 characters.</p>
            @enderror
        </div>

        <div>
            <label for="name" class="label">Full name <span class="text-rose-500">*</span></label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                   autocomplete="name" placeholder="Jenna Cruz"
                   class="input @if ($errors->has('name')) input-error @endif">
            @error('name')
                <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="label">Email address <span class="text-rose-500">*</span></label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                   autocomplete="email" placeholder="jenna@supermarket.test"
                   class="input @if ($errors->has('email')) input-error @endif">
            @error('email')
                <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="phone" class="label">Mobile number <span class="font-normal text-ink-400">(optional)</span></label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel"
                   placeholder="09 380 000 00" class="input @if ($errors->has('phone')) input-error @endif">
            @error('phone')
                <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="password" class="label">Password <span class="text-rose-500">*</span></label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                       class="input @if ($errors->has('password')) input-error @endif">
                @error('password')
                    <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="label">Confirm password <span class="text-rose-500">*</span></label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       autocomplete="new-password" class="input">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-full">
            <x-icon name="user" class="size-4" />
            Create account
        </button>
    </form>

    <p class="mt-5 text-center text-sm text-ink-500">
        Already registered?
        <a href="{{ route('login') }}" class="link">Sign in instead</a>
    </p>
</x-layouts.guest>
