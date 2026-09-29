<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use App\Services\CategoryTreeService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageCatalog() ?? false;
    }

    public function rules(): array
    {
        $category = $this->route('category');
        $id = $category instanceof Category ? $category->id : null;

        return [
            'name' => ['required', 'string', 'max:120'],
            'parent_id' => ['nullable', 'integer', Rule::exists(Category::class, 'id'), Rule::notIn([$id])],
            'description' => ['nullable', 'string', 'max:2000'],
            'color' => ['nullable', 'string', 'max:20'],
            'icon' => ['nullable', 'string', 'max:60'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'parent_id' => 'parent classification',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $parentId = $this->input('parent_id');
                $category = $this->route('category');

                if (! $parentId || ! $category instanceof Category) {
                    return;
                }

                $tree = app(CategoryTreeService::class);

                if ($tree->isDescendant($category->id, (int) $parentId)) {
                    $validator->errors()->add('parent_id', 'A classification cannot be moved inside its own sub-classifications.');
                }
            },
        ];
    }
}
