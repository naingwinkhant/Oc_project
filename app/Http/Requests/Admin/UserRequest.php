<?php

namespace App\Http\Requests\Admin;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageUsers() ?? false;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $id = $user instanceof User ? $user->id : null;

        return [
            'username' => [
                'required',
                'string',
                'min:3',
                'max:40',
                'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($id),
            ],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(Role::values())],
            'is_active' => ['nullable', 'boolean'],
            'password' => [$id ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Usernames may only contain letters, numbers, dots, dashes and underscores.',
        ];
    }

    public function attributes(): array
    {
        return [
            'is_active' => 'active status',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }

    public function after(): array
    {
        return [
            function ($validator) {
                $user = $this->route('user');

                if ($user instanceof User && $user->id === $this->user()->id && $this->input('role') !== Role::Admin->value) {
                    $validator->errors()->add('role', 'You cannot remove your own administrator role.');
                }
            },
        ];
    }
}
