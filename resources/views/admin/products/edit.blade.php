@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Goods', 'url' => route('admin.products.index')],
        ['label' => $product->name],
    ]" />
@endsection

<x-layouts.app title="Edit goods" :heading="$product->name" description="Update catalogue details, pricing and stock">
    <x-slot:actions>
        <a href="{{ route('catalog.product', $product) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">
            <x-icon name="eye" class="size-4" />
            <span class="hidden sm:inline">Preview</span>
        </a>
    </x-slot:actions>

    <div class="mb-5 flex flex-wrap items-center gap-2">
        <span class="badge bg-ink-100 text-ink-600 ring-ink-500/10">{{ $product->sku }}</span>
        <span class="badge {{ $product->stockStatusTone() }}">{{ $product->stockStatusLabel() }}</span>
        @if ($product->is_featured)
            <span class="badge bg-amber-50 text-amber-700 ring-amber-600/20">Featured</span>
        @endif
        <span class="text-xs text-ink-400">Added {{ $product->created_at->diffForHumans() }}</span>
    </div>

    @include('admin.products._form', ['mode' => 'edit'])
</x-layouts.app>
