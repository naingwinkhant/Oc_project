@props([
    'label' => 'Getting things ready',
])

{{--
    The loading mark: a ring with the dolphin travelling around it.

    Drawn as inline SVG so it takes the brand colour and needs no image file.
    The rider counter-spins so it stays nose-up instead of turning over with the
    track, and the ring is a little behind it for depth.
--}}
<div class="flex flex-col items-center gap-6" data-loading-mark>
    <div class="relative grid size-40 place-items-center sm:size-48">
        {{-- The ring the dolphin runs along. --}}
        <div class="absolute inset-0 rounded-full border border-dashed border-brand-600/25" aria-hidden="true"></div>
        <div class="absolute inset-3 rounded-full bg-brand-50 dark:bg-brand-500/10" aria-hidden="true"></div>

        {{-- The centre, so the shape reads as an orbit rather than a blob. --}}
        <svg class="relative size-7 text-brand-600 dark:text-brand-300" viewBox="0 0 24 24" fill="none"
             stroke-width="1.6" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.19a3 3 0 0 1-.621 4.72M6.75 18h3.75m-6.75 0h3.75m-3.75 0h1.5"/>
        </svg>

        {{-- The travelling part: one spin moves it round the ring. --}}
        <div class="orbit-track absolute inset-0" aria-hidden="true">
            <div class="orbit-rider absolute -top-2 left-1/2 -translate-x-1/2">
                {{-- Filled rather than outlined: a silhouette still reads at the
                     size it actually appears, where a stroke turns to mush. --}}
                <svg class="size-11 text-brand-600 dark:text-brand-300" viewBox="0 0 100 60" fill="currentColor">
                    <g transform="rotate(-12 50 30)">
                        <path d="M96 24C90 16 78 11 64 11C54 11 46 13 40 17C30 23 22 29 16 34C10 38 6 42 4 44C10 44 16 42 22 39C20 43 18 47 18 50C24 47 30 43 34 39C46 47 62 50 76 46C88 43 96 34 96 24Z" />
                        <path d="M45 14C43 7 41 2 38 0C36 5 34 11 34 15C37 13 41 13 45 14Z" />
                        <path d="M45 39C43 45 41 50 41 53C45 51 49 47 51 43Z" />
                        <circle cx="85" cy="21" r="2.4" class="fill-surface" />
                    </g>
                </svg>
            </div>
        </div>
    </div>

    <div class="text-center">
        <p class="text-sm font-semibold text-ink-900">{{ $label }}</p>
        <p class="mt-1 text-xs text-ink-500">One moment while we get you to your account.</p>
    </div>
</div>