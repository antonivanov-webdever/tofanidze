@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'canonical' => null,
    'publishedAt' => null,
    'noindex' => false,
])

@php
    $site = app(\App\Support\Site::class);
    $siteName = $site->name();
    $fullTitle = $title ? $title.' — '.$siteName : $site->get('meta_title');
    $metaDescription = \Illuminate\Support\Str::limit(
        $description ?: $site->get('meta_description'),
        200,
    );
    $canonicalUrl = $canonical ?: url()->current();
    $imageUrl = $image ?: asset('images/og-default.png');
@endphp

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

@if ($noindex)
    <meta name="robots" content="noindex, nofollow">
@else
    <meta name="robots" content="index, follow, max-image-preview:large">
@endif

<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:image" content="{{ $imageUrl }}">
@if ($publishedAt)
    <meta property="article:published_time" content="{{ $publishedAt }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $imageUrl }}">
