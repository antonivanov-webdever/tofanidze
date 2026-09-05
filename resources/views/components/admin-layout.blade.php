@props(['title' => 'Admin', 'heading' => null, 'description' => null])

@php
    $nav = [
        ['label' => 'Overview', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'grid'],
        ['label' => 'Case studies', 'route' => 'admin.projects.index', 'active' => 'admin.projects.*', 'icon' => 'briefcase'],
        ['label' => 'Articles', 'route' => 'admin.posts.index', 'active' => 'admin.posts.*', 'icon' => 'file-text'],
        ['label' => 'Experience', 'route' => 'admin.experiences.index', 'active' => 'admin.experiences.*', 'icon' => 'calendar'],
        ['label' => 'Services', 'route' => 'admin.services.index', 'active' => 'admin.services.*', 'icon' => 'layers'],
        ['label' => 'Skills', 'route' => 'admin.skill-categories.index', 'active' => 'admin.skill-categories.*|admin.skills.*', 'icon' => 'target'],
        ['label' => 'Technologies', 'route' => 'admin.technologies.index', 'active' => 'admin.technologies.*', 'icon' => 'code'],
        ['label' => 'Messages', 'route' => 'admin.messages.index', 'active' => 'admin.messages.*', 'icon' => 'inbox'],
        ['label' => 'Settings', 'route' => 'admin.settings.edit', 'active' => 'admin.settings.*', 'icon' => 'settings'],
    ];

    $unread = \App\Models\ContactMessage::unread()->count();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} — Admin</title>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-ink-950 font-sans antialiased" x-data="{ sidebar: false }">
    <div class="lg:flex">
        {{-- ───────────── Sidebar ───────────── --}}
        <aside class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full border-r border-white/8 bg-ink-900
                      transition-transform lg:translate-x-0"
               :class="sidebar && '!translate-x-0'"
               x-cloak>
            <div class="flex h-16 items-center justify-between border-b border-white/8 px-5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-white/12
                                 bg-gradient-to-br from-white/12 to-transparent text-[11px] font-bold text-white">
                        {{ app(\App\Support\Site::class)->initials() }}
                    </span>
                    <span class="text-sm font-semibold text-white">Admin</span>
                </a>

                <button type="button" @click="sidebar = false"
                        class="text-muted transition hover:text-white lg:hidden" aria-label="Close menu">
                    <x-icon name="close" class="h-5 w-5" />
                </button>
            </div>

            <nav class="space-y-1 p-3" aria-label="Admin">
                @foreach ($nav as $item)
                    @php $isActive = request()->routeIs(explode('|', $item['active'])); @endphp
                    <a href="{{ route($item['route']) }}"
                       @class([
                           'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
                           'bg-accent-500/15 text-white' => $isActive,
                           'text-muted hover:bg-white/5 hover:text-white' => ! $isActive,
                       ])>
                        <x-icon :name="$item['icon']" class="h-4 w-4 {{ $isActive ? 'text-accent-300' : '' }}" />
                        {{ $item['label'] }}
                        @if ($item['route'] === 'admin.messages.index' && $unread > 0)
                            <span class="ml-auto rounded-full bg-accent-500 px-2 py-0.5 text-[10px] font-bold text-white">
                                {{ $unread }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="absolute inset-x-0 bottom-0 border-t border-white/8 p-3">
                <a href="{{ route('home') }}" target="_blank" rel="noopener"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-muted transition hover:bg-white/5 hover:text-white">
                    <x-icon name="external" class="h-4 w-4" />
                    View site
                </a>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-muted transition hover:bg-white/5 hover:text-white">
                        <x-icon name="logout" class="h-4 w-4" />
                        Sign out
                    </button>
                </form>
            </div>
        </aside>

        <div x-show="sidebar" x-cloak @click="sidebar = false"
             class="fixed inset-0 z-30 bg-black/60 lg:hidden" aria-hidden="true"></div>

        {{-- ───────────── Content ───────────── --}}
        <div class="min-h-screen flex-1 lg:ml-64">
            <header class="sticky top-0 z-20 border-b border-white/8 bg-ink-950/90 backdrop-blur-xl">
                <div class="flex h-16 items-center gap-4 px-5 sm:px-8">
                    <button type="button" @click="sidebar = true"
                            class="text-muted transition hover:text-white lg:hidden" aria-label="Open menu">
                        <x-icon name="menu" class="h-5 w-5" />
                    </button>

                    <div class="min-w-0 flex-1">
                        <h1 class="truncate text-base font-semibold text-white">{{ $heading ?? $title }}</h1>
                        @if ($description)
                            <p class="truncate text-xs text-muted">{{ $description }}</p>
                        @endif
                    </div>

                    @isset($actions)
                        <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
                    @endisset
                </div>
            </header>

            <main class="px-5 py-8 sm:px-8">
                @if (session('status'))
                    <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-400/25 bg-emerald-400/10 p-4"
                         role="status">
                        <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400" />
                        <p class="text-sm text-emerald-200">{{ session('status') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-400/25 bg-red-400/10 p-4" role="alert">
                        <p class="text-sm font-semibold text-red-200">
                            {{ $errors->count() }} {{ Str::plural('error', $errors->count()) }} to fix:
                        </p>
                        <ul class="mt-2 space-y-1 text-sm text-red-200/90">
                            @foreach ($errors->all() as $error)
                                <li>· {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
