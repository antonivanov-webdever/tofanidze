@php
    $site = app(\App\Support\Site::class);

    $person = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $site->name(),
        'url' => route('home'),
        'email' => 'mailto:'.$site->get('email'),
        'jobTitle' => $site->get('headline'),
        'description' => $site->get('meta_description'),
        'knowsAbout' => [
            'PHP', 'Laravel', 'Yii', 'Node.js', 'NestJS', 'Vue.js', 'React',
            'MySQL', 'MongoDB', 'RabbitMQ', 'Salesforce', 'Marketo', 'HubSpot',
            'B2B marketing platforms', 'Enterprise portals', 'System integration',
        ],
        'address' => $site->get('location') ? [
            '@type' => 'PostalAddress',
            'addressLocality' => $site->get('location'),
        ] : null,
        'sameAs' => array_values($site->socialLinks()),
    ], fn ($value) => filled($value));
@endphp

<script type="application/ld+json">
    {!! json_encode($person, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
