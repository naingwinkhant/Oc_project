@props(['user', 'mode' => 'create'])

@php $isEdit = $mode === 'edit'; @endphp

<form method="POST" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}" class="grid gap-5 lg:grid-cols-3">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="space-y-5 lg:col-span-2">
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Account details</h2>
            </div>
            <div class="card-body grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-form-field field="username" label="Username" :value="$user->username" required
                                  placeholder="jennac"
                                  hint="Lowercase letters, numbers, dots, dashes and underscores." />
                </div>

                <x-form-field field="name" label="Full name" :value="$user->name" required placeholder="Jenna Cruz" />

                <x-form-field field="phone" label="Phone" :value="$user->phone" placeholder="09 380 000 03" />

                <div class="sm:col-span-2">
                    <x-form-field field="email" label="Email address" type="email" :value="$user->email" required />
                </div>

                <x-form-field field="password" :label="$isEdit ? 'New password' : 'Password'" type="password"
                              :required="! $isEdit"
                              :hint="$isEdit ? 'Leave blank to keep the current password.' : null" />

                <x-form-field field="password_confirmation" :label="$isEdit ? 'Confirm new password' : 'Confirm password'"
                              type="password" :required="! $isEdit" />
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Role &amp; access</h2>
            </div>
            <div class="card-body space-y-3">
                <x-form-field field="role" label="Role" type="select" required>
                    @foreach (\App\Enums\Role::cases() as $role)
                        <option value="{{ $role->value }}" @selected(old('role', $user->role?->value ?? 'staff') === $role->value)>
                            {{ $role->label() }}
                        </option>
                    @endforeach
                </x-form-field>

                <p class="rounded-lg bg-ink-50 p-3 text-xs leading-relaxed text-ink-600">
                    {{ \App\Enums\Role::from(old('role', $user->role?->value ?? 'staff'))->description() }}
                </p>

                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-ink-200 p-3 transition hover:bg-ink-50">
                    <input type="checkbox" name="is_active" value="1"
                           @checked(old('is_active', $user->is_active ?? true)) class="checkbox mt-0.5">
                    <span>
                        <span class="block text-sm font-semibold text-ink-900">Account is active</span>
                        <span class="block text-xs text-ink-500">Disabled accounts cannot sign in.</span>
                    </span>
                </label>
            </div>
        </section>
    </div>

    <div class="space-y-5">
        <div class="flex flex-col gap-2">
            <button type="submit" class="btn btn-primary btn-lg w-full">
                <x-icon name="check" class="size-4" />
                {{ $isEdit ? 'Save changes' : 'Create user' }}
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary w-full">Cancel</a>
        </div>
    </div>
</form>
