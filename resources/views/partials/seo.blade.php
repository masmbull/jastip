{{--
  Sumber tunggal meta SEO untuk halaman publik.
  Dipakai layouts/app.blade.php dengan variabel opsional:
    $fullTitle, $pageDescription, $ogImage, $ogType, $canonical, $noindex
  Halaman bisa menimpanya: @section('title') / $metaDescription / $ogImage.
--}}
@php
    $brand = setting('brand_name', 'NITIP DI END');
    $title = trim($fullTitle ?? $brand);
    $description = trim((string) ($pageDescription ?? ''));
    $canonical = $canonical ?? url()->current();
    $type = $ogType ?? 'website';
    $image = $ogImage ?? (setting('homepage_hero')
        ? asset('storage/'.setting('homepage_hero'))
        : asset('images/icons/icon-512.png'));
    $twitterSite = setting('social_twitter');
@endphp

<title>{{ $title }}</title>

@if($description !== '')
    <meta name="description" content="{{ $description }}">
@endif

@if(setting('meta_keywords'))
    <meta name="keywords" content="{{ setting('meta_keywords') }}">
@endif

@if($noindex ?? false)
    <meta name="robots" content="noindex, nofollow">
@else
    <meta name="robots" content="index, follow, max-image-preview:large">
@endif

<link rel="canonical" href="{{ $canonical }}">

{{-- Open Graph --}}
<meta property="og:type" content="{{ $type }}">
<meta property="og:site_name" content="{{ $brand }}">
<meta property="og:locale" content="id_ID">
<meta property="og:title" content="{{ $title }}">
@if($description !== '')
    <meta property="og:description" content="{{ $description }}">
@endif
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:image:alt" content="{{ $title }}">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
@if($description !== '')
    <meta name="twitter:description" content="{{ $description }}">
@endif
<meta name="twitter:image" content="{{ $image }}">
@if($twitterSite)
    <meta name="twitter:site" content="{{ $twitterSite }}">
@endif

@stack('seo')