<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageCatalog() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:2000'],
            'tone' => ['required', Rule::in(['brand', 'info', 'warning', 'danger'])],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'is_active' => ['nullable', 'boolean'],
            'show_on_shop' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'starts_on' => 'start date',
            'ends_on' => 'end date',
            'is_active' => 'published state',
            'show_on_shop' => 'shopper visibility',
        ];
    }

    public function messages(): array
    {
        return [
            'ends_on.after_or_equal' => 'The end date must be on or after the start date.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'show_on_shop' => $this->boolean('show_on_shop'),
        ]);
    }
}
