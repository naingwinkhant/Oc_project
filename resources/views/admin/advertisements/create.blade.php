@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Advertisements', 'url' => route('admin.advertisements.index')],
        ['label' => 'New advertisement'],
    ]" />
@endsection

<x-layouts.app title="New advertisement" heading="New advertisement"
               description="A slide at the top of the catalogue">
    {{-- Reused by the edit page, so a field added once is added in both. --}}
    <x-advertisement-form :advertisement="$advertisement" mode="create" />
</x-layouts.app>