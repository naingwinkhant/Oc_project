<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * An advertisement as a manager fills it in.
 */
class AdvertisementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageCatalog() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'eyebrow' => ['nullable', 'string', 'max:60'],
            'body' => ['nullable', 'string', 'max:300'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'video' => ['nullable', 'file', 'mimes:mp4,webm', 'max:20480'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            // A path, not a whole address: an advertisement that could point
            // anywhere would let a form send shoppers off this site. The
            // negative look-ahead rejects "//host", which is a protocol-
            // relative link to somewhere else and also begins with a slash.
            'link' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^\/(?!\/)[^\s]*$/',
            ],
            'button_label' => ['nullable', 'string', 'max:40'],

            'position' => ['nullable', 'integer', 'min:0', 'max:999'],

            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],

            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'starts_on' => 'start date',
            'ends_on' => 'end date',
            'is_active' => 'live',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);

        // An empty link and an empty button label are the same as leaving them
        // out, rather than storing two blanks to be checked for later.
        foreach (['link', 'button_label', 'eyebrow'] as $field) {
            if (trim((string) $this->input($field)) === '') {
                $this->merge([$field => null]);
            }
        }
    }

    public function messages(): array
    {
        return [
            'link.regex' => 'The link has to be a path on this site, starting with /.',
            'ends_on.after_or_equal' => 'The end date cannot be before the start date.',
            'video.mimes' => 'The video has to be an MP4 or a WebM file.',
        ];
    }
}
