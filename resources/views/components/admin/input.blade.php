@props([
    'name',
    'label',
    'value' => null,
    'type' => 'text',
    'hint' => null,
    'required' => false,
    'placeholder' => null,
])

<div>
    <label for="{{ $name }}" class="field-label">
        {{ $label }}
        @if ($required)<span class="text-accent-400">*</span>@endif
    </label>

    <input type="{{ $type }}"
           id="{{ $name }}"
           name="{{ $name }}"
           value="{{ old($name, $value) }}"
           @if ($required) required @endif
           @if ($placeholder) placeholder="{{ $placeholder }}" @endif
           {{ $attributes->class(['field', 'border-red-400/50' => $errors->has($name)]) }}>

    @error($name)
        <p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>
    @else
        @if ($hint)
            <p class="mt-1.5 text-xs text-muted">{{ $hint }}</p>
        @endif
    @enderror
</div>
