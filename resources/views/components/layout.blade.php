@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'publishedAt' => null,
    'noindex' => false,
])

<!DOCTYPE html>
<html lang="en" class="scroll-pt-24">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#08080b">
    <meta name="author" content="{{ $site->name() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <x-seo
        :title="$title"
        :description="$description"
        :image="$image"
        :type="$type"
        :published-at="$publishedAt"
        :noindex="$noindex"
    />

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate" type="application/rss+xml" title="{{ $site->name() }} — Writing" href="{{ route('feed') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <x-structured-data />

    @stack('head')
</head>
<body class="min-h-screen bg-ink-950 font-sans antialiased">
    <a href="#main"
       class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50
              focus:rounded-lg focus:bg-accent-500 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        Skip to content
    </a>

    <x-partials.header />

    <main id="main">
        {{ $slot }}
    </main>

    <x-partials.footer />
</body>
</html>
