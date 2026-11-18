<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#FFC800">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ setting('brand_name', 'NITIP DI END') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $brandName = setting('brand_name', 'NITIP DI END');
        // getSection() mengembalikan isi section yang SUDAH di-escape Blade.
        // Tanpa html_entity_decode, `&` di judul jadi `&amp;amp;` saat
        // di-escape lagi oleh {{ }} di partials.seo.
        $sectionTitle = html_entity_decode((string) app('view')->getSection('title'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $fullTitle = $sectionTitle !== '' ? ($sectionTitle . ' | ' . $brandName) : ($brandName . ': Titipan Spesial');
        $pageDescription = $metaDescription ?? setting('meta_description', 'Temukan berbagai produk impian harga terbaik, gratis ongkir di atas Rp150.000, COD, dan garansi autentik 100%.');
        // Halaman produk/viral sering set $ogImage; partial SEO baca $metaImage.
        $metaImage = $metaImage ?? $ogImage ?? null;
        $metaKeywords = $metaKeywords ?? setting('meta_keywords');
        $appleTitle = $brandName;
    @endphp

    @include('layouts.partials.seo')

    <!-- Favicon -->
    @php
        $faviconUrl = media_url(setting('brand_favicon')) ?? asset('images/icons/favicon-16.png');
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}" type="image/png" sizes="16x16">
    <link rel="icon" href="{{ asset('images/icons/favicon-32.png') }}" type="image/png" sizes="32x32">

    <!-- PWA -->
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500..800&family=DM+Sans:ital,opsz,wght@0,9..40,400..800;1,9..40,400..700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-colors')
    @include('components.seo-structured-data')
    @stack('seo')
    {{-- Service worker is registered once from resources/js/app.js (/sw.js). --}}

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
                        class="nb-btn px-5 py-2.5 text-sm bg-[#FFC800] text-[#141414]">
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