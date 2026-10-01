@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'New accounts'],
    ]" />
@endsection

<x-layouts.app title="New accounts" heading="New accounts"
               description="Registrations waiting to be looked at before they can sign in">
    <div class="space-y-5">
        <section class="grid gap-3 sm:grid-cols-3">
            <x-stat-card label="Waiting" :value="number_format($counts['pending'])" icon="clock" tone="violet"
                         hint="cannot sign in yet" />
            <x-stat-card label="Accepted" :value="number_format($counts['approved'])" icon="check" tone="emerald" />
            <x-stat-card label="Turned down" :value="number_format($counts['rejected'])" icon="x" tone="rose" />
        </section>

        <section class="card overflow-hidden">
            <div class="card-header">
                <h2 class="card-title">Waiting for a decision</h2>
                <span class="text-xs text-ink-400">Oldest first</span>
            </div>

            {{-- What the two buttons actually do, said plainly: one opens the
                 account and one closes it for good. --}}
            <div class="grid gap-px bg-ink-100 sm:grid-cols-2">
                <div class="flex items-start gap-2.5 bg-emerald-50/60 p-3.5">
                    <span class="mt-0.5 grid size-7 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700">
                        <x-icon name="check" class="size-4" />
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-emerald-900">Accept</p>
                        <p class="mt-0.5 text-xs leading-relaxed text-emerald-800">
                            The account becomes a member of staff. They can sign in at the staff door and reach
                            goods, stock and orders. It can be withdrawn later.
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5 bg-rose-50/60 p-3.5">
                    <span class="mt-0.5 grid size-7 shrink-0 place-items-center rounded-full bg-rose-100 text-rose-700">
                        <x-icon name="x" class="size-4" />
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-rose-900">Reject</p>
                        <p class="mt-0.5 text-xs leading-relaxed text-rose-800">
                            The account is closed for good. The person can never sign in, and that email
                            address cannot register again.
                        </p>
                    </div>
                </div>
            </div>

            @if ($pending->isEmpty())
                <x-empty-state icon="check" title="Nothing waiting"
                              description="Every registration has been looked at. New ones will appear here." />
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Person</th>
                                <th class="hidden sm:table-cell">Email</th>
                                <th>Status</th>
                                <th class="hidden text-end lg:table-cell">Registered</th>
                                <th class="text-end">Decision</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pending as $person)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2.5">
                                            <span class="grid size-8 shrink-0 place-items-center rounded-full bg-ink-100 text-[0.6875rem] font-bold text-ink-600">
                                                {{ $person->initials() }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-ink-900">{{ $person->name }}</p>
                                                <p class="text-[0.6875rem] text-ink-400">
                                                    &#64;{{ $person->username }} · {{ $person->role->label() }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell">
                                        <span class="text-xs text-ink-600">{{ $person->email }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $person->status->tone() }}">{{ $person->status->label() }}</span>
                                    </td>
                                    <td class="hidden text-right text-xs text-ink-400 lg:table-cell">
                                        {{ $person->created_at?->diffForHumans() ?? '—' }}
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-end gap-1.5">
                                            <form method="POST" action="{{ route('admin.approvals.accept', $person) }}"
                                                  data-confirm="Accept {{ $person->name }}? They will be able to sign in.">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <x-icon name="check" class="size-3.5" /> Accept
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.approvals.reject', $person) }}"
                                                  data-confirm="Turn down {{ $person->name }}? They will not be able to sign in.">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-secondary btn-sm text-rose-600 hover:bg-rose-50">
                                                    <x-icon name="x" class="size-3.5" /> Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        @if ($decided->isNotEmpty())
            <section class="card overflow-hidden">
                <div class="card-header">
                    <h2 class="card-title">Decided recently</h2>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Person</th>
                                <th>Status</th>
                                <th class="hidden sm:table-cell">Decided by</th>
                                <th class="hidden text-end lg:table-cell">When</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($decided as $person)
                                <tr>
                                    <td>
                                        <p class="text-sm font-semibold text-ink-900">{{ $person->name }}</p>
                                        <p class="text-[0.6875rem] text-ink-400">&#64;{{ $person->username }}</p>
                                    </td>
                                    <td>
                                        <span class="badge {{ $person->status->tone() }}">{{ $person->status->label() }}</span>
                                    </td>
                                    <td class="hidden sm:table-cell text-xs text-ink-600">
                                        {{ $person->decider?->name ?? '—' }}
                                    </td>
                                    <td class="hidden text-right text-xs text-ink-400 lg:table-cell">
                                        {{ $person->decided_at?->diffForHumans() ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </div>
</x-layouts.app>