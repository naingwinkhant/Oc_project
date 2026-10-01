<x-layouts.app title="Edit notice" :heading="$notice->title" description="Update the announcement">
    @include('admin.notices._form', ['mode' => 'edit'])
</x-layouts.app>
