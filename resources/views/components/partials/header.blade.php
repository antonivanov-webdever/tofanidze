@php
    $links = [
        ['label' => 'Work', 'route' => 'projects.index', 'active' => 'projects.*'],
        ['label' => 'About', 'route' => 'about', 'active' => 'about'],
        ['label' => 'Writing', 'route' => 'blog.index', 'active' => 'blog.*'],
        ['label' => 'Résumé', 'route' => 'resume.show', 'active' => 'resume.*'],
    ];
@endphp

<header x-data="{ open: false, scrolled: false }"
        @scroll.window="scrolled = window.scrollY > 16"
        class="sticky top-0 z-40 bg-ink-950/70 backdrop-blur-xl transition duration-300"
        :class="scrolled ? 'border-b border-white/8 !bg-ink-950/90' : 'border-b border-transparent'">
    <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-6 px-5 sm:px-8">
        <a href="{{ route('home') }}" class="group flex items-center gap-3" aria-label="{{ $site->name() }} — home">
            <span class="relative flex h-9 w-9 items-center justify-center rounded-xl border border-white/12
                         bg-gradient-to-br from-white/12 to-transparent text-[13px] font-bold tracking-tight text-white
                         transition group-hover:border-accent-400/50">
                {{ $site->initials() }}
            </span>
            <span class="hidden sm:block">
                <span class="block text-sm font-semibold leading-tight text-white">{{ $site->name() }}</span>
                <span class="block font-mono text-[11px] leading-tight text-muted">{{ $site->get('headline') }}</span>
            </span>
        </a>

        <nav class="hidden items-center gap-1 md:flex" aria-label="Main">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}"
                   @class([
                       'rounded-lg px-3.5 py-2 text-sm font-medium transition',
                       'text-white bg-white/8' => request()->routeIs($link['active']),
                       'text-muted hover:text-white hover:bg-white/5' => ! request()->routeIs($link['active']),
                   ])>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-3">
            <a href="{{ route('contact.show') }}"
               class="hidden rounded-xl border border-accent-400/30 bg-accent-500/10 px-4 py-2 text-sm font-semibold
                      text-accent-300 transition hover:border-accent-400/60 hover:bg-accent-500/20 hover:text-white sm:inline-flex">
                Start a project
            </a>

            <button type="button"
                    @click="open = !open"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-white/10
                           text-muted transition hover:text-white md:hidden"
                    :aria-expanded="open"
                    aria-controls="mobile-nav"
                    aria-label="Toggle navigation">
                <x-icon name="menu" x-show="!open" />
                <x-icon name="close" x-show="open" x-cloak />
            </button>
        </div>
    </div>

    <div id="mobile-nav"
         x-show="open"
         x-cloak
         x-transition.origin.top
         @click.outside="open = false"
         class="border-t border-white/8 bg-ink-950/95 backdrop-blur-xl md:hidden">
        <nav class="mx-auto max-w-6xl space-y-1 px-5 py-4 sm:px-8" aria-label="Mobile">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}"
                   @class([
                       'block rounded-lg px-4 py-3 text-sm font-medium transition',
                       'bg-white/8 text-white' => request()->routeIs($link['active']),
                       'text-muted hover:bg-white/5 hover:text-white' => ! request()->routeIs($link['active']),
                   ])>
                    {{ $link['label'] }}
                </a>
            @endforeach

            <a href="{{ route('contact.show') }}"
               class="mt-2 block rounded-lg bg-accent-500/15 px-4 py-3 text-sm font-semibold text-accent-300">
                Start a project
            </a>
        </nav>
    </div>
</header>
