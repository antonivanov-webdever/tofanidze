@props(['action', 'label' => 'Delete', 'confirm' => 'Delete this item? This cannot be undone.'])

<form method="POST" action="{{ $action }}" class="inline"
      onsubmit="return confirm('{{ $confirm }}')">
    @csrf
    @method('DELETE')
    <button type="submit"
            {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-lg border border-white/10 px-2.5 py-1.5 text-xs font-medium text-muted transition hover:border-red-400/40 hover:text-red-300']) }}>
        <x-icon name="trash" class="h-3.5 w-3.5" />
        {{ $label }}
    </button>
</form>
