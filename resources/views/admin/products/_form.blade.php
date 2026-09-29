@props(['product', 'categories', 'mode' => 'create'])

@php $isEdit = $mode === 'edit'; @endphp

<form method="POST" action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}"
      enctype="multipart/form-data" class="grid gap-5 lg:grid-cols-3">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="space-y-5 lg:col-span-2">
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Item details</h2>
            </div>
            <div class="card-body grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-form-field field="name" label="Goods name" :value="$product->name" required
                                  placeholder="Frozen Strawberry 1kg" />
                </div>

                <div>
                    <x-form-field field="sku" label="SKU / internal code" :value="$product->sku"
                                  placeholder="Auto-generated" hint="Leave blank to generate automatically." />
                </div>

                <div>
                    <x-form-field field="barcode" label="Barcode (EAN/UPC)" :value="$product->barcode"
                                  placeholder="4801234567890" />
                </div>

                <div>
                    <x-form-field field="brand" label="Brand" :value="$product->brand" placeholder="Nature's Best" />
                </div>

                <x-form-field field="category_id" label="Classification" type="select">
                    <option value="">Unclassified</option>
                    @foreach ($categories as $id => $name)
                        <option value="{{ $id }}" @selected((int) old('category_id', $product->category_id) === $id)>
                            {{ $name }}
                        </option>
                    @endforeach
                </x-form-field>

                <div class="sm:col-span-2">
                    <x-form-field field="description" label="Description" type="textarea" :value="$product->description"
                                  placeholder="Shelf notes, pack size, storage instructions…" />
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Pricing &amp; stock</h2>
            </div>
            <div class="card-body grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-form-field field="price" label="List price" type="number" step="1" min="0"
                              :value="$product->price ?? 0" required
                              hint="The everyday price. Struck through when a sale price is set." />

                <x-form-field field="sale_price" label="Sale price" type="number" step="1" min="0"
                              :value="$product->sale_price"
                              placeholder="Leave blank if not on promotion"
                              hint="Must be lower than the list price. Shown in blue on the shelf." />

                <x-form-field field="cost_price" label="Cost price" type="number" step="0.01" min="0"
                              :value="$product->cost_price ?? 0" hint="Used for margin." />

                <x-form-field field="unit" label="Unit of measure" :value="$product->unit ?? 'pcs'" required
                              placeholder="pcs, kg, L, pack" />

                <x-form-field field="weight" label="Weight (kg)" type="number" step="0.001" min="0"
                              :value="$product->weight" />

                <x-form-field field="stock" label="Quantity in stock" type="number" min="0"
                              :value="$product->stock ?? 0" />

                <x-form-field field="min_stock" label="Low stock threshold" type="number" min="0"
                              :value="$product->min_stock ?? 5" hint="Alerts you when stock hits this." />
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Production &amp; expiry</h2>
            </div>
            <div class="card-body grid gap-4 sm:grid-cols-2">
                <x-form-field field="produced_at" label="Production date" type="date"
                              :value="$product->produced_at?->format('Y-m-d')"
                              hint="When this batch was produced or packed." />

                <x-form-field field="expires_at" label="Expiry date" type="date"
                              :value="$product->expires_at?->format('Y-m-d')"
                              hint="Expired items are blocked from the cart automatically." />

                <x-form-field field="available_from" label="Available from" type="date"
                              :value="$product->available_from?->format('Y-m-d')"
                              hint="A future date lists the item as coming soon and blocks the cart." />

                <div class="sm:col-span-2" data-shelf-life>
                    <div class="flex flex-wrap items-center gap-2 rounded-lg bg-ink-50 p-3 text-xs text-ink-600">
                        <x-icon name="clock" class="size-3.5 shrink-0 text-ink-400" />
                        <span data-shelf-life-text>
                            @if ($product->shelfLifeDays())
                                Shelf life {{ $product->shelfLifeDays() }} {{ Str::plural('day', $product->shelfLifeDays()) }}.
                            @else
                                Set both dates to see the shelf life.
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="space-y-5">
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Photo</h2>
            </div>
            <div class="card-body space-y-3">
                <div class="grid aspect-4/3 place-items-center overflow-hidden rounded-lg border border-dashed border-ink-300 bg-ink-50">
                    @if ($product->imageUrl())
                        <img src="{{ $product->imageUrl() }}" alt="" data-image-preview class="size-full object-cover">
                    @else
                        <span data-image-placeholder class="flex flex-col items-center gap-2 text-ink-400">
                            <x-icon name="image" class="size-8" />
                            <span class="text-xs font-medium">JPG, PNG or WEBP · max 2MB</span>
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

        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Visibility</h2>
            </div>
            <div class="card-body space-y-3">
                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-ink-200 p-3 transition hover:bg-ink-50">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))
                           class="checkbox mt-0.5">
                    <span>
                        <span class="block text-sm font-semibold text-ink-900">Visible in catalogue</span>
                        <span class="block text-xs text-ink-500">Uncheck to archive without deleting.</span>
                    </span>
                </label>

                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-ink-200 p-3 transition hover:bg-ink-50">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))
                           class="checkbox mt-0.5">
                    <span>
                        <span class="block text-sm font-semibold text-ink-900">Featured item</span>
                        <span class="block text-xs text-ink-500">Highlighted as a promotion.</span>
                    </span>
                </label>

                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-ink-200 p-3 transition hover:bg-ink-50">
                    <input type="checkbox" name="is_new" value="1" @checked(old('is_new', $product->is_new ?? false))
                           class="checkbox mt-0.5">
                    <span>
                        <span class="block text-sm font-semibold text-ink-900">New arrival</span>
                        <span class="block text-xs text-ink-500">Shows in the New arrivals section on the catalogue.</span>
                    </span>
                </label>

                @if ($isEdit)
                    <div class="rounded-lg bg-ink-50 p-3">
                        <x-form-field field="slug" label="URL slug" :value="$product->slug"
                                      hint="Leave blank to regenerate from the name." />
                    </div>
                @endif
            </div>
        </section>

        <div class="flex flex-col gap-2">
            <button type="submit" class="btn btn-primary btn-lg w-full">
                <x-icon name="check" class="size-4" />
                {{ $isEdit ? 'Save changes' : 'Add to catalogue' }}
            </button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary w-full">Cancel</a>
        </div>
    </div>
</form>
