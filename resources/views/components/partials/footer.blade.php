<footer class="mt-24 border-t border-white/8 bg-ink-900/40">
    <div class="mx-auto max-w-6xl px-5 py-14 sm:px-8">
        <div class="grid gap-10 md:grid-cols-[1.4fr_1fr_1fr]">
            <div>
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/12
                                 bg-gradient-to-br from-white/12 to-transparent text-[13px] font-bold text-white">
                        {{ $site->initials() }}
                    </span>
                    <span class="text-sm font-semibold text-white">{{ $site->name() }}</span>
                </div>

                <p class="mt-4 max-w-sm text-sm leading-relaxed text-muted">
                    {{ $site->get('tagline') }}. Portals, dashboards and the integrations that keep them in sync.
                </p>

                @if ($site->get('available_for_work'))
                    <p class="mt-5 inline-flex items-center gap-2 rounded-full border border-emerald-400/25
                              bg-emerald-400/10 px-3 py-1.5 text-xs font-medium text-emerald-300">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-60"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                        </span>
                        {{ $site->get('availability_note') }}
                    </p>
                @endif
            </div>

            <nav aria-label="Footer">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-muted">Site</h2>
                <ul class="mt-4 space-y-2.5 text-sm">
                    @foreach ([
                        'home' => 'Home',
                        'projects.index' => 'Case studies',
                        'about' => 'About',
                        'blog.index' => 'Writing',
                        'resume.show' => 'Résumé',
                        'contact.show' => 'Contact',
                    ] as $route => $label)
                        <li>
                            <a href="{{ route($route) }}" class="text-muted transition hover:text-white">{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div>
                <h2 class="text-xs font-semibold uppercase tracking-wider text-muted">Contact</h2>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li>
                        <a href="mailto:{{ $site->get('email') }}"
                           class="inline-flex items-center gap-2 text-muted transition hover:text-white">
                            <x-icon name="mail" class="h-4 w-4" />
                            {{ $site->get('email') }}
                        </a>
                    </li>
                    @if ($site->get('location'))
                        <li class="inline-flex items-center gap-2 text-muted">
                            <x-icon name="map-pin" class="h-4 w-4" />
                            {{ $site->get('location') }}
                        </li>
                    @endif
                    @foreach ($site->socialLinks() as $network => $url)
                        <li>
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 text-muted transition hover:text-white">
                                <x-icon :name="$network" class="h-4 w-4" />
                                {{ ucfirst($network) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-start justify-between gap-4 border-t border-white/8 pt-6
                    text-xs text-muted sm:flex-row sm:items-center">
            <p>© {{ now()->year }} {{ $site->name() }}. All rights reserved.</p>
            <p class="font-mono">
                Built with Laravel {{ Illuminate\Foundation\Application::VERSION }}, Tailwind CSS and Alpine.js
            </p>
        </div>
    </div>
</footer>
