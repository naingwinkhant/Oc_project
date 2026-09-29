@props(['supplier', 'products' => null, 'mode' => 'create'])

@php $isEdit = $mode === 'edit'; @endphp

<form method="POST" action="{{ $isEdit ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store') }}" class="grid gap-5 lg:grid-cols-3">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="space-y-5 lg:col-span-2">
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Supplier details</h2>
            </div>
            <div class="card-body grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-form-field field="name" label="Company name" :value="$supplier->name" required
                                  placeholder="Metro Fresh Distribution" />
                </div>

                <x-form-field field="code" label="Supplier code" :value="$supplier->code"
                              placeholder="Auto-generated" hint="Leave blank to generate one." />

                <x-form-field field="contact_name" label="Contact person" :value="$supplier->contact_name"
                              placeholder="Juan Dela Cruz" />

                <x-form-field field="phone" label="Phone" :value="$supplier->phone" placeholder="+63 917 000 0000" />

                <x-form-field field="email" label="Email" type="email" :value="$supplier->email"
                              placeholder="orders@supplier.com" />

                <div class="sm:col-span-2">
                    <x-form-field field="address" label="Address" type="textarea" :value="$supplier->address" :rows="2"
                                  placeholder="Street, city, region" />
                </div>

                <div>
                    <label class="label">Status</label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-ink-200 p-3 transition hover:bg-ink-50">
                        <input type="checkbox" name="is_active" value="1"
                               @checked(old('is_active', $supplier->is_active ?? true)) class="checkbox">
                        <span class="text-sm font-medium text-ink-700">Active supplier</span>
                    </label>
                </div>
            </div>
        </section>

        @if ($isEdit && $products && $products->isNotEmpty())
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Linked goods</h2>
                </div>
                <ul class="divide-y divide-ink-100">
                    @foreach ($products as $item)
                        <li class="flex items-center justify-between gap-3 px-4 py-2.5 sm:px-5">
                            <span class="truncate text-sm font-medium text-ink-800">{{ $item->name }}</span>
                            <span class="shrink-0 font-mono text-xs text-ink-400">{{ $item->sku }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>

    <div class="space-y-5">
        <div class="flex flex-col gap-2">
            <button type="submit" class="btn btn-primary btn-lg w-full">
                <x-icon name="check" class="size-4" />
                {{ $isEdit ? 'Save changes' : 'Add supplier' }}
            </button>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary w-full">Cancel</a>
        </div>
    </div>
</form>
