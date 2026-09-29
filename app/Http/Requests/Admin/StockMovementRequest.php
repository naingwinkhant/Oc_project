<?php

namespace App\Http\Requests\Admin;

use App\Enums\StockMovementType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageCatalog() ?? false;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'type' => ['required', Rule::in(array_column(StockMovementType::cases(), 'value'))],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
            'reason' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'product_id' => 'goods item',
            'quantity' => 'quantity',
        ];
    }
}
