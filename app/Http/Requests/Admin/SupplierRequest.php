<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageCatalog() ?? false;
    }

    public function rules(): array
    {
        $supplier = $this->route('supplier');
        $id = $supplier?->id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:40', Rule::unique('suppliers', 'code')->ignore($id)],
            'contact_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:190'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
