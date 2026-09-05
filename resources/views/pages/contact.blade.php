<x-layout
    title="Contact"
    description="Tell me about your portal, dashboard or integration project. Straight answer on scope and approach, usually within one business day."
>
    <x-page-header
        eyebrow="Contact"
        title="Let's talk about your project"
        lead="Whether it is a new portal, a dashboard nobody trusts, or an integration that keeps dropping records — describe the situation and I will tell you honestly how I would approach it."
    />

    <div class="mx-auto max-w-6xl px-5 py-16 sm:px-8">
        <div class="grid gap-10 lg:grid-cols-[1.4fr_1fr]">
            {{-- ───────────────── Form ───────────────── --}}
            <div>
                @if (session('status'))
                    <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-400/25 bg-emerald-400/10 p-4"
                         role="status">
                        <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400" />
                        <p class="text-sm text-emerald-200">{{ session('status') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-400/25 bg-red-400/10 p-4" role="alert">
                        <p class="text-sm font-semibold text-red-200">Please fix the following:</p>
                        <ul class="mt-2 space-y-1 text-sm text-red-200/90">
                            @foreach ($errors->all() as $error)
                                <li>· {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="panel space-y-5 p-6 sm:p-8">
                    @csrf
                    <input type="hidden" name="rendered_at" value="{{ time() }}">

                    {{-- Honeypot: hidden from humans, irresistible to bots. --}}
                    <div class="absolute left-[-9999px]" aria-hidden="true">
                        <label for="website">Website</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="name" class="field-label">Your name <span class="text-accent-400">*</span></label>
                            <input type="text" id="name" name="name" required autocomplete="name"
                                   value="{{ old('name') }}"
                                   @class(['field', 'border-red-400/50' => $errors->has('name')])
                                   placeholder="Jane Doe">
                            @error('name')
                                <p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="field-label">Email <span class="text-accent-400">*</span></label>
                            <input type="email" id="email" name="email" required autocomplete="email"
                                   value="{{ old('email') }}"
                                   @class(['field', 'border-red-400/50' => $errors->has('email')])
                                   placeholder="jane@company.com">
                            @error('email')
                                <p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="company" class="field-label">Company</label>
                            <input type="text" id="company" name="company" autocomplete="organization"
                                   value="{{ old('company') }}" class="field" placeholder="Acme Inc.">
                        </div>

                        <div>
                            <label for="budget" class="field-label">Budget range</label>
                            <select id="budget" name="budget" class="field">
                                <option value="">Not sure yet</option>
                                @foreach ([
                                    'Under €5k',
                                    '€5k – €15k',
                                    '€15k – €40k',
                                    '€40k+',
                                    'Ongoing / retainer',
                                ] as $range)
                                    <option value="{{ $range }}" @selected(old('budget') === $range)>{{ $range }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="field-label">Subject</label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                               class="field" placeholder="Partner portal with Salesforce sync">
                    </div>

                    <div x-data="{ count: {{ strlen(old('message', '')) }} }">
                        <label for="message" class="field-label">
                            Project details <span class="text-accent-400">*</span>
                        </label>
                        <textarea id="message" name="message" rows="7" required
                                  x-on:input="count = $event.target.value.length"
                                  @class(['field resize-y', 'border-red-400/50' => $errors->has('message')])
                                  placeholder="What are you building, what is currently in place, and what does success look like?">{{ old('message') }}</textarea>
                        <div class="mt-1.5 flex items-center justify-between">
                            @error('message')
                                <p class="text-xs text-red-300">{{ $message }}</p>
                            @else
                                <p class="text-xs text-muted">The more context, the more useful my answer.</p>
                            @enderror
                            <span class="font-mono text-xs text-muted" x-text="`${count}/5000`">0/5000</span>
                        </div>
                    </div>

                    <div class="flex flex-col items-start gap-4 border-t border-white/8 pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs leading-relaxed text-muted">
                            Your details are used only to reply to this enquiry.
                        </p>
                        <button type="submit" class="btn-primary w-full sm:w-auto">
                            Send message
                            <x-icon name="arrow-right" class="h-4 w-4" />
                        </button>
                    </div>
                </form>
            </div>

            {{-- ───────────────── Sidebar ───────────────── --}}
            <aside class="space-y-4">
                <div class="panel p-6">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-muted">Direct</h2>

                    <div class="mt-5 space-y-4">
                        <div x-data="copyable('{{ $site->get('email') }}')">
                            <p class="text-xs text-muted">Email</p>
                            <div class="mt-1 flex items-center justify-between gap-3">
                                <a href="mailto:{{ $site->get('email') }}"
                                   class="text-sm font-medium text-white hover:text-accent-300">
                                    {{ $site->get('email') }}
                                </a>
                                <button type="button" @click="copy()"
                                        class="shrink-0 rounded-lg border border-white/10 p-2 text-muted transition hover:text-white"
                                        :aria-label="copied ? 'Copied' : 'Copy email address'">
                                    <x-icon name="copy" class="h-3.5 w-3.5" x-show="!copied" />
                                    <x-icon name="check" class="h-3.5 w-3.5 text-emerald-400" x-show="copied" x-cloak />
                                </button>
                            </div>
                        </div>

                        @if ($site->get('phone'))
                            <div>
                                <p class="text-xs text-muted">Phone</p>
                                <a href="tel:{{ preg_replace('/[^+0-9]/', '', $site->get('phone')) }}"
                                   class="mt-1 block text-sm font-medium text-white hover:text-accent-300">
                                    {{ $site->get('phone') }}
                                </a>
                            </div>
                        @endif

                        <div>
                            <p class="text-xs text-muted">Location</p>
                            <p class="mt-1 text-sm font-medium text-white">{{ $site->get('location') }}</p>
                            @if ($site->get('timezone_label'))
                                <p class="mt-0.5 font-mono text-xs text-muted">{{ $site->get('timezone_label') }}</p>
                            @endif
                        </div>
                    </div>

                    @if ($site->socialLinks())
                        <div class="mt-6 flex gap-2 border-t border-white/8 pt-5">
                            @foreach ($site->socialLinks() as $network => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                   class="flex h-10 w-10 items-center justify-center rounded-lg border border-white/10
                                          text-muted transition hover:border-accent-400/40 hover:text-white"
                                   aria-label="{{ ucfirst($network) }}">
                                    <x-icon :name="$network" class="h-4 w-4" />
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="panel p-6">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-muted">What happens next</h2>
                    <ol class="mt-5 space-y-4">
                        @foreach ([
                            ['Within one business day', 'I read your message and reply with first questions or a straight “not my area”.'],
                            ['A 30-minute call', 'We go through the current setup, the constraints and what success looks like.'],
                            ['A written proposal', 'Scope, approach, milestones and price — no surprises later.'],
                        ] as $index => [$title, $description])
                            <li class="flex gap-4">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border
                                             border-accent-400/25 bg-accent-500/10 font-mono text-xs text-accent-300">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <p class="text-sm font-medium text-white">{{ $title }}</p>
                                    <p class="mt-1 text-xs leading-relaxed text-muted">{{ $description }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>

                @if ($site->get('available_for_work'))
                    <div class="panel border-emerald-400/20 bg-emerald-400/[0.04] p-6">
                        <p class="inline-flex items-center gap-2 text-sm font-medium text-emerald-300">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-60"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                            </span>
                            {{ $site->get('availability_note') }}
                        </p>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</x-layout>
