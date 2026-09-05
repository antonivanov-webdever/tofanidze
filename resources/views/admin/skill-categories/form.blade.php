@php
    $isEdit = $category->exists;
    $icons = ['server', 'layout', 'database', 'plug', 'code', 'layers', 'target', 'zap', 'grid'];
@endphp

<x-admin-layout
    :title="$isEdit ? 'Edit skill group' : 'New skill group'"
    :heading="$isEdit ? $category->name : 'New skill group'"
>
    <x-slot:actions>
        <a href="{{ route('admin.skill-categories.index') }}" class="btn-ghost !px-4 !py-2 !text-xs">Cancel</a>
    </x-slot:actions>

    <form method="POST"
          action="{{ $isEdit ? route('admin.skill-categories.update', $category) : route('admin.skill-categories.store') }}"
          class="max-w-xl">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <x-admin.card>
            <div class="space-y-5">
                <x-admin.input name="name" label="Name" :value="$category->name" required placeholder="Backend" />

                <x-admin.input name="slug" label="Slug" :value="$category->slug"
                               hint="Generated from the name if left empty." />

                <x-admin.input name="description" label="Description" :value="$category->description"
                               placeholder="Domain modelling, APIs and background processing." />

                <x-admin.select name="icon" label="Icon" :value="$category->icon ?: 'code'"
                                :options="collect($icons)->mapWithKeys(fn ($icon) => [$icon => Str::headline($icon)])->all()" />

                <x-admin.input name="sort_order" label="Sort order" type="number" :value="$category->sort_order ?? 0" />
            </div>

            <div class="mt-6 flex items-center gap-3 border-t border-white/8 pt-5">
                <button type="submit" class="btn-primary !py-2.5 !text-sm">
                    {{ $isEdit ? 'Save changes' : 'Create group' }}
                </button>
                <a href="{{ route('admin.skill-categories.index') }}" class="btn-ghost !py-2.5 !text-sm">Cancel</a>
            </div>
        </x-admin.card>
    </form>
</x-admin-layout>
