<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Beranda') — {{ setting('brand_name', 'NITIP DI END') }} | {{ setting('brand_tagline', 'EH, NITIP DONG!') }}</title>

        @if (($metaDescription ?? setting('meta_description')))
            <meta name="description" content="{{ $metaDescription ?? setting('meta_description') }}">
        @endif

        @if (setting('meta_keywords'))
            <meta name="keywords" content="{{ setting('meta_keywords') }}">
        @endif

        <!-- Open Graph -->
        <meta property="og:title" content="{{ ($title ?? null) . ' — ' . setting('brand_name', 'NITIP DI END') }}">
        <meta property="og:description" content="{{ $metaDescription ?? setting('meta_description') }}">
        <meta property="og:image" content="{{ $ogImage ?? asset('images/og-image.jpg') }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="{{ setting('brand_name', 'NITIP DI END') }}">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ ($title ?? null) . ' — ' . setting('brand_name', 'NITIP DI END') }}">

        <!-- Favicon -->
        @php
            $faviconUrl = setting('brand_favicon') ? asset('storage/' . setting('brand_favicon')) : asset('favicon.png');
        @endphp
        <link rel="icon" href="{{ $faviconUrl }}" type="image/png">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js" defer></script>
    </head>
    <body x-data="{ mobileMenuOpen: false, cartOpen: false }" class="bg-[#FAF7F2] text-[#333333] font-sans antialiased">

        {{-- Skip to content --}}
        <a href="#main-content" class="sr-only focus:not-sr-only absolute top-4 left-4 z-50 bg-rose-500 text-white px-4 py-2 rounded">
            Skip to main content
        </a>

        {{-- NAVBAR --}}
        <x-navbar />

        {{-- MAIN CONTENT --}}
        <main id="main-content" class="pb-20 md:pb-0 min-h-[calc(100vh-200px)]">
        @yield('content')
    </main>

        {{-- FOOTER --}}
        <x-footer />

        {{-- WHATSAPP FLOATING BUTTON --}}
        <x-whatsapp-floating-button />

        <x-toast />

        {{-- Loading Overlay --}}
        <div x-show="cartOpen || mobileMenuOpen" x-transition.opacity class="fixed inset-0 bg-black/30 z-40 lg:hidden hidden"
             @click="cartOpen = false; mobileMenuOpen = false"></div>

    </body>
</html>
