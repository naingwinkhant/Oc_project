@props(['category', 'parents', 'mode' => 'create'])

@php
    $isEdit = $mode === 'edit';
    $selectedParent = old('parent_id', $category->parent_id ?? request('parent'));
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
      enctype="multipart/form-data" class="grid gap-5 lg:grid-cols-3">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="space-y-5 lg:col-span-2">
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Classification details</h2>
            </div>
            <div class="card-body grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-form-field field="name" label="Name" :value="$category->name" required
                                  placeholder="Fresh Produce" />
                </div>

                <div class="sm:col-span-2">
                    <x-form-field field="parent_id" label="Parent classification" type="select"
                                  hint="Leave as “Top level” for a main department.">
                        <option value="">Top level department</option>
                        @foreach ($parents as $id => $label)
                            <option value="{{ $id }}" @selected((int) $selectedParent === (int) $id)>{{ $label }}</option>
                        @endforeach
                    </x-form-field>
                </div>

                <div class="sm:col-span-2">
                    <x-form-field field="description" label="Description" type="textarea" :value="$category->description"
                                  placeholder="Where this classification lives on the shop floor." />
                </div>

                <div>
                    <x-form-field field="color" label="Accent colour" type="select" :value="$category->color ?? 'emerald'">
                        @foreach (['emerald', 'amber', 'sky', 'rose', 'violet', 'slate'] as $swatch)
                            <option value="{{ $swatch }}" @selected(($category->color ?? 'emerald') === $swatch)>
                                {{ ucfirst($swatch) }}
                            </option>
                        @endforeach
                    </x-form-field>
                </div>

                <div>
                    <label for="is_active" class="label">Visibility</label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-ink-200 p-3 transition hover:bg-ink-50">
                        <input type="checkbox" name="is_active" value="1" id="is_active"
                               @checked(old('is_active', $category->is_active ?? true)) class="checkbox">
                        <span class="text-sm font-medium text-ink-700">Visible in catalogue</span>
                    </label>
                </div>
            </div>
        </section>

        @if ($isEdit)
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Goods in this classification</h2>
                    <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" class="btn btn-ghost btn-sm">
                        View all
                        <x-icon name="arrow-right" class="size-4" />
                    </a>
                </div>

                @if ($products->isEmpty())
                    <x-empty-state icon="box" title="No goods here yet"
                                  description="Assign items to this classification from the goods screen." />
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Goods</th>
                                    <th class="text-end">Stock</th>
                                    <th class="text-end">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.products.edit', $item) }}" class="text-sm font-semibold text-ink-900 hover:text-brand-700">
                                                {{ $item->name }}
                                            </a>
                                            <p class="font-mono text-[0.6875rem] text-ink-400">{{ $item->sku }}</p>
                                        </td>
                                        <td class="text-end text-sm tabular-nums">{{ $item->stock }}</td>
                                        <td class="text-end text-sm font-semibold tabular-nums">
                                            {{ number_format((float) $item->price, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        @endif
    </div>

    <div class="space-y-5">
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Photo</h2>
            </div>
            <div class="card-body space-y-3">
                <div class="grid aspect-4/3 place-items-center overflow-hidden rounded-lg border border-dashed border-ink-300 bg-ink-50">
                    @if ($category->imageUrl())
                        <img src="{{ $category->imageUrl() }}" alt="" data-image-preview class="size-full object-cover">
                        <img alt="" data-image-preview class="hidden size-full object-cover">
                    @else
                        <span data-image-placeholder class="flex flex-col items-center gap-2 text-ink-400">
                            <x-icon name="image" class="size-8" />
                            <span class="text-xs font-medium">Optional · max 2MB</span>
                        </span>
                        <img alt="" data-image-preview class="hidden size-full object-cover">
                    @endif
                </div>

                <input type="file" name="image" accept="image/*" data-image-input
                       class="block w-full text-xs text-ink-600 file:me-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                @error('image')
                    <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $message }}</p>
                @enderror
            </div>
        </section>

        <div class="flex flex-col gap-2">
            <button type="submit" class="btn btn-primary btn-lg w-full">
                <x-icon name="check" class="size-4" />
                {{ $isEdit ? 'Save changes' : 'Create classification' }}
            </button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary w-full">Cancel</a>
        </div>
    </div>
</form>
