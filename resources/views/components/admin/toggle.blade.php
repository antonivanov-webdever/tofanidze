@props(['name', 'label', 'checked' => false, 'hint' => null])

<label class="flex cursor-pointer items-start gap-3 rounded-xl border border-white/10 bg-white/[0.02] p-4
              transition hover:border-white/20">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox"
           name="{{ $name }}"
           value="1"
           @checked(old($name, $checked))
           class="mt-0.5 h-4 w-4 shrink-0 rounded border-white/20 bg-ink-900 text-accent-500 focus:ring-accent-500/40">
    <span>
        <span class="block text-sm font-medium text-white">{{ $label }}</span>
        @if ($hint)
            <span class="mt-0.5 block text-xs text-muted">{{ $hint }}</span>
        @endif
    </span>
</label>
