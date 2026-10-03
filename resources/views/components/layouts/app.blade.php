@props([
    'title' => null,
    'heading' => null,
    'description' => null,
    'breadcrumbs' => [],
    'actions' => null,
    'wide' => false,
])

@php
    $user = auth()->user();
    $nav = [
        [
            'label' => 'Overview',
            'items' => [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'permission' => null],
                ['route' => 'history.index', 'label' => 'My history', 'icon' => 'clock', 'permission' => null],
            ],
        ],
        [
            'label' => 'Catalogue',
            'items' => [
                ['route' => 'admin.products.index', 'label' => 'Goods', 'icon' => 'box', 'permission' => null],
                ['route' => 'admin.categories.index', 'label' => 'Classifications', 'icon' => 'layers', 'permission' => 'catalog'],
                ['route' => 'admin.suppliers.index', 'label' => 'Suppliers', 'icon' => 'truck', 'permission' => 'catalog'],
            ],
        ],
        [
            'label' => 'Operations',
            'items' => [
                ['route' => 'admin.orders.index', 'label' => 'Orders', 'icon' => 'clipboard', 'permission' => null, 'pill' => 'orders'],
                ['route' => 'admin.notices.index', 'label' => 'Notices', 'icon' => 'bell', 'permission' => null],
                ['route' => 'admin.stock.index', 'label' => 'Stock movements', 'icon' => 'clipboard', 'permission' => null],
                ['route' => 'admin.stock.low', 'label' => 'Low stock alerts', 'icon' => 'alert', 'permission' => null, 'pill' => 'lowStock'],
            ],
        ],
        [
            'label' => 'Administration',
            'items' => [
['route' => 'admin.users.index', 'label' => 'Users & roles', 'icon' => 'users', 'permission' => 'users'],
                ['route' => 'admin.approvals.index', 'label' => 'New accounts', 'icon' => 'users', 'permission' => 'catalog', 'pill' => 'approvals'],
                ['route' => 'admin.advertisements.index', 'label' => 'Advertisements', 'icon' => 'sparkles', 'permission' => 'catalog'],
                ['route' => 'admin.activity.index', 'label' => 'Activity log', 'icon' => 'clock', 'permission' => 'catalog'],
            ],
        ],
    ];

$lowStockCount = \App\Models\Product::query()->lowStock()->count();
    // Orders this person has not cleared from the bell yet. The bell asks for the
    // same list, so the service only runs the query once per request.
    $openOrderCount = app(\App\Notifications\TeamAlertService::class)->unreadCount();
    // Registrations waiting for somebody to accept or turn them down.
    $pendingAccounts = $user?->canManageCatalog()
        ? \App\Models\User::query()->where('status', \App\Enums\AccountStatus::Pending)->count()
        : 0;
    $notifications = app(\App\Notifications\NotificationService::class);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    {{-- Applied before first paint, so night mode never flashes a white page. --}}
    <x-theme-script />
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
<div class="flex min-h-full">

    <div id="sidebar-overlay" data-overlay-lock="true" data-toggle="sidebar-overlay!" class="fixed inset-0 z-40 hidden bg-ink-950/50 backdrop-blur-sm lg:hidden"></div>

    <aside id="sidebar" data-overlay-lock="true"
           class="fixed inset-y-0 left-0 z-50 hidden w-72 flex-col border-r border-ink-200 bg-surface lg:flex">
        <div class="flex h-16 shrink-0 items-center gap-2.5 border-b border-ink-200 px-5">
            <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-brand-600 text-white shadow-raise">
                <x-icon name="store" class="size-5" />
            </span>
            <div class="min-w-0">
                <p class="truncate text-sm font-bold tracking-tight text-ink-900">Goods Hub</p>
                <p class="truncate text-[0.6875rem] text-ink-500">Inventory control</p>
            </div>
            <button type="button" class="btn-icon ms-auto lg:hidden" data-toggle="sidebar,sidebar-overlay!" aria-label="Close menu">
                <x-icon name="x" />
            </button>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
            @foreach ($nav as $group)
                @php
                    $visible = collect($group['items'])->filter(function ($item) use ($user) {
                        return $item['permission'] === 'users' ? $user?->canManageUsers()
                            : ($item['permission'] === 'catalog' ? $user?->canManageCatalog() : true);
                    });
                @endphp

                @if ($visible->isNotEmpty())
                    <div>
                        <p class="section-title mb-2 px-3">{{ $group['label'] }}</p>
                        <ul class="space-y-0.5">
                            @foreach ($visible as $item)
                                @php $active = request()->routeIs($item['route']); @endphp
                                <li>
                                    <a href="{{ route($item['route']) }}" @class(['nav-link', 'nav-link-active' => $active])
                                       @if ($active) aria-current="page" @endif>
                                        <x-icon :name="$item['icon']" class="size-[1.125rem] shrink-0" />
                                        <span class="truncate">{{ $item['label'] }}</span>
@if (($item['pill'] ?? null) === 'lowStock' && $lowStockCount > 0)
                                            <span class="ms-auto rounded-full bg-rose-100 px-1.5 py-0.5 text-[0.625rem] font-bold text-rose-700 tabular-nums">
                                                {{ $lowStockCount > 99 ? '99+' : $lowStockCount }}
                                            </span>
                                        @endif
                                        {{-- Orders the till has not dealt with yet. Never
                                             counts past 99, so the badge keeps its width. --}}
                                        @if (($item['pill'] ?? null) === 'orders' && $openOrderCount > 0)
                                            <span data-order-pill
                                                  class="ms-auto rounded-full bg-rose-100 px-1.5 py-0.5 text-[0.625rem] font-bold text-rose-700 tabular-nums">
                                                {{ $openOrderCount > 99 ? '99+' : $openOrderCount }}
                                            </span>
                                        @endif
                                        {{-- Registrations waiting to be accepted. --}}
                                        @if (($item['pill'] ?? null) === 'approvals' && $pendingAccounts > 0)
                                            <span data-approval-pill
                                                  class="ms-auto rounded-full bg-violet-100 px-1.5 py-0.5 text-[0.625rem] font-bold text-violet-700 tabular-nums">
                                                {{ $pendingAccounts > 99 ? '99+' : $pendingAccounts }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endforeach
        </nav>

        <div class="shrink-0 border-t border-ink-200 p-3">
            <a href="{{ route('catalog.index') }}" class="nav-link mb-2">
                <x-icon name="cart" class="size-[1.125rem] shrink-0" />
                <span>Public catalogue</span>
                <x-icon name="arrow-right" class="ms-auto size-4 opacity-50" />
            </a>
            <div class="flex items-center gap-3 rounded-lg bg-ink-50 p-2.5">
                @if ($user?->avatarUrl())
                    <img src="{{ $user->avatarUrl() }}" alt="" class="size-9 shrink-0 rounded-full object-cover ring-2 ring-white">
                @else
                    <span class="grid size-9 shrink-0 place-items-center rounded-full bg-brand-600 text-xs font-bold text-white">
                        {{ $user?->initials() ?? '—' }}
                    </span>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold text-ink-900">{{ $user?->name }}</p>
                    <p class="truncate text-[0.6875rem] text-ink-500">{{ $user?->role->label() }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-icon" title="Sign out" aria-label="Sign out">
                        <x-icon name="logout" class="size-[1.125rem]" />
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col lg:ps-72">
        <header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-3 border-b border-ink-200 bg-surface/85 px-4 backdrop-blur-md sm:px-6">
            <button type="button" class="btn-icon lg:hidden" data-toggle="sidebar,sidebar-overlay" aria-label="Open menu">
                <x-icon name="menu" />
            </button>

            <div class="min-w-0 flex-1">
                <h1 class="truncate text-base font-bold tracking-tight text-ink-900 sm:text-lg">
                    {{ $heading ?? $title ?? 'Dashboard' }}
                </h1>
                @if ($description)
                    <p class="truncate text-xs text-ink-500 sm:text-sm">{{ $description }}</p>
                @endif
            </div>

            <form action="{{ route('admin.products.index') }}" method="GET" role="search" class="hidden md:block md:w-72 lg:w-80">
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search goods, SKU, barcode…"
                           data-live-search
                           class="input input-sm ps-9" aria-label="Search goods">
                </div>
            </form>

            @if ($actions)
                <div class="flex shrink-0 items-center gap-2">{!! $actions !!}</div>
            @endif

            {{-- Same bell as the shop, so an announcement reaches staff too. --}}
            <x-notifications :team="true" />
        </header>

        <main class="flex-1 px-4 py-5 sm:px-6 sm:py-6">
            @hasSection('breadcrumb')
                <div class="mb-4">
                    @yield('breadcrumb')
                </div>
            @endif

            <x-flash />
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
