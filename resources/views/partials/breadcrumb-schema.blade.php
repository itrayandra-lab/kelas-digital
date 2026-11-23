{{-- $crumbs: [label => url], the last one is the current page. --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => collect($crumbs)->keys()->map(fn ($name, $i) => [
        '@type' => 'ListItem', 'position' => $i + 1, 'name' => $name, 'item' => $crumbs[$name],
    ])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
</script>
