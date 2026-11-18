{{--
  SEO Meta Tags: OG, Twitter Card
  Expected: $fullTitle, $pageDescription
  Optional: $metaImage, $metaImageAlt, $metaKeywords, $ogType, $noindex
--}}
@php
    $siteUrl = rtrim((string) config('app.url'), '/');
    $brandName = setting('brand_name', 'NITIP DI END');
    $metaImage = $metaImage ?? $ogImage ?? asset('images/og-default.jpg');
    if ($metaImage && ! str_starts_with((string) $metaImage, 'http')) {
        $metaImage = url($metaImage);
    }
    $metaImageAlt = $metaImageAlt ?? $brandName.' | '.$fullTitle;
    $metaKeywords = $metaKeywords ?? setting('meta_keywords', 'jastip, nitip, jastip lokal, parfum, tumbler, lifestyle, fashion, beauty');
    $canonical = $canonical ?? url()->current();
    $ogType = $ogType ?? 'website';
    $noindex = $noindex ?? false;
@endphp

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<meta name="keywords" content="{{ $metaKeywords }}">
<link rel="canonical" href="{{ $canonical }}">

<meta property="og:type" content="{{ $ogType }}">
<meta property="og:site_name" content="{{ $brandName }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:image:secure_url" content="{{ $metaImage }}">
<meta property="og:image:alt" content="{{ $metaImageAlt }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="id_ID">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $pageDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">
<meta name="twitter:image:alt" content="{{ $metaImageAlt }}">

@if($noindex)
    <meta name="robots" content="noindex, nofollow">
@else
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
@endif
<meta name="author" content="{{ $brandName }}">
<meta name="application-name" content="{{ $brandName }}">
<link rel="alternate" hreflang="id" href="{{ $canonical }}">
<link rel="alternate" hreflang="x-default" href="{{ $canonical }}">
