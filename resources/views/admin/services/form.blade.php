@php
    $isEdit = $service->exists;
    $icons = ['building', 'chart', 'plug', 'refresh', 'layers', 'server', 'layout', 'database',
              'code', 'zap', 'target', 'sparkles', 'grid', 'briefcase'];
@endphp

<x-admin-layout
    :title="$isEdit ? 'Edit service' : 'New service'"
    :heading="$isEdit ? $service->title : 'New service'"
>
    <x-slot:actions>
        <a href="{{ route('admin.services.index') }}" class="btn-ghost !px-4 !py-2 !text-xs">Cancel</a>
    </x-slot:actions>

    <form method="POST"
          action="{{ $isEdit ? route('admin.services.update', $service) : route('admin.services.store') }}"
          class="grid gap-6 lg:grid-cols-[1.6fr_1fr]">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <x-admin.card title="Content">
            <div class="space-y-5">
                <x-admin.input name="title" label="Title" :value="$service->title" required
                               placeholder="Enterprise portals" />

                <x-admin.input name="subtitle" label="Subtitle" :value="$service->subtitle"
                               placeholder="Partner, client and internal platforms" />

                <x-admin.textarea name="description" label="Description" :value="$service->description" required rows="5" />

                <x-admin.textarea
                    name="bullets_text"
                    label="Bullets"
                    :value="\App\Support\ListInput::toText($service->bullets)"
                    rows="4"
                    mono
                    hint="One per line."
                    placeholder="Multi-tenant architecture and role-based access control" />
            </div>
        </x-admin.card>

        <div class="space-y-6">
            <x-admin.card title="Presentation">
                <div class="space-y-5">
                    <x-admin.select name="icon" label="Icon" :value="$service->icon ?: 'layers'" required
                                    :options="collect($icons)->mapWithKeys(fn ($icon) => [$icon => Str::headline($icon)])->all()" />

                    <div class="flex flex-wrap gap-2 rounded-xl border border-white/10 bg-white/[0.02] p-4">
                        @foreach ($icons as $icon)
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10
                                         text-muted" title="{{ $icon }}">
                                <x-icon :name="$icon" class="h-4 w-4" />
                            </span>
                        @endforeach
                    </div>

                    <x-admin.input name="sort_order" label="Sort order" type="number" :value="$service->sort_order ?? 0" />

                    <x-admin.toggle name="is_published" label="Published" :checked="$service->is_published" />
                </div>

                <div class="mt-6 border-t border-white/8 pt-5">
                    <button type="submit" class="btn-primary w-full !py-2.5 !text-sm">
                        {{ $isEdit ? 'Save changes' : 'Create service' }}
                    </button>
                </div>
            </x-admin.card>
        </div>
    </form>

    @if ($isEdit)
        <div class="mt-6 flex justify-end">
            <x-admin.delete-button
                :action="route('admin.services.destroy', $service)"
                label="Delete this service"
                :confirm="'Delete “'.$service->title.'”?'"
                class="!px-4 !py-2.5" />
        </div>
    @endif
</x-admin-layout>
