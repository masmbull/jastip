<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false, darkMode: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches) }" :class="{ 'dark': darkMode }" @theme-toggle.window="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" @keydown.escape.window="sidebarOpen = false">
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

    <script>
        /* Prevent FOUC - set dark mode before styles load */
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white dark:bg-[#1a1a1a] text-[#1E293B] dark:text-[#f1f5f9] font-sans antialiased min-h-screen transition-colors duration-300">

{{-- Loading splash: self-removing, no Alpine dependency, so it cannot get
     stuck when the Alpine CDN is slow or the load event already fired. --}}
<div id="jd-splash"
     style="z-index: 9999"
     class="fixed inset-0 flex items-center justify-center bg-white dark:bg-[#1a1a1a] transition-opacity duration-500">
    <div class="flex flex-col items-center gap-3">
        <div class="w-12 h-12 border-2 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
        <span class="text-sm text-[#94A3B8]">Memuat panel...</span>
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
           class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-white dark:bg-[#23252b] border-r border-[#E2E8F0] dark:border-[#404854] shadow-lg dark:shadow-none -translate-x-full transition-transform duration-200 ease-in-out lg:static lg:h-screen lg:shrink-0 lg:translate-x-0 lg:shadow-none">

        {{-- Logo --}}
        <div class="flex items-center justify-between p-6 border-b border-[#E2E8F0] dark:border-[#404854]">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 bg-orange-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-xl">{{ strtoupper(substr(setting('brand_name', 'NITIP DI END'), 0, 1)) }}</span>
                </div>
                <div>
                    <span class="font-bold text-lg text-[#1E293B] dark:text-[#f1f5f9]">{{ setting('brand_name', 'NITIP DI END') }}</span>
                    <span class="text-xs text-orange-500 block">Admin Panel</span>
                </div>
            </div>
            <button type="button" @click="sidebarOpen = false"
                    class="lg:hidden text-[#94A3B8] hover:text-[#1E293B] dark:hover:text-[#f1f5f9] p-1"
                    aria-label="Tutup menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
        </div>


        @include('layouts.partials.admin-nav')

        {{-- Footer --}}
        <div class="shrink-0 border-t border-[#E2E8F0] dark:border-[#404854] p-4 bg-white dark:bg-[#23252b] transition-colors">
            <div class="flex items-center justify-between text-xs text-[#94A3B8] dark:text-[#cbd5e1]">
                <span class="px-4 py-2 rounded-lg bg-orange-50 dark:bg-orange-500/20 font-medium text-orange-600 dark:text-orange-400">{{ setting('brand_name', 'NITIP DI END') }}</span>
                <span>v1.0</span>
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
        <header class="bg-white dark:bg-[#23252b] border-b border-[#E2E8F0] dark:border-[#404854] px-4 sm:px-6 py-3 flex items-center justify-between transition-colors">
            <div class="flex items-center space-x-4">
                <button type="button" @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 p-2 -ml-1 rounded-lg transition-colors"
                        aria-label="Buka menu">
                    <x-icon name="menu" class="w-5 h-5" />
                </button>
                <div class="hidden sm:flex items-center gap-2 text-sm text-[#64748B] dark:text-[#cbd5e1]">
                    <x-icon name="envelope" class="w-4 h-4" />
                    <span>{{ setting('whatsapp', '08123456789') }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                <span class="hidden sm:inline text-sm text-[#64748B] dark:text-[#cbd5e1]">Halo, Admin</span>

                {{-- Dark-mode toggle --}}
                <button type="button"
                        @click="$dispatch('theme-toggle')"
                        class="p-2 rounded-lg text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] transition-colors"
                        title="Ganti mode gelap" aria-label="Ganti mode gelap">
                    <svg x-show="!document.documentElement.classList.contains('dark')" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12.75a9 9 0 1 1-9.25-9.25 7 7 0 0 0 9.25 9.25z"></path>
                    </svg>
                    <svg x-show="document.documentElement.classList.contains('dark')" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v2m0 14v2m8.66-10.66-1.42 1.42M4.76 4.76l1.42 1.42M19 12h2M3 12h2m14.66 4.66-1.42 1.42M4.76 19.24l1.42-1.42"></path>
                    </svg>
                </button>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                            class="px-3 py-1.5 text-sm bg-orange-500 hover:bg-orange-600 text-white rounded-lg shadow-sm">
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 animate-fade-up">
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
            <div class="relative w-full h-full sm:h-[88vh] sm:max-w-3xl sm:max-h-[92vh] bg-[#F1F5F9] dark:bg-[#1a1a1a] sm:rounded-2xl shadow-2xl overflow-hidden flex flex-col animate-scale-in"
                 x-data="{ loaded: false }"
                 @click.stop>

                {{-- Header --}}
                <div class="shrink-0 px-4 sm:px-5 py-3 border-b border-[#E2E8F0] dark:border-[#404854] flex items-center justify-between bg-white dark:bg-[#23252b]">
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-[#1E293B] dark:text-[#f1f5f9] truncate" x-text="title"></h3>
                        <p class="text-xs text-[#94A3B8] mt-0.5">Pratinjau invoice. Tekan Cetak untuk simpan PDF.</p>
                    </div>
                    <button type="button" @click="close()" aria-label="Tutup"
                            class="shrink-0 ml-3 text-[#94A3B8] dark:text-[#cbd5e1] hover:text-[#1E293B] dark:hover:text-[#f1f5f9] p-1.5 rounded-lg hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] transition-colors">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Body --}}
                <div class="flex-1 relative bg-[#E9EEF3] dark:bg-[#121212]">
                    <div x-show="!loaded" class="absolute inset-0 flex items-center justify-center text-[#64748B] dark:text-[#cbd5e1] text-sm">
                        <x-spinner label="Memuat invoice…" />
                    </div>
                    <iframe x-ref="frame" :src="url" x-show="loaded" @load="loaded = true; $refs.frame.focus()"
                            class="w-full h-full border-0" title="Pratinjau invoice"></iframe>
                </div>

                {{-- Footer actions --}}
                <div class="shrink-0 px-4 sm:px-5 py-3 border-t border-[#E2E8F0] dark:border-[#404854] flex items-center justify-between gap-3 bg-white dark:bg-[#23252b]">
                    <a :href="url" target="_blank" rel="noopener"
                       class="text-sm text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 font-medium transition-colors">
                        Buka di tab
                    </a>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="close()"
                                class="px-4 py-2 text-sm text-[#64748B] dark:text-[#cbd5e1] hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] rounded-lg font-medium transition-colors">
                            Tutup
                        </button>
                        <button type="button" @click="print()"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-[#0891B2] hover:bg-[#0E7490] text-white font-medium rounded-lg shadow-sm transition-colors">
                            <x-icon name="printer" class="w-4 h-4" /> Cetak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

{{-- iOS (iPhone) tak punya prompt install otomatis: tampilkan petunjuk
     "Add to Home Screen" sekali, hanya di Safari iOS dan bila belum standalone. --}}
<div x-data="{
        show: false,
        init() {
            const standalone = window.navigator.standalone === true || window.matchMedia('(display-mode: standalone)').matches;
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
            const dismissed = localStorage.getItem('a2hs-dismissed') === '1';
            this.show = isIOS && !standalone && !dismissed;
        },
        dismiss() { this.show = false; localStorage.setItem('a2hs-dismissed', '1'); }
     }"
     x-show="show" x-cloak x-transition
     class="fixed inset-x-3 bottom-3 z-[450] sm:left-auto sm:right-6 sm:max-w-sm">
    <div class="bg-[#1E293B] dark:bg-[#23252b] text-white rounded-xl shadow-xl p-4 flex items-start gap-3 border border-white/10">
        <x-icon name="download" class="w-5 h-5 shrink-0 mt-0.5 text-orange-400" />
        <div class="text-sm">
            <p class="font-medium">Pasang sebagai aplikasi</p>
            <p class="text-white/70 mt-0.5">Ketuk <span class="font-medium">Bagikan</span> lalu <span class="font-medium">Tambah ke Layar Utama</span> supaya kebuka seperti app.</p>
        </div>
        <button type="button" @click="dismiss()" class="shrink-0 text-white/60 hover:text-white p-0.5" aria-label="Tutup">
            <x-icon name="x" class="w-4 h-4" />
        </button>
    </div>
</div>

{{-- Quick-view modal: dispatches on `quickview` event, fetches a Blade partial via fetch() --}}
<div x-data="jdQuickview()"
     @quickview.window="open($event.detail)"
     @keydown.escape.window="show = false"
     x-cloak>
    <template x-if="show">
        <div class="fixed inset-0 z-[400] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="show = false"></div>
            <div class="relative bg-white dark:bg-[#23252b] rounded-xl shadow-xl w-full max-w-3xl mx-4 my-8 overflow-hidden flex flex-col max-h-[85vh] animate-scale-in transition-colors"
                 @click.stop>
                <div class="p-4 border-b border-[#E2E8F0] dark:border-[#404854] flex items-center justify-between bg-white dark:bg-[#23252b]">
                    <h3 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9]" x-text="title"></h3>
                    <button type="button"
                            @click="show = false"
                            class="text-[#94A3B8] dark:text-[#cbd5e1] hover:text-[#1E293B] dark:hover:text-[#f1f5f9] p-1 rounded-lg hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"></path>
                        </svg>
                    </button>
                </div>
                <div class="overflow-y-auto flex-1 p-6 bg-white dark:bg-[#23252b]" x-html="html"></div>
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
            class="w-11 h-11 rounded-full bg-orange-500 hover:bg-orange-600 text-white shadow-lg flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-orange-300">
        <x-icon name="chevron-down" class="w-5 h-5 rotate-180" />
    </button>
</div>

@stack('scripts')
</body>
</html>
