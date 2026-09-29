@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Suppliers', 'url' => route('admin.suppliers.index')],
        ['label' => $supplier->name],
    ]" />
@endsection

<x-layouts.app title="Edit supplier" :heading="$supplier->name" description="Update supplier contact details">
    @include('admin.suppliers._form', ['mode' => 'edit'])
</x-layouts.app>
