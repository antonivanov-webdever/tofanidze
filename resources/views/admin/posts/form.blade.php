@php
    $isEdit = $post->exists;
@endphp

<x-admin-layout
    :title="$isEdit ? 'Edit article' : 'New article'"
    :heading="$isEdit ? $post->title : 'New article'"
    :description="$isEdit ? 'Editing /blog/'.$post->slug : 'Markdown is supported in the body.'"
>
    <x-slot:actions>
        <a href="{{ route('admin.posts.index') }}" class="btn-ghost !px-4 !py-2 !text-xs">Cancel</a>
    </x-slot:actions>

    <form method="POST"
          action="{{ $isEdit ? route('admin.posts.update', $post) : route('admin.posts.store') }}"
          class="grid gap-6 lg:grid-cols-[1.6fr_1fr]">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="space-y-6">
            <x-admin.card title="Content">
                <div class="space-y-5">
                    <x-admin.input name="title" label="Title" :value="$post->title" required />

                    <x-admin.input name="slug" label="URL slug" :value="$post->slug"
                                   hint="Leave empty to generate it from the title." />

                    <x-admin.textarea name="excerpt" label="Excerpt" :value="$post->excerpt" rows="3"
                                      hint="Shown on cards and used as the meta description fallback." />

                    <x-admin.textarea name="body" label="Body (Markdown)" :value="$post->body" required rows="26" mono
                                      hint="Headings, lists, code fences and links are all supported." />
                </div>
            </x-admin.card>

            <x-admin.card title="SEO">
                <div class="space-y-5">
                    <x-admin.input name="meta_title" label="Meta title" :value="$post->meta_title" />
                    <x-admin.textarea name="meta_description" label="Meta description" :value="$post->meta_description" rows="2" />
                </div>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card title="Publishing">
                <div class="space-y-4">
                    <x-admin.toggle name="is_published" label="Published" :checked="$post->is_published"
                                    hint="Unpublished articles are hidden from the site and the feed." />

                    <x-admin.input
                        name="published_at"
                        label="Publish date"
                        type="datetime-local"
                        :value="$post->published_at?->format('Y-m-d\TH:i')"
                        hint="Leave empty to use the moment you publish." />
                </div>

                <div class="mt-6 border-t border-white/8 pt-5">
                    <button type="submit" class="btn-primary w-full !py-2.5 !text-sm">
                        {{ $isEdit ? 'Save changes' : 'Create article' }}
                    </button>
                </div>
            </x-admin.card>

            <x-admin.card title="Metadata">
                <div class="space-y-5">
                    <x-admin.input name="tags_text" label="Tags"
                                   :value="implode(', ', $post->tags ?? [])"
                                   hint="Comma separated." placeholder="Integrations, Salesforce, Architecture" />

                    <x-admin.input name="cover_image" label="Cover image" :value="$post->cover_image"
                                   hint="A full URL, or a path inside storage/app/public." />
                </div>

                @if ($isEdit)
                    <div class="mt-6 border-t border-white/8 pt-5 text-xs text-muted">
                        <p>{{ $post->readingTime() }} min read · {{ $post->views }} views</p>
                        <p class="mt-1">Updated {{ $post->updated_at->diffForHumans() }}</p>
                    </div>
                @endif
            </x-admin.card>
        </div>
    </form>

    @if ($isEdit)
        <div class="mt-6 flex justify-end">
            <x-admin.delete-button
                :action="route('admin.posts.destroy', $post)"
                label="Delete this article"
                :confirm="'Delete “'.$post->title.'”? This cannot be undone.'"
                class="!px-4 !py-2.5" />
        </div>
    @endif
</x-admin-layout>
