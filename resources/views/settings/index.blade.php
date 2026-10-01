<x-layouts.public title="Settings" description="Your appearance, history, notices and store rules">
    <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        {{-- Settings is reached from the header on any page, so back means
             back. It is a real link to the catalogue for anyone without a
             referrer, and for crawlers. --}}
        <a href="{{ route('catalog.index') }}" data-back
           class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-ink-500 transition hover:text-ink-900">
            <x-icon name="arrow-left" class="size-4" /> Back
        </a>

        <h1 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">Settings</h1>
        <p class="mt-0.5 text-sm text-ink-500">How the shop looks, what you have looked at, and the rules we sell by.</p>

        <div class="mt-6 space-y-5">

            {{-- Appearance --}}
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Appearance</h2>
                    <p class="text-xs text-ink-400">Day or night. It follows you to this device and to your account.</p>
                </div>

                <form method="POST" action="{{ route('settings.theme') }}" data-theme-form>
                    @csrf
                    <div class="card-body">
                        <div class="grid gap-3 sm:grid-cols-3" data-theme-options>
                            @foreach ([
                                'light' => ['icon' => 'sparkles', 'label' => 'Day', 'hint' => 'Always light'],
                                'dark' => ['icon' => 'clock', 'label' => 'Night', 'hint' => 'Always dark'],
                                'system' => ['icon' => 'settings', 'label' => 'System', 'hint' => 'Follows this device'],
                            ] as $value => $option)
                                <label @class([
                                    'flex cursor-pointer items-start gap-3 rounded-lg border p-3 transition',
                                    'border-brand-600 bg-brand-50 ring-1 ring-brand-600' => $theme === $value,
                                    'border-ink-200 hover:border-ink-300 hover:bg-ink-50' => $theme !== $value,
                                ])>
                                    <input type="radio" name="theme" value="{{ $value }}" data-theme-choice class="mt-0.5"
                                           @checked($theme === $value)>
                                    <span class="min-w-0">
                                        <span class="flex items-center gap-1.5 text-sm font-semibold text-ink-900">
                                            <x-icon :name="$option['icon']" class="size-4 text-ink-500" />
                                            {{ $option['label'] }}
                                        </span>
                                        <span class="block text-[0.6875rem] text-ink-500">{{ $option['hint'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </form>
            </section>

            {{-- Your own corner of the shop --}}
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Your activity</h2>
                </div>
                <div class="card-body grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('history.index') }}"
                       class="flex items-center gap-3 rounded-lg border border-ink-200 p-3 transition hover:border-ink-300 hover:bg-ink-50">
                        <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-ink-100 text-ink-600">
                            <x-icon name="clock" class="size-5" />
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-ink-900">History</span>
                            <span class="block text-xs text-ink-500">
                                {{ auth()->check() ? 'What you browsed and what you bought' : 'What you have browsed on this device' }}
                            </span>
                        </span>
                        <x-icon name="chevron-right" class="ms-auto size-4 shrink-0 text-ink-300" />
                    </a>

                    <a href="{{ route('favourites.index') }}"
                       class="flex items-center gap-3 rounded-lg border border-ink-200 p-3 transition hover:border-ink-300 hover:bg-ink-50">
                        <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-ink-100 text-ink-600">
                            <x-icon name="heart" class="size-5" />
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-ink-900">Favourites</span>
                            <span class="block text-xs text-ink-500">Items you saved for later</span>
                        </span>
                        <x-icon name="chevron-right" class="ms-auto size-4 shrink-0 text-ink-300" />
                    </a>
                </div>
            </section>

            {{-- Services and Information are reached from here rather than from
                 the nav bar, so the classifications keep the top of the page. --}}
            <section class="card" id="store">
                <div class="card-header">
                    <h2 class="card-title">The store</h2>
                    <p class="text-xs text-ink-400">What we do, and where to find us.</p>
                </div>

                <div class="card-body grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('services') }}"
                       class="flex items-center gap-3 rounded-lg border border-ink-200 p-3 transition hover:border-ink-300 hover:bg-ink-50">
                        <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-ink-100 text-ink-600">
                            <x-icon name="truck" class="size-5" />
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-ink-900">Services</span>
                            <span class="block text-xs text-ink-500">Delivery, payment, freshness and changes</span>
                        </span>
                        <x-icon name="chevron-right" class="ms-auto size-4 shrink-0 text-ink-300" />
                    </a>

                    <a href="{{ route('information') }}"
                       class="flex items-center gap-3 rounded-lg border border-ink-200 p-3 transition hover:border-ink-300 hover:bg-ink-50">
                        <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-ink-100 text-ink-600">
                            <x-icon name="info" class="size-5" />
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-ink-900">Information</span>
                            <span class="block text-xs text-ink-500">About us, branches and opening hours</span>
                        </span>
                        <x-icon name="chevron-right" class="ms-auto size-4 shrink-0 text-ink-300" />
                    </a>
                </div>
            </section>

            {{-- Notices live in the bell in the header and nowhere else, so the
                 settings page only points at them. --}}
            <section class="card" id="notices">
                <div class="card-header">
                    <h2 class="card-title">Notifications</h2>
                </div>
                <div class="card-body flex flex-wrap items-center gap-3">
                    <p class="min-w-0 flex-1 text-sm text-ink-600">
                        Store announcements appear in the bell at the top of every page.
                        @if ($unreadCount > 0)
                            You have <span class="font-semibold text-ink-900">{{ $unreadCount }}</span> waiting.
                        @else
                            You are all caught up.
                        @endif
                    </p>
                    <button type="button" class="btn btn-secondary btn-sm" data-toggle="notifications,notifications-panel">
                        <x-icon name="bell" class="size-4" /> Open notifications
                    </button>
                </div>
            </section>

            {{-- Rules --}}
            <section class="card" id="rules">
                <div class="card-header">
                    <h2 class="card-title">Store rules</h2>
                    <p class="text-xs text-ink-400">The same rules the till actually enforces.</p>
                </div>

                <div class="card-body space-y-5">
                    @foreach ($rules as $rule)
                        <div>
                            <h3 class="text-sm font-semibold text-ink-900">{{ $rule['title'] }}</h3>
                            <ul class="mt-2 space-y-1.5">
                                @foreach ($rule['lines'] as $line)
                                    <li class="flex items-start gap-2 text-xs leading-relaxed text-ink-600">
                                        <x-icon name="check" class="mt-0.5 size-3.5 shrink-0 text-brand-600" />
                                        <span>{{ $line }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</x-layouts.public>
