@props([
    'name',
    'label',
    'options' => [],
    'value' => null,
    'hint' => null,
    'required' => false,
    'placeholder' => null,
])

<div>
    <label for="{{ $name }}" class="field-label">
        {{ $label }}
        @if ($required)<span class="text-accent-400">*</span>@endif
    </label>

    <select id="{{ $name }}"
            name="{{ $name }}"
            @if ($required) required @endif
            {{ $attributes->class(['field', 'border-red-400/50' => $errors->has($name)]) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) old($name, $value) === (string) $optionValue)>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>
    @else
        @if ($hint)
            <p class="mt-1.5 text-xs text-muted">{{ $hint }}</p>
        @endif
    @enderror
</div>
