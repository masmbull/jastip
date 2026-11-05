{{--
  SEO Meta Tags: OG, Twitter Card, structured data
  Variables expected:
  - $fullTitle: page title with brand
  - $pageDescription: meta description
  - $metaImage (optional): social share image
  - $metaKeywords (optional): keywords
--}}
@php
    $siteUrl = config('app.url');
    $brandName = setting('brand_name', 'NITIP DI END');
    $metaImage = $metaImage ?? asset('images/og-default.jpg');
    $metaImageAlt = $metaImageAlt ?? $brandName.' | '.$fullTitle;
    $metaKeywords = $metaKeywords ?? 'jastip, titip beli, jasa titip, belanja import, barang luar negeri';
    $canonical = url()->current();
@endphp

{{-- Basic SEO --}}
<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<meta name="keywords" content="{{ $metaKeywords }}">
<link rel="canonical" href="{{ $canonical }}">

{{-- Open Graph (Facebook, WhatsApp, LinkedIn) --}}
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $brandName }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:image:secure_url" content="{{ $metaImage }}">
<meta property="og:image:alt" content="{{ $metaImageAlt }}">
<meta property="og:locale" content="id_ID">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $pageDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">
<meta name="twitter:image:alt" content="{{ $metaImageAlt }}">

{{-- Additional SEO --}}
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="author" content="{{ $brandName }}">
