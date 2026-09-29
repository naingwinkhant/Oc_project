@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Users', 'url' => route('admin.users.index')],
        ['label' => 'Add user'],
    ]" />
@endsection

<x-layouts.app title="Add user" heading="Add user" description="Create an account for a team member">
    @include('admin.users._form', ['mode' => 'create'])
</x-layouts.app>
