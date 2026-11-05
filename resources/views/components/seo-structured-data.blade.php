@php
    $brandName = setting('brand_name', 'NITIP DI END');
    $siteUrl = rtrim(config('app.url') ?: url('/'), '/');
    $siteDescription = $pageDescription ?? setting('meta_description', '');
    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => $siteUrl.'/#organization',
                'name' => $brandName,
                'url' => $siteUrl,
                'logo' => asset('images/icons/icon-512.png'),
                'description' => $siteDescription,
            ],
            [
                '@type' => 'WebSite',
                '@id' => $siteUrl.'/#website',
                'url' => $siteUrl,
                'name' => $brandName,
                'description' => $siteDescription,
                'inLanguage' => 'id-ID',
                'publisher' => ['@id' => $siteUrl.'/#organization'],
            ],
        ],
    ];
@endphp
<script type="application/ld+json">@json($schema)</script>
