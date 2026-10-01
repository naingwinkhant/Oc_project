@props(['notice' => null, 'mode' => 'create'])

@php
    // The same form writes a new notice and edits an existing one. On create
    // there is no record yet, so an unsaved one stands in with the defaults a
    // notice should start life with.
    $notice ??= new \App\Models\Notice([
        'tone' => 'brand',
        'is_active' => true,
        'show_on_shop' => true,
    ]);
@endphp

<form method="POST" action="{{ $mode === 'edit' ? route('admin.notices.update', $notice) : route('admin.notices.store') }}"
      class="grid gap-5 lg:grid-cols-3">
    @csrf
    @if ($mode === 'edit') @method('PUT') @endif

    <div class="space-y-5 lg:col-span-2">
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">The notice</h2>
            </div>
            <div class="card-body grid gap-4">
                <x-form-field field="title" label="Title" :value="$notice->title" required
                              placeholder="Delivery hours changed this week" />

                <x-form-field field="body" label="Message" type="textarea" :value="$notice->body" required
                              placeholder="Tell shoppers what is happening, in one or two sentences."
                              hint="Shown in the bell at the top of every shopper page." />

                <div class="grid gap-4 sm:grid-cols-3">
                    <x-form-field field="starts_on" label="Show from" type="date"
                                  :value="$notice->starts_on?->format('Y-m-d')"
                                  hint="Optional." />

                    <x-form-field field="ends_on" label="Show until" type="date"
                                  :value="$notice->ends_on?->format('Y-m-d')"
                                  hint="Optional." />

                    <x-form-field field="tone" label="Colour" type="select" :value="$notice->tone ?? 'brand'">
                        @foreach (['brand' => 'Store green', 'info' => 'Blue', 'warning' => 'Amber', 'danger' => 'Red'] as $value => $label)
                            <option value="{{ $value }}" @selected(($notice->tone ?? 'brand') === $value)>{{ $label }}</option>
                        @endforeach
                    </x-form-field>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Where it appears</h2>
            </div>
            <div class="card-body grid gap-3 sm:grid-cols-2">
                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-ink-200 p-3 transition hover:bg-ink-50">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                           @checked(old('is_active', $notice->is_active ?? true)) class="checkbox mt-0.5">
                    <span class="text-sm">
                        <span class="block font-semibold text-ink-800">Published</span>
                        <span class="block text-xs text-ink-500">Unpublished notices are kept but hidden.</span>
                    </span>
                </label>

                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-ink-200 p-3 transition hover:bg-ink-50">
                    <input type="checkbox" name="show_on_shop" value="1" id="show_on_shop"
                           @checked(old('show_on_shop', $notice->show_on_shop ?? true)) class="checkbox mt-0.5">
                    <span class="text-sm">
                        <span class="block font-semibold text-ink-800">For shoppers</span>
                        <span class="block text-xs text-ink-500">Off keeps it internal, for the store team only.</span>
                    </span>
                </label>
            </div>
        </section>
    </div>

    <div class="space-y-5">
        @if ($mode === 'edit' && $notice->isLive())
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Preview</h2>
                </div>
                <div class="card-body">
                    <article class="rounded-lg p-3 ring-1 {{ $notice->toneClasses() }}">
                        <h3 class="text-sm font-semibold">{{ $notice->title }}</h3>
                        <p class="mt-1 text-xs leading-relaxed opacity-90">{{ $notice->body }}</p>
                    </article>
                </div>
            </section>
        @endif

        <div class="flex flex-col gap-2">
            <button type="submit" class="btn btn-primary btn-lg w-full">
                <x-icon name="check" class="size-4" />
                {{ $mode === 'edit' ? 'Save changes' : 'Publish notice' }}
            </button>
            <a href="{{ route('admin.notices.index') }}" class="btn btn-secondary w-full">Cancel</a>
        </div>
    </div>
</form>
