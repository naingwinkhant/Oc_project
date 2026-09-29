@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Suppliers', 'url' => route('admin.suppliers.index')],
        ['label' => 'Add supplier'],
    ]" />
@endsection

<x-layouts.app title="Add supplier" heading="Add supplier" description="Record a vendor you buy stock from">
    @include('admin.suppliers._form', ['mode' => 'create'])
</x-layouts.app>
