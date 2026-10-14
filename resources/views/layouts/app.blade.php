<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#0891B2">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="NITIP DI END">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $brandName = setting('brand_name', 'NITIP DI END');
        $sectionTitle = app('view')->getSection('title');
        $fullTitle = $sectionTitle ? ($sectionTitle . ' | ' . $brandName) : ($brandName . ': Titipan Spesial');
        $pageDescription = $metaDescription ?? setting('meta_description', 'Temukan berbagai produk impian harga terbaik, gratis ongkir di atas Rp150.000, COD, dan garansi autentik 100%.');
    @endphp
    <title>{{ $fullTitle }}</title>

    @if ($pageDescription)
        <meta name="description" content="{{ $pageDescription }}">
    @endif

    @if (setting('meta_keywords'))
        <meta name="keywords" content="{{ setting('meta_keywords') }}">
    @endif

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $ogImage ?? (setting('homepage_hero') ? asset('storage/' . setting('homepage_hero')) : asset('images/icons/icon-512.png')) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ setting('brand_name', 'NITIP DI END') }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">

    <!-- Favicon -->
    @php
        $faviconUrl = setting('brand_favicon') ? asset('storage/' . setting('brand_favicon')) : asset('images/icons/favicon-16.png');
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}" type="image/png" sizes="16x16">
    <link rel="icon" href="{{ asset('images/icons/favicon-32.png') }}" type="image/png" sizes="32x32">

    <!-- PWA -->
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/apple-touch-icon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-colors')
    {{-- Service worker is registered once from resources/js/app.js (/sw.js).
         Do NOT register a second one here: two SWs at scope "/" fight over
         the cache and a cache-first one serves stale HTML pointing at old
         build hashes (breaks styles + Alpine site-wide). --}}

</head>
<body class="bg-[#FAF6EF] text-[#141414] font-sans antialiased min-h-screen flex flex-col"
      x-data="{ mobileMenuOpen: false, searchOpen: false }"
      x-cloak>

        {{-- Skip to content --}}
        <a href="#main-content" class="sr-only focus:not-sr-only absolute top-4 left-4 z-50 nb-btn px-4 py-2 text-sm bg-[#FFC800] text-[#141414]">
            Lewati ke konten
        </a>

        {{-- NAVBAR --}}
        <x-navbar />

        {{-- MAIN CONTENT --}}
                <main id="main-content" class="flex-1 pb-20 lg:pb-0">
            @yield('content')
        </main>

        {{-- FOOTER --}}
        <x-footer />

        {{-- WHATSAPP FLOATING BUTTON --}}
        <x-whatsapp-floating-button />

        {{-- Toast + Confirm --}}
        <x-toast />
        <x-confirm-modal />

        {{-- Pasang sebagai aplikasi (PWA) --}}
        <x-pwa-install />

        {{-- Mobile menu overlay --}}
        <div x-show="mobileMenuOpen"
             x-transition.opacity
             class="fixed inset-0 bg-black/30 z-40 lg:hidden"
             @click="mobileMenuOpen = false"></div>

        {{-- Search drawer --}}
        <div x-show="searchOpen"
             x-transition
             x-cloak
             class="fixed inset-x-0 top-16 z-[200] bg-white dark:bg-[#1E1E1E] border-b-2 border-[#141414] dark:border-[#FAF6EF]">
            <form method="GET" action="{{ route('products.index') }}"
                  @submit="searchOpen = false"
                  role="search"
                  class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex gap-2">
                <label for="nav-search" class="sr-only">Cari produk</label>
                <input type="search" id="nav-search" name="q" placeholder="Cari produk..."
                       autocomplete="off"
                       class="nb-input flex-1 px-4 py-2.5 text-sm">
                <button type="submit"
                        class="nb-btn px-5 py-2.5 text-sm bg-[#0E7490] text-white">
                    Cari
                </button>
                <button type="button" @click="searchOpen = false"
                        aria-label="Tutup pencarian" title="Tutup"
                        class="nb-btn px-3 py-2.5 bg-white dark:bg-[#262626]">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </form>
        </div>

    </body>
</html>