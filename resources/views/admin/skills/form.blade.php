@php
    $isEdit = $skill->exists;
@endphp

<x-admin-layout
    :title="$isEdit ? 'Edit skill' : 'New skill'"
    :heading="$isEdit ? $skill->name : 'New skill'"
>
    <x-slot:actions>
        <a href="{{ route('admin.skill-categories.index') }}" class="btn-ghost !px-4 !py-2 !text-xs">Cancel</a>
    </x-slot:actions>

    <form method="POST"
          action="{{ $isEdit ? route('admin.skills.update', $skill) : route('admin.skills.store') }}"
          class="max-w-xl">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <x-admin.card>
            <div class="space-y-5">
                <x-admin.select name="skill_category_id" label="Group" required
                                :value="$skill->skill_category_id"
                                placeholder="Choose a group"
                                :options="$categories->pluck('name', 'id')->all()" />

                <x-admin.input name="name" label="Skill" :value="$skill->name" required placeholder="Laravel" />

                <div x-data="{ level: {{ old('level', $skill->level ?: 80) }} }">
                    <label for="level" class="field-label">
                        Proficiency — <span class="font-mono text-accent-300" x-text="level + '%'"></span>
                    </label>
                    <input type="range" id="level" name="level" min="1" max="100" step="1" x-model="level"
                           class="w-full accent-accent-500">
                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-white/8">
                        <div class="h-full rounded-full bg-gradient-to-r from-accent-500 to-cyan-accent transition-[width]"
                             x-bind:style="`width: ${level}%`"></div>
                    </div>
                    @error('level')
                        <p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <x-admin.input name="years" label="Years of experience" type="number" :value="$skill->years" />

                <x-admin.input name="note" label="Note" :value="$skill->note"
                               hint="Optional context, not shown on the public site yet." />

                <x-admin.input name="sort_order" label="Sort order" type="number" :value="$skill->sort_order ?? 0" />
            </div>

            <div class="mt-6 flex items-center gap-3 border-t border-white/8 pt-5">
                <button type="submit" class="btn-primary !py-2.5 !text-sm">
                    {{ $isEdit ? 'Save changes' : 'Add skill' }}
                </button>
                <a href="{{ route('admin.skill-categories.index') }}" class="btn-ghost !py-2.5 !text-sm">Cancel</a>
            </div>
        </x-admin.card>
    </form>

    @if ($isEdit)
        <div class="mt-6 max-w-xl">
            <x-admin.delete-button
                :action="route('admin.skills.destroy', $skill)"
                label="Delete this skill"
                :confirm="'Delete “'.$skill->name.'”?'"
                class="!px-4 !py-2.5" />
        </div>
    @endif
</x-admin-layout>
