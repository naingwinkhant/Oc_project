@props([
    'name',
    'action',
    'method' => 'POST',
    'confirm' => null,
    'label' => null,
    'icon' => null,
    'class' => 'btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50 hover:text-rose-700',
])

<form method="POST" action="{{ $action }}" class="inline"
      @if ($confirm) data-confirm="{{ $confirm }}" @endif>
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif
    <button type="submit" class="{{ $class }}" title="{{ $label ?? $name }}">
        @if ($icon)
            <x-icon :name="$icon" class="size-4" />
        @endif
        {{ $name }}
    </button>
</form>
