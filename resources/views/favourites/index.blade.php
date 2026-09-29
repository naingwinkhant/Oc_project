<x-layouts.public title="Favourites" description="The goods you have saved for later">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">Favourites</h1>
                <p class="mt-0.5 text-sm text-ink-500">
                    {{ $products->count() }} {{ Str::plural('item', $products->count()) }} saved for later
                </p>
            </div>

            @if ($products->isNotEmpty())
                <form method="POST" action="{{ route('favourites.clear') }}"
                      onsubmit="return confirm('Clear your whole favourites list?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                        <x-icon name="trash" class="size-4" />
                        Clear all
                    </button>
                </form>
            @endif
        </div>

        @if ($products->isEmpty())
            <div class="card">
                <x-empty-state icon="heart" title="No favourites yet"
                              description="Tap the heart on any goods item to keep it here for your next shop.">
                    <x-slot:action>
                        <a href="{{ route('catalog.index') }}" class="btn btn-primary">
                            <x-icon name="store" class="size-4" /> Browse goods
                        </a>
                    </x-slot:action>
                </x-empty-state>
            </div>
        @else
            <div class="grid gap-3 grid-cards sm:gap-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" :favourited="true" />
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.public>
