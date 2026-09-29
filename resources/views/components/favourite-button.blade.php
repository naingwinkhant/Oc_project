@props(['product', 'active' => false, 'size' => 'md'])

<form method="POST" action="{{ route('favourites.toggle') }}">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="return_to" value="{{ request()->fullUrl() }}">

    <button type="submit"
            @class([
                'grid place-items-center rounded-full transition-all duration-150 active:scale-90',
                'size-9' => $size === 'md',
                'size-10' => $size === 'lg',
                $active
                    ? 'bg-rose-600 text-white ring-1 ring-rose-700/30 hover:bg-rose-700'
                    : 'bg-white/85 text-ink-400 ring-1 ring-ink-200 backdrop-blur hover:bg-white hover:text-rose-500',
            ])
            title="{{ $active ? 'Remove from favourites' : 'Save to favourites' }}"
            aria-label="{{ $active ? 'Remove '.$product->name.' from favourites' : 'Save '.$product->name.' to favourites' }}"
            aria-pressed="{{ $active ? 'true' : 'false' }}">
        <x-icon name="heart" @class([
            'size-5 transition-transform duration-150',
            'fill-rose-600' => $active,
            'fill-none' => ! $active,
        ]) />
    </button>
</form>
