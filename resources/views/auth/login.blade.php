@php
    // One view serves all three doors. $area is set by the admin and staff
    // controllers; the plain /login falls back to the generic wording.
    $area ??= null;
    $action = $area === 'admin'
        ? route('admin.login.php.store')
        : ($area === 'staff' ? route('staff.login.php.store') : route('login'));
@endphp

<x-layouts.guest :title="$heading ?? 'Sign in'" :eyebrow="$eyebrow ?? 'Inventory team access'">
    {{-- The guest layout already prints $title as the card heading, so only
         the supporting line is added here. --}}
    <p class="mb-6 text-sm text-ink-500">{{ $blurb ?? 'Use the username issued to you by your store manager.' }}</p>

    <form method="POST" action="{{ $action }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="label">Email <span class="text-rose-500">*</span></label>
            {{-- Pre-filled from the last sign-in, so it only has to be typed once. --}}
            <input id="email" name="email" type="email" value="{{ old('email', old('username', $identifier ?? '')) }}"
                   required autofocus autocomplete="email" autocapitalize="none" spellcheck="false"
                   placeholder="{{ $area === 'admin' ? 'admin@goldengate.com.mm' : 'you@goldengate.com.mm' }}"
                   class="input @if ($errors->has('email') || $errors->has('username')) input-error @endif">
            @error('email')
                <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
            @enderror
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
            {{ $area === 'admin' ? 'Sign in as administrator' : ($area === 'staff' ? 'Sign in as staff' : 'Sign in') }}
        </button>
    </form>

    <p class="mt-5 text-center text-sm text-ink-500">
        No account yet?
        <a href="{{ route('register') }}" class="link">Create one</a>
    </p>

    {{-- Only worth offering on the admin door: adding a team member by hand needs
         an administrator already signed in. --}}
    @if ($area === 'admin')
        <p class="mt-3 text-center text-sm text-ink-500">
            Adding someone by hand?
            <a href="{{ route('admin.users.create') }}" class="link">Create an account</a>
        </p>
    @endif

    {{-- The admin door never advertises a staff account: this page only takes an
         administrator. --}}
    @if ($area === 'admin')
        <div class="mt-6 rounded-lg bg-ink-50 p-3.5 text-xs text-ink-500 dark:bg-ink-100">
            <p class="font-semibold text-ink-700 dark:text-ink-200">Administrator accounts</p>
            <p class="mt-1.5 leading-relaxed">
                Only an administrator can sign in here. Staff and managers use the
                <a href="{{ route('staff.login.php') }}" class="link">staff sign-in page</a>.
            </p>
        </div>
    @else
        <div class="mt-6 rounded-lg bg-ink-50 p-3.5 text-xs text-ink-500 dark:bg-ink-100">
            <p class="font-semibold text-ink-700 dark:text-ink-200">Demo accounts</p>
            <ul class="mt-2 space-y-1 font-mono">
                <li>admin · password</li>
                <li>manager · password</li>
                <li>staff · password</li>
            </ul>
        </div>
    @endif

    <p class="mt-5 text-center text-sm text-ink-500">
        This page is for the store team. Shopping needs no account &mdash; browse and order as a guest.
    </p>
</x-layouts.guest>
