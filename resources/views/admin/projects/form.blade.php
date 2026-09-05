@php
    $isEdit = $project->exists;
@endphp

<x-admin-layout
    :title="$isEdit ? 'Edit case study' : 'New case study'"
    :heading="$isEdit ? $project->title : 'New case study'"
    :description="$isEdit ? 'Editing /'.$project->slug : 'Add a project to the portfolio.'"
>
    <x-slot:actions>
        <a href="{{ route('admin.projects.index') }}" class="btn-ghost !px-4 !py-2 !text-xs">Cancel</a>
    </x-slot:actions>

    <form method="POST"
          action="{{ $isEdit ? route('admin.projects.update', $project) : route('admin.projects.store') }}"
          class="grid gap-6 lg:grid-cols-[1.6fr_1fr]">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="space-y-6">
            <x-admin.card title="Basics">
                <div class="space-y-5">
                    <x-admin.input name="title" label="Title" :value="$project->title" required
                                   placeholder="Partner Portal for an Enterprise SaaS Vendor" />

                    <x-admin.input name="slug" label="URL slug" :value="$project->slug"
                                   hint="Leave empty to generate it from the title." placeholder="partner-portal" />

                    <x-admin.textarea name="summary" label="Summary" :value="$project->summary" required rows="3"
                                      hint="One or two sentences. Shown on cards and in search results."
                                      placeholder="A multi-tenant portal where 600+ resellers register deals…" />
                </div>
            </x-admin.card>

            <x-admin.card title="The story" description="Each block renders as its own section on the case study page. Leave a blank line between paragraphs.">
                <div class="space-y-5">
                    <x-admin.textarea name="challenge" label="The challenge" :value="$project->challenge" rows="6"
                                      placeholder="What was broken, and why it mattered to the business." />

                    <x-admin.textarea name="solution" label="The solution" :value="$project->solution" rows="8"
                                      placeholder="The architecture and the decisions behind it." />

                    <x-admin.textarea name="outcome" label="The outcome" :value="$project->outcome" rows="4"
                                      placeholder="What changed afterwards." />
                </div>
            </x-admin.card>

            <x-admin.card title="Numbers and highlights">
                <div class="space-y-5">
                    <x-admin.textarea
                        name="metrics_text"
                        label="Metrics"
                        :value="\App\Support\ListInput::pairsToText($project->metrics, 'value', 'label')"
                        rows="4"
                        mono
                        hint="One per line, as: value | label — for example “600+ | active partner accounts”."
                        placeholder="600+ | active partner accounts&#10;<2 min | deal-to-Salesforce latency" />

                    <x-admin.textarea
                        name="highlights_text"
                        label="Technical highlights"
                        :value="\App\Support\ListInput::toText($project->highlights)"
                        rows="4"
                        mono
                        hint="One per line."
                        placeholder="Field-level RBAC across four partner roles" />
                </div>
            </x-admin.card>

            <x-admin.card title="SEO">
                <div class="space-y-5">
                    <x-admin.input name="meta_title" label="Meta title" :value="$project->meta_title"
                                   hint="Defaults to the project title." />
                    <x-admin.textarea name="meta_description" label="Meta description" :value="$project->meta_description"
                                      rows="2" hint="Defaults to the summary. Aim for 150–160 characters." />
                </div>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card title="Visibility">
                <div class="space-y-3">
                    <x-admin.toggle name="is_published" label="Published" :checked="$project->is_published"
                                    hint="Visible on the public site." />
                    <x-admin.toggle name="is_featured" label="Featured" :checked="$project->is_featured"
                                    hint="Shown on the home page." />
                    <x-admin.input name="sort_order" label="Sort order" type="number" :value="$project->sort_order ?? 0"
                                   hint="Lower numbers come first." />
                </div>

                <div class="mt-6 border-t border-white/8 pt-5">
                    <button type="submit" class="btn-primary w-full !py-2.5 !text-sm">
                        {{ $isEdit ? 'Save changes' : 'Create case study' }}
                    </button>
                </div>
            </x-admin.card>

            <x-admin.card title="Context">
                <div class="space-y-5">
                    <x-admin.input name="client" label="Client" :value="$project->client"
                                   placeholder="Enterprise SaaS vendor" />
                    <x-admin.input name="industry" label="Industry" :value="$project->industry" placeholder="B2B SaaS" />
                    <x-admin.input name="role" label="Your role" :value="$project->role"
                                   placeholder="Lead full-stack engineer" />
                    <x-admin.input name="year" label="Year" type="number" :value="$project->year" />
                    <x-admin.input name="duration" label="Duration" :value="$project->duration" placeholder="7 months" />
                    <x-admin.input name="team_size" label="Team" :value="$project->team_size"
                                   placeholder="4 engineers, 1 designer" />
                </div>
            </x-admin.card>

            <x-admin.card title="Stack">
                <div class="max-h-80 space-y-4 overflow-y-auto pr-1">
                    @foreach (\App\Models\Technology::CATEGORIES as $key => $categoryLabel)
                        @php $items = $technologies->where('category', $key); @endphp
                        @continue($items->isEmpty())

                        <div>
                            <p class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-muted">
                                {{ $categoryLabel }}
                            </p>
                            <div class="space-y-1.5">
                                @foreach ($items as $technology)
                                    <label class="flex cursor-pointer items-center gap-2.5 text-sm text-muted-strong
                                                  transition hover:text-white">
                                        <input type="checkbox" name="technologies[]" value="{{ $technology->id }}"
                                               @checked(in_array($technology->id, old('technologies', $project->technologies->pluck('id')->all())))
                                               class="h-4 w-4 rounded border-white/20 bg-ink-900 text-accent-500 focus:ring-accent-500/40">
                                        {{ $technology->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-admin.card>

            <x-admin.card title="Media and links">
                <div class="space-y-5">
                    <x-admin.input name="cover_image" label="Cover image" :value="$project->cover_image"
                                   hint="A full URL, or a path inside storage/app/public." />
                    <x-admin.input name="external_url" label="Live URL" type="url" :value="$project->external_url" />
                    <x-admin.input name="repository_url" label="Repository" type="url" :value="$project->repository_url" />
                </div>
            </x-admin.card>

            @if ($isEdit)
                <x-admin.card>
                    <p class="text-xs text-muted">
                        Created {{ $project->created_at->format('d M Y') }} ·
                        updated {{ $project->updated_at->diffForHumans() }}
                    </p>
                </x-admin.card>
            @endif
        </div>
    </form>

    @if ($isEdit)
        <div class="mt-6 flex justify-end">
            <x-admin.delete-button
                :action="route('admin.projects.destroy', $project)"
                label="Delete this case study"
                :confirm="'Delete “'.$project->title.'”? This cannot be undone.'"
                class="!px-4 !py-2.5" />
        </div>
    @endif
</x-admin-layout>
