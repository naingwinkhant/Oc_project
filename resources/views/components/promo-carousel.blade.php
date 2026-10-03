@props(['slides' => []])

@if (count($slides) > 0)
    <div class="promo-carousel relative h-full min-w-0"
         data-carousel
         data-autoplay="{{ config('shop.promo.autoplay_ms', 6000) }}"
         role="group"
         aria-roledescription="carousel"
         aria-label="Featured promotions">
        {{-- Fills the banner beside it on wide screens, keeps a usable height
             when the layout stacks on a phone. --}}
        <div class="relative h-52 w-full overflow-hidden rounded-xl bg-brand-800 shadow-pop ring-1 ring-white/15 sm:h-60 lg:h-64">
            @foreach ($slides as $index => $slide)
                <div @class([
                    'promo-slide absolute inset-0 transition-opacity duration-500 ease-out',
                    'opacity-100' => $loop->first,
                    'opacity-0' => ! $loop->first,
                ])
                     data-slide
                     aria-hidden="{{ $loop->first ? 'false' : 'true' }}">

@if ($slide['type'] === 'video')
                        <video class="size-full object-cover"
                               data-promo-video
                               muted loop playsinline preload="metadata"
                               @if ($slide['poster'] ?? null)
                                   poster="{{ $slide['poster'] }}"
                               @endif
                            @disabled(! $loop->first)>
                            <source src="{{ $slide['src'] }}" type="video/mp4">
                        </video>
                    @elseif ($slide['src'] ?? null)
                        <img src="{{ $slide['src'] }}"
                             alt="{{ $slide['alt'] ?? $slide['title'] }}"
                             class="size-full object-cover"
                             loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                    @else
                        {{-- An advertisement written in words only. The frame's
                             own gradient is the picture, so nothing is fetched
                             and there is no blank rectangle. --}}
                        <div class="size-full bg-gradient-to-br from-brand-800 via-brand-900 to-brand-950"></div>
                    @endif

                    {{-- Scrim so the caption stays readable on any photograph.
                         Over a worded slide it would only dull the colours, so
                         it is left off there. --}}
                    @unless ($slide['plain'] ?? false)
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-brand-950/90 via-brand-950/25 to-transparent"></div>
                    @endunless

                    <div class="absolute inset-x-0 bottom-0 p-3.5">
                        @if ($slide['eyebrow'] ?? null)
                            <p class="truncate text-[0.5625rem] font-semibold tracking-widest text-brand-200 uppercase">
                                {{ $slide['eyebrow'] }}
                            </p>
                        @endif

                        <p class="mt-0.5 line-clamp-1 text-sm font-bold text-white">
                            {{ $slide['title'] }}
                        </p>

                        <div class="mt-0.5 flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
                            @if ($slide['price'] ?? null)
                                <span class="text-sm font-bold text-white tabular-nums">{{ $slide['price'] }}</span>
                            @endif

                            @if ($slide['was'] ?? null)
                                <s class="text-[0.6875rem] text-brand-200 tabular-nums">{{ $slide['was'] }}</s>
                            @endif

                            @if ($slide['text'] ?? null)
                                <span class="truncate text-[0.6875rem] text-brand-100">{{ $slide['text'] }}</span>
                            @endif
                        </div>

                        @if ($slide['url'] ?? null)
                            <a href="{{ $slide['url'] }}"
                               class="btn btn-lg mt-2.5 border-0 bg-white px-4 py-2 text-sm text-brand-800 hover:bg-white sm:px-5 sm:py-2.5 sm:text-base">
                                {{ $slide['label'] ?? 'View item' }}
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach

            @if (count($slides) > 1)
                {{-- Top corner, so the arrows never sit on top of the caption. --}}
                <div class="absolute end-2 top-2 z-10 flex gap-1">
                    <button type="button" data-carousel-prev
                            class="grid size-7 place-items-center rounded-full bg-brand-950/45 text-white ring-1 ring-white/20 backdrop-blur transition hover:bg-brand-950/70"
                            aria-label="Previous slide">
                        <x-icon name="chevron-left" class="size-3.5" />
                    </button>

                    <button type="button" data-carousel-next
                            class="grid size-7 place-items-center rounded-full bg-brand-950/45 text-white ring-1 ring-white/20 backdrop-blur transition hover:bg-brand-950/70"
                            aria-label="Next slide">
                        <x-icon name="chevron-right" class="size-3.5" />
                    </button>
                </div>

                {{-- Inside the frame: the carousel can then fill the banner height. --}}
                <div class="absolute start-2 top-2 z-10 flex items-center gap-1.5 rounded-full bg-brand-950/30 px-2 py-1.5 backdrop-blur"
                     data-carousel-dots>
                    @foreach ($slides as $index => $slide)
                        <button type="button"
                                data-carousel-dot="{{ $index }}"
                                @class([
                                    'h-1.5 rounded-full transition-all',
                                    'w-6 bg-white' => $loop->first,
                                    'w-1.5 bg-white/40 hover:bg-white/70' => ! $loop->first,
                                ])
                                aria-label="Slide {{ $index + 1 }} of {{ count($slides) }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif
