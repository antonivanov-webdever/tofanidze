@php
    $isEdit = $experience->exists;
@endphp

<x-admin-layout
    :title="$isEdit ? 'Edit role' : 'New role'"
    :heading="$isEdit ? $experience->position.' · '.$experience->company : 'New role'"
>
    <x-slot:actions>
        <a href="{{ route('admin.experiences.index') }}" class="btn-ghost !px-4 !py-2 !text-xs">Cancel</a>
    </x-slot:actions>

    <form method="POST"
          action="{{ $isEdit ? route('admin.experiences.update', $experience) : route('admin.experiences.store') }}"
          class="grid gap-6 lg:grid-cols-[1.6fr_1fr]">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="space-y-6">
            <x-admin.card title="Role">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input name="position" label="Position" :value="$experience->position" required
                                   placeholder="Senior Full-Stack Engineer" />
                    <x-admin.input name="company" label="Company" :value="$experience->company" required />
                    <x-admin.input name="location" label="Location" :value="$experience->location" placeholder="Remote" />
                    <x-admin.input name="employment_type" label="Employment type" :value="$experience->employment_type"
                                   placeholder="Full-time" />
                    <x-admin.input name="company_url" label="Company URL" type="url" :value="$experience->company_url"
                                   class="sm:col-span-2" />
                </div>

                <div class="mt-5">
                    <x-admin.textarea name="description" label="Summary" :value="$experience->description" rows="3"
                                      hint="One or two sentences on what the role covered." />
                </div>
            </x-admin.card>

            <x-admin.card title="Details">
                <div class="space-y-5">
                    <x-admin.textarea
                        name="highlights_text"
                        label="Highlights"
                        :value="\App\Support\ListInput::toText($experience->highlights)"
                        rows="6"
                        mono
                        hint="One achievement per line. Lead with the outcome where you can."
                        placeholder="Rebuilt a reporting dashboard on pre-aggregated rollups, cutting load from 9s to 400ms." />

                    <x-admin.input name="stack_text" label="Stack"
                                   :value="implode(', ', $experience->stack ?? [])"
                                   hint="Comma separated."
                                   placeholder="Laravel, NestJS, Vue 3, MySQL, RabbitMQ" />
                </div>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card title="Dates">
                <div class="space-y-5">
                    <x-admin.input name="started_at" label="Start date" type="date" required
                                   :value="$experience->started_at?->format('Y-m-d')" />

                    <x-admin.toggle name="is_current" label="Current role" :checked="$experience->is_current"
                                    hint="Clears the end date and shows “Present”." />

                    <x-admin.input name="ended_at" label="End date" type="date"
                                   :value="$experience->ended_at?->format('Y-m-d')" />

                    <x-admin.input name="sort_order" label="Sort order" type="number"
                                   :value="$experience->sort_order ?? 0" hint="Lower numbers come first." />
                </div>

                <div class="mt-6 border-t border-white/8 pt-5">
                    <button type="submit" class="btn-primary w-full !py-2.5 !text-sm">
                        {{ $isEdit ? 'Save changes' : 'Add role' }}
                    </button>
                </div>
            </x-admin.card>
        </div>
    </form>

    @if ($isEdit)
        <div class="mt-6 flex justify-end">
            <x-admin.delete-button
                :action="route('admin.experiences.destroy', $experience)"
                label="Delete this role"
                :confirm="'Delete the role at '.$experience->company.'?'"
                class="!px-4 !py-2.5" />
        </div>
    @endif
</x-admin-layout>
