@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Activity log'],
    ]" />
@endsection

<x-layouts.app title="Activity log" heading="Activity log" description="A trace of every change in the system">
    <div class="space-y-4">
        <form method="GET" class="card">
            <div class="card-body grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="relative lg:col-span-2">
                    <x-icon name="search" class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ request('q') }}" data-live-search
                           placeholder="Search descriptions…" class="input ps-9" aria-label="Search activity">
                </div>

                <select name="action" class="select" data-autosubmit aria-label="Filter by action">
                    <option value="">All actions</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
                    @endforeach
                </select>

                <select name="user" class="select" data-autosubmit aria-label="Filter by user">
                    <option value="">All users</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <section class="card overflow-hidden">
            <div class="card-header">
                <h2 class="card-title">{{ number_format($logs->total()) }} entries</h2>
                <a href="{{ route('admin.activity.index') }}" class="btn btn-secondary btn-sm">
                    <x-icon name="refresh" class="size-4" /> Reset
                </a>
            </div>

            @if ($logs->isEmpty())
                <x-empty-state icon="clock" title="No activity recorded" description="Actions taken in the system will appear here." />
            @else
                <ul class="divide-y divide-ink-100">
                    @foreach ($logs as $log)
                        <li class="flex items-start gap-3 px-4 py-3 sm:px-5">
                            <span class="badge {{ $log->tone() }} mt-0.5 shrink-0">{{ $log->action }}</span>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-ink-800">{{ $log->description ?? \Illuminate\Support\Str::headline($log->action) }}</p>
                                <p class="mt-0.5 text-[0.6875rem] text-ink-400">
                                    {{ $log->user?->name ?? 'System' }}
                                    @if ($log->user) · {{ $log->user->role->label() }} @endif
                                    · {{ $log->created_at->format('M j, Y · g:i A') }}
                                    @if ($log->subject_type) · {{ $log->subjectLabel() }} #{{ $log->subject_id }} @endif
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="border-t border-ink-200 px-4 py-3 sm:px-5">
                    {{ $logs->links('pagination::tailwind-simple') }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
