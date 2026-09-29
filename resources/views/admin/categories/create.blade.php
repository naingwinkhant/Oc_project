@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Classifications', 'url' => route('admin.categories.index')],
        ['label' => 'New classification'],
    ]" />
@endsection

<x-layouts.app title="New classification" heading="New classification" description="Add a department or nest an aisle under an existing one">
    @include('admin.categories._form', ['mode' => 'create'])
</x-layouts.app>
