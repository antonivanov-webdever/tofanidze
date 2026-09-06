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
            {{-- Submitted via fetch(), not a plain POST — see routes/web.php --}}
            <div x-data="contactForm()">
                <div x-show="succeeded" x-cloak
                     class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-400/25 bg-emerald-400/10 p-4"
                     role="status">
                    <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400" />
                    <p class="text-sm text-emerald-200" x-text="successMessage"></p>
                </div>

                <div x-show="generalError" x-cloak
                     class="mb-6 flex items-start gap-3 rounded-xl border border-red-400/25 bg-red-400/10 p-4" role="alert">
                    <p class="text-sm text-red-200" x-text="generalError"></p>
                </div>

                <form action="{{ route('contact.store') }}" class="panel space-y-5 p-6 sm:p-8" @submit.prevent="submit($event)">
                    {{-- Honeypot: hidden from humans, irresistible to bots. --}}
                    <div class="absolute left-[-9999px]" aria-hidden="true">
                        <label for="website">Website</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="name" class="field-label">Your name <span class="text-accent-400">*</span></label>
                            <input type="text" id="name" name="name" required autocomplete="name"
                                   :class="errors.name && 'border-red-400/50'"
                                   class="field" placeholder="Jane Doe">
                            <p class="mt-1.5 text-xs text-red-300" x-show="errors.name" x-text="errors.name"></p>
                        </div>

                        <div>
                            <label for="email" class="field-label">Email <span class="text-accent-400">*</span></label>
                            <input type="email" id="email" name="email" required autocomplete="email"
                                   :class="errors.email && 'border-red-400/50'"
                                   class="field" placeholder="jane@company.com">
                            <p class="mt-1.5 text-xs text-red-300" x-show="errors.email" x-text="errors.email"></p>
                        </div>

                        <div>
                            <label for="company" class="field-label">Company</label>
                            <input type="text" id="company" name="company" autocomplete="organization"
                                   class="field" placeholder="Acme Inc.">
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
                                    <option value="{{ $range }}">{{ $range }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="field-label">Subject</label>
                        <input type="text" id="subject" name="subject"
                               class="field" placeholder="Partner portal with Salesforce sync">
                    </div>

                    <div x-data="{ count: 0 }">
                        <label for="message" class="field-label">
                            Project details <span class="text-accent-400">*</span>
                        </label>
                        <textarea id="message" name="message" rows="7" required
                                  x-on:input="count = $event.target.value.length"
                                  :class="errors.message && 'border-red-400/50'"
                                  class="field resize-y"
                                  placeholder="What are you building, what is currently in place, and what does success look like?"></textarea>
                        <div class="mt-1.5 flex items-center justify-between">
                            <p class="text-xs text-red-300" x-show="errors.message" x-text="errors.message"></p>
                            <p class="text-xs text-muted" x-show="!errors.message">The more context, the more useful my answer.</p>
                            <span class="font-mono text-xs text-muted" x-text="`${count}/5000`">0/5000</span>
                        </div>
                    </div>

                    <div class="flex flex-col items-start gap-4 border-t border-white/8 pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs leading-relaxed text-muted">
                            Your details are used only to reply to this enquiry.
                        </p>
                        <button type="submit" class="btn-primary w-full sm:w-auto" :disabled="submitting">
                            <span x-text="submitting ? 'Sending…' : 'Send message'"></span>
                            <x-icon name="arrow-right" class="h-4 w-4" x-show="!submitting" />
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
