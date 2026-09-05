@php
    $isEdit = $technology->exists;
@endphp

<x-admin-layout
    :title="$isEdit ? 'Edit technology' : 'New technology'"
    :heading="$isEdit ? $technology->name : 'New technology'"
>
    <x-slot:actions>
        <a href="{{ route('admin.technologies.index') }}" class="btn-ghost !px-4 !py-2 !text-xs">Cancel</a>
    </x-slot:actions>

    <form method="POST"
          action="{{ $isEdit ? route('admin.technologies.update', $technology) : route('admin.technologies.store') }}"
          class="max-w-xl">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <x-admin.card>
            <div class="space-y-5">
                <x-admin.input name="name" label="Name" :value="$technology->name" required placeholder="Laravel" />

                <x-admin.input name="slug" label="Slug" :value="$technology->slug"
                               hint="Used by the project filter. Generated from the name if left empty." />

                <x-admin.select name="category" label="Group" required
                                :value="$technology->category ?: 'backend'"
                                :options="\App\Models\Technology::CATEGORIES" />

                <div x-data="{ color: '{{ old('color', $technology->color ?: '#4f9dff') }}' }">
                    <label for="color" class="field-label">Accent colour</label>
                    <div class="flex items-center gap-3">
                        <input type="color" x-model="color"
                               class="h-11 w-14 cursor-pointer rounded-lg border border-white/10 bg-ink-900 p-1"
                               aria-label="Colour picker">
                        <input type="text" id="color" name="color" x-model="color"
                               @class(['field font-mono', 'border-red-400/50' => $errors->has('color')])
                               placeholder="#4f9dff">
                    </div>
                    @error('color')
                        <p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>
                    @else
                        <p class="mt-1.5 text-xs text-muted">Shown as the dot next to the name.</p>
                    @enderror
                </div>

                <x-admin.input name="sort_order" label="Sort order" type="number" :value="$technology->sort_order ?? 0" />

                <x-admin.toggle name="is_featured" label="Featured" :checked="$technology->is_featured"
                                hint="Featured technologies appear in the home page toolkit grid." />
            </div>

            <div class="mt-6 flex items-center gap-3 border-t border-white/8 pt-5">
                <button type="submit" class="btn-primary !py-2.5 !text-sm">
                    {{ $isEdit ? 'Save changes' : 'Add technology' }}
                </button>
                <a href="{{ route('admin.technologies.index') }}" class="btn-ghost !py-2.5 !text-sm">Cancel</a>
            </div>
        </x-admin.card>
    </form>

    @if ($isEdit)
        <div class="mt-6 max-w-xl">
            <x-admin.delete-button
                :action="route('admin.technologies.destroy', $technology)"
                label="Delete this technology"
                :confirm="'Delete '.$technology->name.'?'"
                class="!px-4 !py-2.5" />
        </div>
    @endif
</x-admin-layout>
