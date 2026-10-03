@section('breadcrumb')
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Advertisements'],
    ]" />
@endsection

@php
    // Only an administrator or manager may write advertising. Staff can read the
    // list, so the controls are not offered to them at all.
    $canManage = auth()->user()?->canManageCatalog() ?? false;
@endphp

<x-layouts.app title="Advertisements" heading="Advertisements"
               description="The slides at the top of the catalogue, in the order shoppers see them">
    @if ($canManage)
        <x-slot:actions>
            <a href="{{ route('admin.advertisements.create') }}" class="btn btn-primary btn-sm">
                <x-icon name="plus" class="size-4" /> New advertisement
            </a>
        </x-slot:actions>
    @endif

    <div class="space-y-4">
        <section class="grid gap-3 sm:grid-cols-2">
            <x-stat-card label="Advertisements" :value="number_format($total)" icon="sparkles" tone="brand" />
            <x-stat-card label="Live now" :value="number_format($live)" icon="check" tone="emerald"
                         hint="switched on and in date" />
        </section>

        <div class="card overflow-hidden">
            @if ($advertisements->isEmpty())
                <x-empty-state icon="sparkles" title="No advertisements yet"
                              description="Add one and it becomes a slide at the top of the catalogue. A slide needs no picture: it is shown over the store's own colours.">
                    @if ($canManage)
                        <x-slot:action>
                            <a href="{{ route('admin.advertisements.create') }}" class="btn btn-primary btn-sm">
                                Write the first advertisement
                            </a>
                        </x-slot:action>
                    @endif
                </x-empty-state>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="w-12">Order</th>
                                <th>Advertisement</th>
                                <th class="hidden md:table-cell">Media</th>
                                <th class="hidden sm:table-cell">Showing</th>
                                <th>State</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($advertisements as $advertisement)
                                <tr>
                                    <td class="align-top text-center text-xs font-semibold text-ink-400 tabular-nums">
                                        {{ $advertisement->position }}
                                    </td>

                                    <td>
                                        <p class="text-sm font-semibold text-ink-900">{{ $advertisement->title }}</p>
                                        @if ($advertisement->eyebrow)
                                            <p class="mt-0.5 text-[0.6875rem] font-semibold tracking-widest text-ink-400 uppercase">
                                                {{ $advertisement->eyebrow }}
                                            </p>
                                        @endif
                                        @if ($advertisement->body)
                                            <p class="mt-0.5 line-clamp-1 text-xs text-ink-500">{{ $advertisement->body }}</p>
                                        @endif
                                        @if ($advertisement->link)
                                            <p class="mt-0.5 font-mono text-[0.6875rem] text-ink-400">
                                                {{ $advertisement->button_label ?: 'See more' }} &#8594; {{ $advertisement->link }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="hidden md:table-cell">
                                        @if ($advertisement->hasVideo())
                                            <span class="badge bg-violet-50 text-violet-700 ring-violet-600/20">Video</span>
                                        @elseif ($advertisement->hasImage())
                                            <img src="{{ $advertisement->imageUrl() }}" alt=""
                                                 class="size-12 rounded-md object-cover ring-1 ring-ink-200">
                                        @else
                                            {{-- Words only. The slide is still shown, over the store's
                                                 own colours, so this is a choice rather than a fault. --}}
                                            <span class="badge bg-ink-100 text-ink-500 ring-ink-500/10">Words only</span>
                                        @endif
                                    </td>

                                    <td class="hidden text-xs text-ink-500 sm:table-cell tabular-nums">
                                        {{ $advertisement->windowLabel() }}
                                    </td>

                                    <td>
                                        <span @class([
                                            'badge',
                                            'bg-emerald-50 text-emerald-700 ring-emerald-600/20' => $advertisement->isLive(),
                                            'bg-ink-100 text-ink-500 ring-ink-500/10' => ! $advertisement->isLive(),
                                        ])>
                                            {{ $advertisement->isLive() ? 'Live' : 'Hidden' }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="flex items-center justify-end gap-0.5">
                                            @if ($canManage)
                                                <form method="POST" action="{{ route('admin.advertisements.move', $advertisement) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="direction" value="up">
                                                    <button type="submit" class="btn-icon" title="Move earlier">
                                                        <x-icon name="chevron-left" class="size-4" />
                                                        <span class="sr-only">Move {{ $advertisement->title }} earlier</span>
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.advertisements.move', $advertisement) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="direction" value="down">
                                                    <button type="submit" class="btn-icon" title="Move later">
                                                        <x-icon name="chevron-right" class="size-4" />
                                                        <span class="sr-only">Move {{ $advertisement->title }} later</span>
                                                    </button>
                                                </form>

                                                <a href="{{ route('admin.advertisements.edit', $advertisement) }}" class="btn-icon"
                                                   title="Edit {{ $advertisement->title }}">
                                                    <x-icon name="pencil" class="size-4" />
                                                </a>

                                                <x-delete-button name="Delete" icon="trash"
                                                                label="Delete {{ $advertisement->title }}"
                                                                :action="route('admin.advertisements.destroy', $advertisement)"
                                                                :confirm="'Delete '.$advertisement->title.'? Its picture is removed too, and this cannot be undone.'" />
                                            @else
                                                <span class="text-xs text-ink-400">Read only</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-ink-100 p-4">
                    {{ $advertisements->links('pagination::tailwind-simple') }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>