@props([
    'name',
    'label',
    'value' => null,
    'rows' => 4,
    'hint' => null,
    'required' => false,
    'placeholder' => null,
    'mono' => false,
])

<div>
    <label for="{{ $name }}" class="field-label">
        {{ $label }}
        @if ($required)<span class="text-accent-400">*</span>@endif
    </label>

    <textarea id="{{ $name }}"
              name="{{ $name }}"
              rows="{{ $rows }}"
              @if ($required) required @endif
              @if ($placeholder) placeholder="{{ $placeholder }}" @endif
              {{ $attributes->class([
                  'field resize-y',
                  'font-mono !text-[13px] leading-relaxed' => $mono,
                  'border-red-400/50' => $errors->has($name),
              ]) }}>{{ old($name, $value) }}</textarea>

    @error($name)
        <p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>
    @else
        @if ($hint)
            <p class="mt-1.5 text-xs text-muted">{{ $hint }}</p>
        @endif
    @enderror
</div>
