@props(['field', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null, 'placeholder' => null, 'rows' => 3])

@php
    $current = old($field, $value);
    $hasError = $errors->has($field);
@endphp

<div>
    <label for="{{ $field }}" class="label">{{ $label }}</label>

    @if ($type === 'textarea')
        <textarea id="{{ $field }}" name="{{ $field }}" rows="{{ $rows }}"
                  @if ($required) required @endif
                  @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                  class="input @if ($hasError) input-error @endif">{{ $current }}</textarea>
    @elseif ($type === 'select')
        <select id="{{ $field }}" name="{{ $field }}"
                @if ($required) required @endif
                class="select @if ($hasError) input-error @endif">
            {{ $slot }}
        </select>
    @else
        <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ $current }}"
               @if ($required) required @endif
               @if ($placeholder) placeholder="{{ $placeholder }}" @endif
               class="input @if ($hasError) input-error @endif">
    @endif

    @if ($hasError)
        <p class="help-error"><x-icon name="alert" class="size-3.5 shrink-0" /> {{ $errors->first($field) }}</p>
    @elseif ($hint)
        <p class="mt-1.5 text-xs text-ink-400">{{ $hint }}</p>
    @endif
</div>
