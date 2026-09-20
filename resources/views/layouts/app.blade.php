<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#f43f5e">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="NITIP DI END">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') — {{ setting('brand_name', 'NITIP DI END') }}</title>

    @if (($metaDescription ?? setting('meta_description')))
        <meta name="description" content="{{ $metaDescription ?? setting('meta_description') }}">
    @endif

    @if (setting('meta_keywords'))
        <meta name="keywords" content="{{ setting('meta_keywords') }}">
    @endif

    <!-- Open Graph -->
    <meta property="og:title" content="{{ ($title ?? null) . ' — ' . setting('brand_name', 'NITIP DI END') }}">
    <meta property="og:description" content="{{ $metaDescription ?? setting('meta_description') }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('images/icons/icon-512.png') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ setting('brand_name', 'NITIP DI END') }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ ($title ?? null) . ' — ' . setting('brand_name', 'NITIP DI END') }}">

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
    <script src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js" defer></script>
</head>
    <body class="bg-[#FAF7F2] text-[#333333] font-sans antialiased"
          x-data="{ mobileMenuOpen: false, searchOpen: false }"
          x-cloak>

        {{-- Skip to content --}}
        <a href="#main-content" class="sr-only focus:not-sr-only absolute top-4 left-4 z-50 bg-rose-500 text-white px-4 py-2 rounded">
            Skip to main content
        </a>

        {{-- NAVBAR --}}
        <x-navbar />

        {{-- MAIN CONTENT --}}
        <main id="main-content" class="pb-20 md:pb-0 min-h-[calc(100vh-200px)] animate-fade-up">
            @yield('content')
        </main>

        {{-- FOOTER --}}
        <x-footer />

        {{-- WHATSAPP FLOATING BUTTON --}}
        <x-whatsapp-floating-button />

        {{-- Toast + Confirm --}}
        <x-toast />
        <x-confirm-modal />

        {{-- Mobile menu overlay --}}
        <div x-show="mobileMenuOpen"
             x-transition.opacity
             class="fixed inset-0 bg-black/30 z-40 lg:hidden"
             @click="mobileMenuOpen = false"></div>

        {{-- Search drawer --}}
        <div x-show="searchOpen"
             x-transition
             x-cloak
             class="fixed inset-x-0 top-16 z-[200] bg-white/95 backdrop-blur border-b border-[#E8E0D8] shadow-lg">
            <form method="GET" action="{{ route('products.index') }}"
                  @submit="searchOpen = false"
                  class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex gap-2">
                <input type="search" name="q" placeholder="Cari produk..."
                       autocomplete="off"
                       class="flex-1 px-4 py-2.5 border border-[#E8E0D8] rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-300">
                <button type="submit"
                        class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-rose-300">
                    Cari
                </button>
                <button type="button" @click="searchOpen = false"
                        class="px-3 py-1 text-sm text-[#999999] hover:text-[#333333]">
                    ✕
                </button>
            </form>
        </div>

    </body>
</html>