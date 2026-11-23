{{-- Who runs the site and how to search it, on every page. Pages add their own schema through @push('schema'). --}}
@php
    $home = url('/');
    $sameAs = collect(['social_facebook', 'social_instagram', 'social_youtube', 'social_tiktok', 'social_linkedin', 'social_twitter'])
        ->map(fn ($key) => \App\Models\Setting::get($key))->filter()->values()->all();
@endphp
<link rel="alternate" type="application/rss+xml" title="RayAcademy" href="{{ url('/feed.xml') }}">
<link rel="search" type="application/opensearchdescription+xml" title="RayAcademy" href="{{ url('/opensearch.xml') }}">
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        array_filter([
            '@type' => 'EducationalOrganization',
            '@id' => $home.'#organization',
            'name' => 'RayAcademy',
            'alternateName' => 'Ray Academy',
            'url' => $home,
            'logo' => asset('logo.png'),
            'description' => config('seo.description.fallback'),
            'email' => \App\Models\Setting::get('contact_email'),
            'telephone' => \App\Models\Setting::get('contact_phone'),
            'address' => \App\Models\Setting::get('contact_address') ? ['@type' => 'PostalAddress', 'streetAddress' => \App\Models\Setting::get('contact_address'), 'addressCountry' => 'ID'] : null,
            'sameAs' => $sameAs ?: null,
        ]),
        [
            '@type' => 'WebSite',
            '@id' => $home.'#website',
            'url' => $home,
            'name' => 'RayAcademy',
            'inLanguage' => 'id-ID',
            'publisher' => ['@id' => $home.'#organization'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/search').'?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
</script>
@stack('schema')
