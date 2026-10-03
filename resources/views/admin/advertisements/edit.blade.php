@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Advertisements', 'url' => route('admin.advertisements.index')],
        ['label' => $advertisement->title],
    ]" />
@endsection

<x-layouts.app title="Edit advertisement" :heading="$advertisement->title"
               description="Changes show on the catalogue as soon as they are saved">
    {{-- Reused by the create page, so a field added once is added in both. --}}
    <x-advertisement-form :advertisement="$advertisement" mode="edit" />
</x-layouts.app>