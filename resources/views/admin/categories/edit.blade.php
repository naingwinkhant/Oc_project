@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Classifications', 'url' => route('admin.categories.index')],
        ['label' => $category->name],
    ]" />
@endsection

<x-layouts.app title="Edit classification" :heading="$category->name" description="Update classification details and nesting">
    <x-slot:actions>
        <a href="{{ route('admin.categories.create', ['parent' => $category->id]) }}" class="btn btn-secondary btn-sm">
            <x-icon name="plus" class="size-4" />
            <span class="hidden sm:inline">Add sub</span>
        </a>
    </x-slot:actions>

    <div class="mb-5 flex flex-wrap items-center gap-2">
        <span class="badge bg-ink-100 text-ink-600 ring-ink-500/10">Level {{ ($category->depth ?? 0) + 1 }}</span>
        @if ($category->parent)
            <span class="badge bg-ink-100 text-ink-600 ring-ink-500/10">Under {{ $category->parent->name }}</span>
        @else
            <span class="badge bg-brand-50 text-brand-700 ring-brand-600/15">Top level</span>
        @endif
        <span class="text-xs text-ink-400">{{ $category->products()->count() }} direct items</span>
    </div>

    @include('admin.categories._form', ['mode' => 'edit'])
</x-layouts.app>
