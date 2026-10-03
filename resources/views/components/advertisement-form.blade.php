@props(['advertisement' => null, 'mode' => 'create'])

@php
    // The same form writes a new advertisement and edits an existing one. On
    // create there is no record yet, so an unsaved one stands in with the
    // defaults a slide should start life with.
    $advertisement ??= new \App\Models\Advertisement([
        'is_active' => true,
        'position' => 0,
    ]);
@endphp

<form method="POST"
      action="{{ $mode === 'edit' ? route('admin.advertisements.update', $advertisement) : route('admin.advertisements.store') }}"
      enctype="multipart/form-data"
      class="grid gap-5 lg:grid-cols-3">
    @csrf
    @if ($mode === 'edit') @method('PUT') @endif

    <div class="space-y-5 lg:col-span-2">
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">What it says</h2>
            </div>
            <div class="card-body grid gap-4">
                <x-form-field field="title" label="Title" :value="$advertisement->title" required
                              placeholder="Two days off, fruit half price" />

                <div class="grid gap-4 sm:grid-cols-3">
                    <x-form-field field="eyebrow" label="Small line above" :value="$advertisement->eyebrow"
                                  placeholder="This week"
                                  hint="Optional." />

                    <x-form-field field="button_label" label="Button text" :value="$advertisement->button_label"
                                  placeholder="See more"
                                  hint="Optional." />
                </div>

                <x-form-field field="body" label="Message" type="textarea" :value="$advertisement->body"
                              placeholder="One sentence a shopper would want to read while the slide is up."
                              hint="Optional. Shown beside the title." />

                <x-form-field field="link" label="Where the button goes" :value="$advertisement->link"
                              placeholder="/catalog/fresh-produce"
                              hint="A path on this site, starting with /. Leave empty for no button." />
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2 class="card-title">What it looks like</h2>
            </div>
            <div class="card-body grid gap-4">
                {{-- Stated up front, because "why is my picture not showing" is
                     the question this form otherwise invites. --}}
                <p class="rounded-lg bg-ink-50 p-3 text-xs leading-relaxed text-ink-600">
                    A picture is optional. Without one the slide is shown over the store's own colours, which
                    reads better than an empty frame. A video replaces the picture entirely.
                </p>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="image" class="label">Picture</label>
                        <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"
                               class="input text-sm">
                        <p class="help">JPEG, PNG or WebP, up to 4&nbsp;MB.</p>
                        @if ($advertisement->hasImage())
                            <img src="{{ $advertisement->imageUrl() }}" alt=""
                                 class="mt-2 size-24 rounded-lg object-cover ring-1 ring-ink-200">
                            <p class="help">Uploading a new picture replaces this one.</p>
                        @endif
                    </div>

                    <div>
                        <label for="video" class="label">Video</label>
                        <input id="video" name="video" type="file" accept="video/mp4,video/webm"
                               class="input text-sm">
                        <p class="help">MP4 or WebM, up to 20&nbsp;MB. Optional.</p>
                        @if ($advertisement->hasVideo())
                            <p class="help">Uploading a new video replaces this one.</p>
                        @endif
                    </div>
                </div>

                <div>
                    <label for="poster" class="label">Video thumbnail</label>
                    <input id="poster" name="poster" type="file" accept="image/jpeg,image/png,image/webp"
                           class="input text-sm">
                    <p class="help">Shown before a shopper presses play. Optional.</p>
                </div>
            </div>
        </section>
    </div>

    <div class="space-y-5">
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Preview</h2>
            </div>
            <div class="card-body">
                {{-- Roughly the slide's own proportions, so what is approved here
                     is what appears above the goods. --}}
                <div class="relative h-40 overflow-hidden rounded-lg bg-gradient-to-t from-brand-950 via-brand-800 to-brand-700 p-3">
                    @if ($advertisement->hasImage())
                        <img src="{{ $advertisement->imageUrl() }}" alt=""
                             class="absolute inset-0 size-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-950/90 via-brand-950/25 to-transparent"></div>
                    @endif

                    <div class="relative flex h-full flex-col justify-end">
                        @if ($advertisement->eyebrow)
                            <p class="text-[0.5rem] font-semibold tracking-widest text-brand-200 uppercase">
                                {{ $advertisement->eyebrow }}
                            </p>
                        @endif

                        <p class="mt-0.5 line-clamp-2 text-sm font-bold text-white">
                            {{ $advertisement->title ?: 'The title goes here' }}
                        </p>

                        @if ($advertisement->body)
                            <p class="mt-0.5 line-clamp-2 text-[0.6875rem] text-brand-100">{{ $advertisement->body }}</p>
                        @endif

                        @if ($advertisement->link)
                            <span class="mt-2 inline-flex w-fit rounded-lg bg-white px-3 py-1 text-[0.6875rem] font-semibold text-brand-800">
                                {{ $advertisement->button_label ?: 'See more' }}
                            </span>
                        @endif
                    </div>
                </div>

                <p class="help mt-2">
                    {{ $advertisement->hasMedia() ? 'A picture or video is set.' : 'Words only, over the store colours.' }}
                </p>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2 class="card-title">When it shows</h2>
            </div>
            <div class="card-body grid gap-4">
                <x-form-field field="position" label="Order" type="number" :value="$advertisement->position ?? 0"
                              hint="Lower numbers come first." />

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-form-field field="starts_on" label="Show from" type="date"
                                  :value="$advertisement->starts_on?->format('Y-m-d')" hint="Optional." />

                    <x-form-field field="ends_on" label="Show until" type="date"
                                  :value="$advertisement->ends_on?->format('Y-m-d')" hint="Optional." />
                </div>

                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-ink-200 p-3 transition hover:bg-ink-50">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                           @checked(old('is_active', $advertisement->is_active ?? true)) class="checkbox mt-0.5">
                    <span class="text-sm">
                        <span class="block font-semibold text-ink-800">Live</span>
                        <span class="block text-xs text-ink-500">Switched off is kept but never shown.</span>
                    </span>
                </label>
            </div>
        </section>

        <div class="flex flex-col gap-2">
            <button type="submit" class="btn btn-primary btn-lg w-full">
                <x-icon name="check" class="size-4" />
                {{ $mode === 'edit' ? 'Save changes' : 'Add advertisement' }}
            </button>
            <a href="{{ route('admin.advertisements.index') }}" class="btn btn-secondary w-full">Cancel</a>
        </div>
    </div>
</form>