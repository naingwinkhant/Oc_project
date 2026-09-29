<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageCatalog() ?? false;
    }

    public function rules(): array
    {
        $product = $this->route('product');
        $id = $product instanceof Product ? $product->id : null;

        return [
            'category_id' => ['nullable', Rule::exists(Category::class, 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('products', 'slug')->ignore($id)],
            'sku' => ['nullable', 'string', 'max:60', Rule::unique('products', 'sku')->ignore($id)],
            'barcode' => ['nullable', 'string', 'max:60', Rule::unique('products', 'barcode')->ignore($id)],
            'brand' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:5000'],
            'unit' => ['required', 'string', 'max:30'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:99999'],
            'price' => ['required', 'integer', 'min:0', 'max:99999999'],
            'sale_price' => [
                'nullable',
                'integer',
                'min:1',
                'max:99999999',
                'lt:price',
                // A discount below cost quietly loses money on every unit sold.
                Rule::when(
                    filled($this->input('cost_price')),
                    fn () => ['gte:cost_price'],
                ),
            ],
            'cost_price' => ['nullable', 'integer', 'min:0', 'max:99999999'],
            'stock' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'min_stock' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'produced_at' => ['nullable', 'date', 'before_or_equal:today'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:produced_at'],
            'available_from' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_new' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'classification',
            'min_stock' => 'low stock threshold',
            'cost_price' => 'cost price',
            'produced_at' => 'production date',
            'expires_at' => 'expiry date',
            'available_from' => 'available from date',
        ];
    }

    public function messages(): array
    {
        return [
            'produced_at.before_or_equal' => 'The production date cannot be in the future.',
            'expires_at.after_or_equal' => 'The expiry date must be on or after the production date.',
            'sale_price.lt' => 'The sale price must be lower than the list price.',
            'sale_price.gte' => 'The sale price cannot be below the cost price.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'is_featured' => $this->boolean('is_featured'),
            'is_new' => $this->boolean('is_new'),
        ]);
    }
}
