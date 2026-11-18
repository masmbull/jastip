@php
    $brandName = setting('brand_name', 'NITIP DI END');
    $siteUrl = rtrim(config('app.url') ?: url('/'), '/');
    $siteDescription = $pageDescription ?? setting('meta_description', '');
    $logo = media_url(setting('brand_logo')) ?: asset('images/icons/icon-512.png');
    $sameAs = array_values(array_filter([
        setting('instagram') ? 'https://instagram.com/'.ltrim((string) setting('instagram'), '@') : null,
        setting('tiktok') ? 'https://www.tiktok.com/@'.ltrim((string) setting('tiktok'), '@') : null,
        setting('whatsapp') ? whatsapp_url((string) setting('whatsapp')) : null,
    ]));

    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => $siteUrl.'/#organization',
                'name' => $brandName,
                'url' => $siteUrl,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $logo,
                ],
                'description' => $siteDescription,
                'sameAs' => $sameAs ?: null,
                'contactPoint' => setting('whatsapp') ? [[
                    '@type' => 'ContactPoint',
                    'contactType' => 'customer service',
                    'telephone' => '+'.preg_replace('/\D+/', '', (string) setting('whatsapp')),
                    'availableLanguage' => ['Indonesian'],
                ]] : null,
            ],
            [
                '@type' => 'WebSite',
                '@id' => $siteUrl.'/#website',
                'url' => $siteUrl,
                'name' => $brandName,
                'description' => $siteDescription,
                'inLanguage' => 'id-ID',
                'publisher' => ['@id' => $siteUrl.'/#organization'],
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => $siteUrl.'/produk?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ],
        ],
    ];

    // Bersihkan null supaya JSON-LD valid & rapi.
    array_walk_recursive($schema, function (&$value) {
        // no-op placeholder for PHPstan-friendly recurse start
    });
    $schema['@graph'] = array_map(function (array $node) {
        return array_filter($node, fn ($v) => $v !== null && $v !== []);
    }, $schema['@graph']);
@endphp
<script type="application/ld+json">@json($schema)</script>
