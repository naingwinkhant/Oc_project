@props([
    'title' => null,
    'description' => null,
])

@php
    $user = auth()->user();
    $roots = \App\Models\Category::query()->active()->roots()->with(['children' => fn ($q) => $q->active()->orderBy('position')])->get();
    $cartCount = app(\App\Cart\CartService::class)->count();
    $favouriteCount = app(\App\Cart\FavouriteService::class)->count();
    $notifications = app(\App\Notifications\NotificationService::class);

    // Exactly one navigation entry may look selected, so work out which one.
    $activeCategory = request()->routeIs('catalog.show') ? request()->route('category') : null;
    $activeProduct = request()->routeIs('catalog.product') ? request()->route('product') : null;
    $activeRootSlug = ($activeCategory ?? $activeProduct?->category)?->root()->slug;
    $activeChildSlug = $activeCategory && ! $activeCategory->isRoot() ? $activeCategory->slug : null;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    <meta name="description" content="{{ $description ?? config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Applied before first paint, so night mode never flashes a white page. --}}
    <x-theme-script />
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full">
<div class="flex min-h-full flex-col">

    <header class="sticky top-0 z-40 border-b border-ink-200 bg-surface/90 backdrop-blur-md">
<div class="mx-auto flex h-14 max-w-7xl items-center gap-2 px-4 sm:h-16 sm:gap-3 sm:px-6 lg:px-8">
            <a href="{{ route('catalog.index') }}" class="flex shrink-0 items-center gap-2.5">
                <span class="grid size-9 place-items-center rounded-lg bg-brand-600 text-white shadow-raise">
                    <x-icon name="store" class="size-5" />
                </span>
                <span class="hidden text-sm font-bold tracking-tight text-ink-900 lg:block">{{ config('app.name') }}</span>
            </a>

            {{-- From medium screens up the search shares the top row. On a phone
                 there is not enough width left beside the icons for a field
                 anybody could type into, so below it takes the full width
                 instead of being squeezed to a few unusable characters. --}}
            <form action="{{ route('catalog.index') }}" method="GET" role="search"
                  class="ml-auto hidden min-w-0 flex-1 sm:ml-6 sm:block sm:max-w-md">
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search goods or scan a barcode"
                           data-live-search class="input input-sm ps-9" aria-label="Search goods">
                </div>
            </form>

            <nav class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <x-icon-button icon="heart" :href="route('favourites.index')" :count="$favouriteCount"
                              label="Favourites" :active="request()->routeIs('favourites.*')" />

                <x-icon-button icon="cart" :href="route('cart.index')" :count="$cartCount" label="Cart" />

                {{-- Flips between day and night straight away; the full choice,
                     including "follow the system", lives in settings. --}}
                <button type="button" class="btn-icon" data-theme-toggle
                        title="Switch between day and night" aria-label="Switch between day and night">
                    <x-icon name="sparkles" class="size-4.5 dark:hidden" />
                    <x-icon name="clock" class="hidden size-4.5 dark:block" />
                </button>

                <x-icon-button icon="settings" :href="route('settings')" label="Settings"
                              :active="request()->routeIs('settings')" />

                <x-notifications />

                @auth
                    {{-- Only somebody who is already signed in as the team sees this.
                         Shoppers are never offered a sign-in button: the public
                         site has no customer accounts. --}}
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm shrink-0 px-2.5 sm:px-3"
                       title="Staff dashboard" aria-label="Staff dashboard">
                        <span class="grid size-5 shrink-0 place-items-center">
                            <x-icon name="dashboard" class="size-4.5" />
                        </span>
                        <span class="hidden lg:inline">Dashboard</span>
                    </a>
                @endauth
            </nav>

<button type="button" class="btn-icon shrink-0 md:hidden" data-toggle="mobile-menu"
                    aria-controls="mobile-menu" aria-expanded="false" aria-label="Open classifications">
                <x-icon name="menu" />
            </button>
        </div>

        {{-- On a phone the search gets a row of its own, edge to edge, rather
             than the sliver left over between the logo and the icons. --}}
        <div class="border-t border-ink-200 bg-surface px-4 py-2 sm:hidden">
            <form action="{{ route('catalog.index') }}" method="GET" role="search">
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search goods or scan a barcode"
                           data-live-search class="input input-sm ps-9" aria-label="Search goods">
                </div>
            </form>
        </div>

        <div id="mobile-menu" class="hidden max-h-[70vh] overflow-y-auto border-t border-ink-200 bg-surface px-4 py-3 md:hidden">
            <p class="section-title mb-2">Classifications</p>
            <ul class="space-y-0.5">
                <li>
                    <a href="{{ route('catalog.index') }}"
                       @class(['nav-link', 'nav-link-active' => request()->routeIs('catalog.index')])>
                        All goods
                    </a>
                </li>
                <li>
                    <a href="{{ route('catalog.new-arrivals') }}"
                       @class(['nav-link', 'nav-link-active' => request()->routeIs('catalog.new-arrivals')])>
                        <x-icon name="sparkles" class="size-4" />
                        New arrivals
                    </a>
                </li>
                @foreach ($roots as $root)
                    <li>
                        <a href="{{ route('catalog.show', $root) }}"
                           @class([
                               'nav-link',
                               // Only the deepest match lights up, so the drawer never
                               // marks both a department and its sub-classification.
                               'nav-link-active' => $activeRootSlug === $root->slug && ! $activeChildSlug,
                           ])>
                            <x-icon :name="$root->iconName()" class="size-4 shrink-0" />
                            {{ $root->name }}
                        </a>
                        @if ($root->children->isNotEmpty())
                            <ul class="ms-4 mt-0.5 space-y-0.5 border-s border-ink-200 ps-2">
                                @foreach ($root->children as $child)
                                    <li>
                                        <a href="{{ route('catalog.show', $child) }}"
                                           @class([
                                               'nav-link py-2 text-[0.8125rem]',
                                               'nav-link-active' => $activeChildSlug === $child->slug,
                                           ])>
                                            <x-icon :name="$child->iconName()" class="size-4 shrink-0" />
                                            {{ $child->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <nav class="hidden border-t border-ink-200 bg-surface md:block">
            <div class="mx-auto flex max-w-7xl items-center gap-1 overflow-x-auto px-4 py-1.5 lg:px-8">
                <a href="{{ route('catalog.index') }}"
                   @class(['nav-link !py-1.5 whitespace-nowrap', 'nav-link-active' => request()->routeIs('catalog.index')])>
                    All goods
                </a>
                <a href="{{ route('catalog.new-arrivals') }}"
                   @class([
                       'nav-link !py-1.5 whitespace-nowrap',
                       'nav-link-active' => request()->routeIs('catalog.new-arrivals'),
                   ])>
                    <x-icon name="sparkles" class="size-4" />
                    New arrivals
                </a>
                <span class="mx-1 h-4 w-px shrink-0 bg-ink-200"></span>
                @foreach ($roots as $root)
                    <a href="{{ route('catalog.show', $root) }}"
                       @class(['nav-link !py-1.5 whitespace-nowrap', 'nav-link-active' => $activeRootSlug === $root->slug])>
                        {{ $root->name }}
                    </a>
                @endforeach
            </div>
        </nav>
    </header>

    <main class="flex-1">
        <x-flash />
        {{ $slot }}
    </main>

    <footer class="mt-16 border-t border-ink-200 bg-surface">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-4 lg:px-8">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2.5">
                    <span class="grid size-9 place-items-center rounded-lg bg-brand-600 text-white">
                        <x-icon name="store" class="size-5" />
                    </span>
                    <span class="text-sm font-bold tracking-tight text-ink-900">{{ config('app.name') }}</span>
                </div>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-ink-500">
                    Every item on the shelf, classified. Browse the full catalogue, check availability and keep
                    stock accurate in real time.
                </p>
            </div>

            <div>
                <p class="section-title mb-3">Classifications</p>
                <ul class="space-y-2 text-sm">
                    @foreach ($roots->take(5) as $root)
                        <li><a href="{{ route('catalog.show', $root) }}" class="text-ink-600 hover:text-brand-700">{{ $root->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <p class="section-title mb-3">Store</p>
                <ul class="space-y-2 text-sm">
                    {{-- Services and Information live on the settings page rather
                         than in the nav bar. --}}
                    <li><a href="{{ route('settings') }}" class="text-ink-600 hover:text-brand-700">Settings, services &amp; information</a></li>
                </ul>
            </div>

            <div>
                <p class="section-title mb-3">Team</p>
                <ul class="space-y-2 text-sm">
                    {{-- The staff sign-in door, but never a customer one. --}}
                    <li><a href="{{ route('staff.login.php') }}" class="text-ink-600 hover:text-brand-700">Staff sign in</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-ink-200 px-4 py-5 text-center text-xs text-ink-400 sm:px-6 lg:px-8">
            &copy; {{ now()->year }} {{ config('app.name') }}. All rights reserved.
        </div>
    </footer>
</div>
</body>
</html>
