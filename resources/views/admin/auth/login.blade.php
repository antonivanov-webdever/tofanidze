<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign in — Admin</title>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-ink-950 px-5 font-sans antialiased">
    <div class="pointer-events-none fixed inset-0 bg-grid opacity-60" aria-hidden="true"></div>
    <div class="pointer-events-none fixed -top-32 left-1/2 h-96 w-[640px] -translate-x-1/2 glow-accent opacity-40"
         aria-hidden="true"></div>

    <div class="relative w-full max-w-sm">
        <div class="mb-8 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl border border-white/12
                         bg-gradient-to-br from-white/12 to-transparent text-sm font-bold text-white">
                {{ $site->initials() }}
            </span>
            <h1 class="mt-5 text-xl font-semibold text-white">Sign in</h1>
            <p class="mt-1.5 text-sm text-muted">Content management for {{ $site->name() }}</p>
        </div>

        <form method="POST" action="{{ route('admin.login') }}" class="panel space-y-5 p-7">
            @csrf

            <div>
                <label for="email" class="field-label">Email</label>
                <input type="email" id="email" name="email" required autofocus autocomplete="username"
                       value="{{ old('email') }}"
                       @class(['field', 'border-red-400/50' => $errors->has('email')])>
                @error('email')
                    <p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="field-label">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password"
                       @class(['field', 'border-red-400/50' => $errors->has('password')])>
                @error('password')
                    <p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex cursor-pointer items-center gap-2.5 text-sm text-muted">
                <input type="checkbox" name="remember" value="1"
                       class="h-4 w-4 rounded border-white/20 bg-ink-900 text-accent-500 focus:ring-accent-500/40">
                Stay signed in
            </label>

            <button type="submit" class="btn-primary w-full">
                Sign in
                <x-icon name="arrow-right" class="h-4 w-4" />
            </button>
        </form>

        <p class="mt-6 text-center text-xs text-muted">
            <a href="{{ route('home') }}" class="transition hover:text-white">← Back to the site</a>
        </p>
    </div>
</body>
</html>
