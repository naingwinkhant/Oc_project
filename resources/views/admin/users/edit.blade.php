@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Users', 'url' => route('admin.users.index')],
        ['label' => $user->name],
    ]" />
@endsection

<x-layouts.app title="Edit user" :heading="$user->name" description="Update account, role and access">
    @include('admin.users._form', ['mode' => 'edit'])
</x-layouts.app>
