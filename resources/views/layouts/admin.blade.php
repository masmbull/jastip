<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', setting('brand_name', 'NITIP DI END'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PWA: admin ikut installable (istri akses dari sini di iPhone). --}}
    <meta name="theme-color" content="#0891B2">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ setting('brand_name', 'NITIP DI END') }}">
    <link rel="manifest" href="{{ asset('manifest-admin.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/apple-touch-icon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-colors')
</head>
<body class="bg-[#FAF6EF] text-[#141414] font-sans antialiased min-h-screen">
    {{-- Skip to content --}}
    <a href="#admin-main"
       class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[999] focus:px-4 focus:py-2 focus:bg-[#FFC800] focus:text-[#141414] focus:font-bold focus:border-2 focus:border-[#141414] focus:rounded-[8px]">
        Lewati ke konten
    </a>

{{-- Loading splash: self-removing, no Alpine dependency, so it cannot get
     stuck when the Alpine CDN is slow or the load event already fired. --}}
<div id="jd-splash"
     style="z-index: 9999"
     class="fixed inset-0 flex items-center justify-center bg-[#FAF6EF] dark:bg-[#121212] transition-opacity duration-300">
    <div class="flex flex-col items-center gap-3">
        <div class="w-12 h-12 border-[3px] border-[#141414] dark:border-[#FAF6EF] border-t-[#FFC800] rounded-[12px] animate-spin"></div>
        <span class="text-sm font-bold text-[#141414] dark:text-[#FAF6EF]">Memuat panel...</span>
    </div>
</div>
<script>
    (function () {
        function hideSplash() {
            var s = document.getElementById('jd-splash');
            if (!s) return;
            s.style.opacity = '0';
            setTimeout(function () { s.remove(); }, 500);
        }
        if (document.readyState === 'complete') hideSplash();
        else window.addEventListener('load', hideSplash);
        setTimeout(hideSplash, 1500); // safety net if 'load' never fires (blocked CDN)
    })();
</script>

<div class="flex h-screen">

    {{-- Sidebar --}}
    <aside id="sidebar"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-[#FAF6EF] border-r-2 border-[#141414] -translate-x-full transition-transform duration-200 ease-in-out lg:static lg:h-screen lg:shrink-0 lg:translate-x-0">

        {{-- Logo --}}
        <div class="flex items-center justify-between p-5 border-b-2 border-[#141414] dark:border-[#FAF6EF]">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 bg-[#FFC800] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] flex items-center justify-center" style="box-shadow: 3px 3px 0 #141414">
                    <span class="text-[#141414] font-black text-xl">{{ strtoupper(substr(setting('brand_name', 'NITIP DI END'), 0, 1)) }}</span>
                </div>
                <div>
                    <span class="font-black text-[17px] leading-tight text-[#141414] dark:text-[#FAF6EF]">{{ setting('brand_name', 'NITIP DI END') }}</span>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#0E7490] dark:text-[#22D3EE] block">Admin Panel</span>
                </div>
            </div>
            <button type="button" @click="sidebarOpen = false"
                    class="lg:hidden text-[#141414] dark:text-[#FAF6EF] p-2 min-w-[44px] min-h-[44px] flex items-center justify-center"
                    aria-label="Tutup menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
        </div>

        {{-- Garis aksen tunggal (dulu strip pelangi 7 warna: terlalu ramai). --}}
        <div class="h-1.5 shrink-0 bg-[#FFC800] border-b-2 border-[#141414]" aria-hidden="true"></div>

        @include('layouts.partials.admin-nav')

        {{-- Footer --}}
        <div class="shrink-0 border-t-2 border-[#141414] dark:border-[#FAF6EF] p-4 bg-white dark:bg-[#1E1E1E] transition-colors">
            <div class="flex items-center justify-between text-xs">
                <span class="px-3 py-1.5 rounded-md bg-[#FFC800] border-2 border-[#141414] dark:border-[#FAF6EF] font-extrabold text-[#141414]">{{ setting('brand_name', 'NITIP DI END') }}</span>
                <span class="font-mono text-[#475569] dark:text-[#D6D3D1]">v1.0</span>
            </div>
        </div>
    </aside>

    {{-- Overlay for mobile --}}
    <div id="overlay"
         x-show="sidebarOpen"
         x-cloak
         x-transition.opacity
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/40 z-20 lg:hidden"></div>

    {{-- Main Content Area --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Top Bar --}}
        <header class="bg-white dark:bg-[#1E1E1E] border-b-2 border-[#141414] dark:border-[#FAF6EF] px-4 sm:px-6 py-3 flex items-center justify-between transition-colors">
            <div class="flex items-center space-x-4">
                <button type="button" @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden text-[#141414] dark:text-[#FAF6EF] p-2 -ml-1 rounded-[8px] min-w-[44px] min-h-[44px] flex items-center justify-center transition-colors"
                        aria-label="Buka menu">
                    <x-icon name="menu" class="w-5 h-5" />
                </button>
                <div class="hidden sm:flex items-center gap-2 text-sm font-bold text-[#141414] dark:text-[#FAF6EF]">
                    <x-icon name="envelope" class="w-4 h-4" />
                    <span>{{ setting('whatsapp', '08123456789') }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                <span class="hidden sm:inline text-sm font-bold text-[#141414] dark:text-[#FAF6EF]">Halo, Admin</span>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                            class="nb-btn px-3 py-1.5 text-sm bg-[#0E7490] text-white">
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        {{-- Page Content --}}
        <main id="admin-main" class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>
</div>

<x-toast />
<x-confirm-modal />

{{-- Pratinjau invoice: modal berisi iframe + tombol cetak. Hanya lembar
     invoice yang tercetak (print dari dalam iframe), bukan seluruh panel.
     Dipicu event `jd-invoice` dari tombol Cetak. --}}
<div x-data="jdInvoiceModal()" x-cloak
     @jd-invoice.window="open($event.detail)"
     @keydown.escape.window="show && close()">
    <template x-if="show">
        <div class="fixed inset-0 z-[400] flex items-stretch sm:items-center justify-center sm:p-6">
            <div class="absolute inset-0 bg-black/60" @click="close()"></div>
            <div class="relative w-full h-full sm:h-[88vh] sm:max-w-3xl sm:max-h-[92vh] bg-[#FAF6EF] dark:bg-[#121212] sm:rounded-[12px] border-2 border-[#141414] dark:border-[#FAF6EF] overflow-hidden flex flex-col"
                 x-data="{ loaded: false }"
                 @click.stop>

                {{-- Header --}}
                <div class="shrink-0 px-4 sm:px-5 py-3 border-b-2 border-[#141414] dark:border-[#FAF6EF] flex items-center justify-between bg-white dark:bg-[#1E1E1E]">
                    <div class="min-w-0">
                        <h3 class="text-base font-black text-[#141414] dark:text-[#FAF6EF] truncate" x-text="title"></h3>
                        <p class="text-xs text-[#475569] dark:text-[#D6D3D1] mt-0.5 font-medium">Pratinjau invoice. Tekan Cetak untuk simpan PDF.</p>
                    </div>
                    <button type="button" @click="close()" aria-label="Tutup"
                            class="shrink-0 ml-3 text-[#141414] dark:text-[#FAF6EF] p-2 min-w-[44px] min-h-[44px] flex items-center justify-center">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Body --}}
                <div class="flex-1 relative bg-[#FAF6EF] dark:bg-[#121212]">
                    <div x-show="!loaded" class="absolute inset-0 flex items-center justify-center text-[#475569] dark:text-[#D6D3D1] text-sm font-bold">
                        <x-spinner label="Memuat invoice…" />
                    </div>
                    <iframe x-ref="frame" :src="url" x-show="loaded" @load="loaded = true; $refs.frame.focus()"
                            class="w-full h-full border-0" title="Pratinjau invoice"></iframe>
                </div>

                {{-- Footer actions --}}
                <div class="shrink-0 px-4 sm:px-5 py-3 border-t-2 border-[#141414] dark:border-[#FAF6EF] flex items-center justify-between gap-3 bg-white dark:bg-[#1E1E1E]">
                    <a :href="url" target="_blank" rel="noopener"
                       class="text-sm text-[#0E7490] dark:text-[#22D3EE] font-bold underline underline-offset-2">
                        Buka di tab
                    </a>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="close()"
                                class="nb-btn px-4 py-2 text-sm bg-white dark:bg-[#1E1E1E] text-[#141414] dark:text-[#FAF6EF]">
                            Tutup
                        </button>
                        <button type="button" @click="print()"
                                class="nb-btn px-4 py-2 text-sm bg-[#0E7490] text-white">
                            <x-icon name="printer" class="w-4 h-4" /> Cetak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

{{-- Pasang sebagai aplikasi (PWA): tombol install Chromium + petunjuk iOS. --}}
<x-pwa-install />

{{-- Quick-view modal: dispatches on `quickview` event, fetches a Blade partial via fetch() --}}
<div x-data="jdQuickview()"
     @quickview.window="open($event.detail)"
     @keydown.escape.window="show = false"
     x-cloak>
    <template x-if="show">
        <div class="fixed inset-0 z-[400] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="show = false"></div>
            <div class="relative bg-white dark:bg-[#1E1E1E] rounded-[12px] border-2 border-[#141414] dark:border-[#FAF6EF] w-full max-w-3xl mx-4 my-8 overflow-hidden flex flex-col max-h-[85vh] transition-colors"
                 @click.stop>
                <div class="p-4 border-b-2 border-[#141414] dark:border-[#FAF6EF] flex items-center justify-between bg-white dark:bg-[#1E1E1E]">
                    <h3 class="text-lg font-black text-[#141414] dark:text-[#FAF6EF]" x-text="title"></h3>
                    <button type="button"
                            @click="show = false"
                            class="text-[#141414] dark:text-[#FAF6EF] p-2 min-w-[44px] min-h-[44px] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"></path>
                        </svg>
                    </button>
                </div>
                <div class="overflow-y-auto flex-1 p-6 bg-white dark:bg-[#1E1E1E]" x-html="html"></div>
            </div>
        </div>
    </template>
</div>

{{-- Back to top (scroll-triggered) --}}
<div x-data="{show:false,
             init(){addEventListener('scroll',()=>this.show=scrollY>420); addEventListener('scrollend',()=>this.show=scrollY>420)}}"
     x-show="show"
     x-transition
     x-cloak
     class="fixed bottom-6 right-6 z-[500]">
    <button type="button"
            @click="window.scrollTo({top:0, behavior:'smooth'})"
            aria-label="Kembali ke atas"
            class="nb-btn w-11 h-11 bg-[#FFC800] text-[#141414]">
        <x-icon name="chevron-down" class="w-5 h-5 rotate-180" />
    </button>
</div>

@stack('scripts')
</body>
</html>
