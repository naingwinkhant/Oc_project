@props([
    // The storefront bell stays customer-facing; the admin bell adds the team's
    // own alerts, which carry order numbers.
    'team' => false,
])

@php
    // One service for both sources, so the panel, the badge and the empty state
    // only have to work once. An anonymous component does not inherit the
    // layout's variables, so it is resolved here.
    $bell = app(\App\Notifications\BellService::class);
    $items = $bell->items($team);
    $badge = $bell->badge($team);
@endphp

<div class="relative shrink-0" data-notifications>
    <button type="button"
            class="btn-icon relative"
            data-toggle="notifications-panel"
            aria-controls="notifications-panel"
            aria-expanded="false"
            aria-label="{{ $items->isEmpty() ? 'Notifications' : $items->count().' unread notifications' }}">
        <x-icon name="bell" />

        @if ($badge)
            <span data-notification-badge
                  class="absolute -end-0.5 -top-0.5 grid min-w-4 place-items-center rounded-full bg-rose-600 px-1 text-[0.5625rem] font-bold leading-4 text-white tabular-nums ring-2 ring-surface">{{ $badge }}</span>
        @else
            <span data-notification-badge hidden
                  class="absolute -end-0.5 -top-0.5 grid min-w-4 place-items-center rounded-full bg-rose-600 px-1 text-[0.5625rem] font-bold leading-4 text-white tabular-nums ring-2 ring-surface"></span>
        @endif
    </button>

    <div id="notifications-overlay"
         data-overlay-lock="true"
         data-toggle="notifications-panel!"
         class="fixed inset-0 z-40 hidden bg-ink-950/40 backdrop-blur-sm"></div>

    {{-- The panel: a small bar across the top, then the list. --}}
    <div id="notifications-panel"
         data-overlay-lock="true"
         role="dialog"
         aria-label="Notifications"
         class="fixed inset-x-3 top-16 z-50 hidden max-h-[70vh] flex-col overflow-hidden rounded-xl border border-ink-200 bg-surface shadow-2xl sm:inset-x-auto sm:end-4 sm:w-96 lg:end-8">

        <div class="flex shrink-0 items-center gap-2 border-b border-ink-200 bg-ink-50 px-4 py-3 dark:bg-ink-100">
            <x-icon name="bell" class="size-4 shrink-0 text-ink-500" />
            <p class="min-w-0 flex-1 text-sm font-semibold text-ink-900">Notifications</p>

            @if ($items->isNotEmpty())
                <form method="POST" action="{{ route('notifications.dismiss-all') }}" data-notification-clear>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-semibold text-ink-500 transition hover:text-rose-600">
                        Clear all
                    </button>
                </form>
            @endif

            <button type="button" class="btn-icon size-7" data-toggle="notifications-panel!" aria-label="Close notifications">
                <x-icon name="x" class="size-3.5" />
            </button>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto">
            @foreach ($items as $item)
                <article class="flex items-start gap-2.5 border-b border-ink-100 p-3.5 last:border-b-0 dark:border-ink-200"
                         data-notification-item="{{ $item['kind'] }}-{{ $item['id'] }}">
                    <span class="mt-0.5 grid size-7 shrink-0 place-items-center rounded-full {{ $item['tone'] }}">
                        <x-icon :name="$item['icon']" class="size-3.5" />
                    </span>

                    <div class="min-w-0 flex-1">
                        @if ($item['link'])
                            <a href="{{ $item['link'] }}" class="block">
                                <p class="text-sm font-semibold text-ink-900 hover:text-brand-700 dark:hover:text-brand-200">{{ $item['title'] }}</p>
                                <p class="mt-0.5 text-xs leading-relaxed text-ink-600 dark:text-ink-400">{{ $item['body'] }}</p>
                            </a>
                        @else
                            <p class="text-sm font-semibold text-ink-900">{{ $item['title'] }}</p>
                            <p class="mt-0.5 text-xs leading-relaxed text-ink-600 dark:text-ink-400">{{ $item['body'] }}</p>
                        @endif
                    </div>

                    <form method="POST"
                          action="{{ $item['kind'] === 'alert'
                              ? route('team-alerts.dismiss', $item['id'])
                              : route('notifications.dismiss', $item['id']) }}"
                          data-notification-dismiss>
                        @csrf
                        <button type="submit" class="btn-icon size-7 shrink-0" title="Remove this notification"
                                aria-label="Remove {{ $item['title'] }}">
                            <x-icon name="x" class="size-3.5" />
                        </button>
                    </form>
                </article>
            @endforeach

            <div data-notification-empty @class([
                'px-4 py-10 text-center',
                'hidden' => $items->isNotEmpty(),
            ])>
                <span class="mx-auto grid size-12 place-items-center rounded-full bg-ink-100 text-ink-400 dark:bg-ink-200 dark:text-ink-500">
                    <x-icon name="bell" class="size-6" />
                </span>
                <p class="mt-3 text-sm font-semibold text-ink-900">Nothing new</p>
                <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">Store announcements will show up here.</p>
            </div>
        </div>
    </div>
</div>