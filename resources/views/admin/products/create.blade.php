@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Goods', 'url' => route('admin.products.index')],
        ['label' => 'Add goods'],
    ]" />
@endsection

<x-layouts.app title="Add goods" heading="Add goods" description="Create a new catalogue item">
    @include('admin.products._form', ['mode' => 'create'])
</x-layouts.app>
