@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Notices'],
    ]" />
@endsection

@php
    // Only an administrator or manager may publish. Staff can read the list,
    // so the send controls are not offered to them at all.
    $canSend = auth()->user()?->canManageCatalog() ?? false;
@endphp

<x-layouts.app title="Notices" heading="Notices" description="Announcements shown to shoppers">
    @if ($canSend)
        <x-slot:actions>
            <a href="{{ route('admin.notices.create') }}" class="btn btn-primary btn-sm">
                <x-icon name="plus" class="size-4" /> New notice
            </a>
        </x-slot:actions>
    @endif

    <div class="space-y-4">
        <section class="grid gap-3 sm:grid-cols-2">
            <x-stat-card label="Notices" :value="number_format($notices->total())" icon="bell" tone="brand" />
            <x-stat-card label="Live now" :value="number_format($live)" icon="check" tone="emerald" hint="published and in date" />
        </section>

        <div class="card overflow-hidden">
            @if ($notices->isEmpty())
                <x-empty-state icon="bell" title="No notices yet"
                              description="Publish an announcement and it appears in the bell at the top of every shopper page.">
                    @if ($canSend)
                        <x-slot:action>
                            <a href="{{ route('admin.notices.create') }}" class="btn btn-primary btn-sm">Write the first notice</a>
                        </x-slot:action>
                    @endif
                </x-empty-state>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Notice</th>
                                <th>Tone</th>
                                <th>Window</th>
                                <th>State</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notices as $notice)
                                <tr>
                                    <td>
                                        <p class="text-sm font-semibold text-ink-900">{{ $notice->title }}</p>
                                        <p class="mt-0.5 line-clamp-1 text-xs text-ink-500">{{ $notice->body }}</p>
                                        @if ($notice->author)
                                            <p class="mt-0.5 text-[0.6875rem] text-ink-400">{{ $notice->author->name }}</p>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $notice->toneClasses() }}">{{ ucfirst($notice->tone) }}</span>
                                    </td>
                                    <td class="text-xs text-ink-500 tabular-nums">
                                        {{ $notice->starts_on?->format('j M') ?? 'always' }}
                                        –
                                        {{ $notice->ends_on?->format('j M') ?? 'always' }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $notice->isLive() ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-ink-100 text-ink-500 ring-ink-500/10' }}">
                                            {{ $notice->isLive() ? 'Live' : 'Hidden' }}
                                        </span>
                                        @unless ($notice->show_on_shop)
                                            <span class="badge mt-1 bg-ink-100 text-ink-500 ring-ink-500/10">Internal</span>
                                        @endunless
                                    </td>
                                    <td class="text-end">
                                        @if ($canSend)
                                            <div class="flex items-center justify-end gap-0.5">
                                                <a href="{{ route('admin.notices.edit', $notice) }}" class="btn-icon" title="Edit">
                                                    <x-icon name="pencil" class="size-4" />
                                                </a>
                                                <x-delete-button name="Delete" icon="trash" label="Delete {{ $notice->title }}"
                                                                :action="route('admin.notices.destroy', $notice)"
                                                                :confirm="'Delete '.$notice->title.'? This cannot be undone.'" />
                                            </div>
                                        @else
                                            <span class="text-xs text-ink-400">Read only</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-ink-100 p-4">
                    {{ $notices->links('pagination::tailwind-simple') }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
