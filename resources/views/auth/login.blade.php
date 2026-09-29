<x-layouts.guest title="Sign in" eyebrow="Inventory team access">
    <h2 class="mb-1 text-lg font-bold tracking-tight text-ink-900">Sign in to your account</h2>
    <p class="mb-6 text-sm text-ink-500">Use the username issued to you by your store manager.</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="username" class="label">Username <span class="text-rose-500">*</span></label>
            <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus
                   autocomplete="username" autocapitalize="none" spellcheck="false" placeholder="admin"
                   class="input @if ($errors->has('username')) input-error @endif">
            @error('username')
                <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="label">Password <span class="text-rose-500">*</span></label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   placeholder="••••••••"
                   class="input @if ($errors->has('password')) input-error @endif">
            @error('password')
                <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
            @enderror
        </div>

        <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-600">
            <input type="checkbox" name="remember" value="1" class="checkbox">
            Keep me signed in
        </label>

        <button type="submit" class="btn btn-primary btn-lg w-full">
            <x-icon name="logout" class="size-4 rotate-180" />
            Sign in
        </button>
    </form>

    <div class="mt-6 rounded-lg bg-ink-50 p-3.5 text-xs text-ink-500">
        <p class="font-semibold text-ink-700">Demo accounts</p>
        <ul class="mt-2 space-y-1 font-mono">
            <li>admin · password</li>
            <li>manager · password</li>
            <li>staff · password</li>
        </ul>
    </div>

    <p class="mt-5 text-center text-sm text-ink-500">
        No account yet?
        <a href="{{ route('register') }}" class="link">Create a staff account</a>
    </p>
</x-layouts.guest>
