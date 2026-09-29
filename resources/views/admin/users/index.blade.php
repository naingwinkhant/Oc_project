@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Users'],
    ]" />
@endsection

<x-layouts.app title="Users" heading="Users &amp; roles" description="Who can access the inventory system">
    <x-slot:actions>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <x-icon name="plus" class="size-4" />
            <span class="hidden sm:inline">Add user</span>
        </a>
    </x-slot:actions>

    <div class="space-y-4">
        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Total users" :value="number_format($stats['total'])" icon="users" tone="brand" />
            <x-stat-card label="Active" :value="number_format($stats['active'])" icon="check" tone="emerald" />
            <x-stat-card label="Administrators" :value="number_format($stats['admins'])" icon="shield" tone="violet" />
            <x-stat-card label="Disabled" :value="number_format($stats['inactive'])" icon="x" tone="rose" />
        </section>

        <form method="GET" class="card">
            <div class="card-body grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="relative lg:col-span-2">
                    <x-icon name="search" class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ request('q') }}" data-live-search
                           placeholder="Search username, name, email or phone…" class="input ps-9" aria-label="Search users">
                </div>

                <select name="role" class="select" data-autosubmit aria-label="Filter by role">
                    <option value="">All roles</option>
                    @foreach (\App\Enums\Role::cases() as $role)
                        <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
                    @endforeach
                </select>

                <select name="status" class="select" data-autosubmit aria-label="Filter by status">
                    <option value="">Any status</option>
                    <option value="active" @selected(request('status') === 'active')>Active only</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Disabled only</option>
                </select>
            </div>
        </form>

        <section class="card overflow-hidden">
            <div class="card-header">
                <h2 class="card-title">{{ number_format($users->total()) }} users</h2>
            </div>

            @if ($users->isEmpty())
                <x-empty-state icon="users" title="No users found" description="Try a different search or filter." />
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th class="hidden sm:table-cell">Role</th>
                                <th class="hidden md:table-cell">Last sign in</th>
                                <th class="text-end">Status</th>
                                <th class="w-px text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            @if ($user->avatarUrl())
                                                <img src="{{ $user->avatarUrl() }}" alt="" class="size-9 shrink-0 rounded-full object-cover">
                                            @else
                                                <span class="grid size-9 shrink-0 place-items-center rounded-full bg-brand-100 text-xs font-bold text-brand-700">
                                                    {{ $user->initials() }}
                                                </span>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-ink-900">
                                                    {{ $user->name }}
                                                    @if ($user->id === auth()->id())
                                                        <span class="text-xs font-normal text-ink-400">(you)</span>
                                                    @endif
                                                </p>
                                                <p class="truncate font-mono text-[0.6875rem] text-ink-400">
                                                    &#64;{{ $user->username }} · {{ $user->email }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell">
                                        <span class="badge {{ $user->role->color() }}">{{ $user->role->label() }}</span>
                                    </td>
                                    <td class="hidden whitespace-nowrap text-xs text-ink-500 md:table-cell">
                                        {{ $user->last_login_at?->diffForHumans() ?? 'Never' }}
                                    </td>
                                    <td class="text-end">
                                        <span @class([
                                            'badge',
                                            'bg-emerald-50 text-emerald-700 ring-emerald-600/20' => $user->is_active,
                                            'bg-ink-100 text-ink-500 ring-ink-500/10' => ! $user->is_active,
                                        ])>{{ $user->is_active ? 'Active' : 'Disabled' }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="flex items-center justify-end gap-0.5">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn-icon" title="Edit">
                                                <x-icon name="pencil" class="size-4" />
                                            </a>
                                            @if ($user->id !== auth()->id())
                                                <x-delete-button name="" icon="trash" label="Delete {{ $user->name }}"
                                                                :action="route('admin.users.destroy', $user)"
                                                                :confirm="'Delete '.$user->name.'\'s account? They will lose access immediately.'"
                                                                class="btn-icon text-rose-600 hover:bg-rose-50 hover:text-rose-700" />
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-ink-200 px-4 py-3 sm:px-5">
                    {{ $users->links('pagination::tailwind-simple') }}
                </div>
            @endif
        </section>

        <section class="card">
            <div class="card-header">
                <h2 class="card-title">What each role can do</h2>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Permissions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (\App\Enums\Role::cases() as $role)
                            <tr>
                                <td>
                                    <span class="badge {{ $role->color() }}">{{ $role->label() }}</span>
                                </td>
                                <td class="text-sm text-ink-600">{{ $role->description() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>
