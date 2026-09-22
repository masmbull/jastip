<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', setting('brand_name', 'NITIP DI END'))</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        /* Apply saved / system dark preference BEFORE styles load (no FOUC). */
        (function () {
            if (localStorage.theme === 'dark' ||
                (!localStorage.theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js" defer></script>
</head>
<body class="bg-[#FDF6EC] text-[#1E293B] font-sans antialiased min-h-screen">

{{-- Loading splash: fades out on full load via Alpine (uses existing x-cloak + animate-spin). --}}
<div id="jd-splash"
     x-data="{ ready: false }"
     x-init="window.addEventListener('load', () => { ready = true })"
     x-show="!ready"
     x-transition:leave="transition ease-out duration-500"
     x-transition:leave-end="opacity-0"
          x-cloak
     style="z-index: 9999"
     class="fixed inset-0 flex items-center justify-center bg-white">
    <div class="flex flex-col items-center gap-3">
        <div class="w-12 h-12 border-2 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
        <span class="text-sm text-[#94A3B8]">Memuat panel…</span>
    </div>
</div>

<div class="flex h-screen">

    {{-- Sidebar --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-white border-r border-[#E2E8F0] shadow-lg -translate-x-full transition-transform duration-200 ease-in-out lg:static lg:h-screen lg:shrink-0 lg:translate-x-0 lg:shadow-none">

        {{-- Logo --}}
        <div class="flex items-center justify-between p-6 border-b border-[#E2E8F0]">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 bg-orange-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-xl">{{ strtoupper(substr(setting('brand_name', 'NITIP DI END'), 0, 1)) }}</span>
                </div>
                <div>
                    <span class="font-bold text-lg text-[#1E293B]">{{ setting('brand_name', 'NITIP DI END') }}</span>
                    <span class="text-xs text-orange-500 block">Admin Panel</span>
                </div>
            </div>
            <button onclick="document.getElementById('sidebar').classList.add('-translate-x-full')"
                    class="lg:hidden text-[#94A3B8] hover:text-[#1E293B] p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto p-4">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-orange-50 text-orange-600 font-medium">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25ZM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25Z"></path>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.products.index') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg text-[#64748B] hover:bg-orange-50 hover:text-orange-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path>
                </svg>
                <span>Produk</span>
            </a>

                        <a href="{{ route('admin.orders.index') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg text-[#64748B] hover:bg-orange-50 hover:text-orange-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0Zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0Z"></path>
                </svg>
                <span>Pesanan</span>
            </a>

            <a href="{{ route('admin.settings.index') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg text-[#64748B] hover:bg-orange-50 hover:text-orange-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0Z"></path>
                </svg>
                <span>Pengaturan</span>
            </a>
        </nav>

        {{-- Footer --}}
        <div class="shrink-0 border-t border-[#E2E8F0] p-4">
            <div class="flex items-center justify-between text-xs text-[#94A3B8]">
                <span class="px-4 py-2 rounded-lg bg-orange-50 font-medium text-orange-600">{{ setting('brand_name', 'NITIP DI END') }}</span>
                <span>v1.0</span>
            </div>
        </div>
    </aside>

    {{-- Overlay for mobile --}}
    <div id="overlay"
         onclick="document.getElementById('sidebar').classList.add('-translate-x-full')"
         class="fixed inset-0 bg-black/40 z-20 hidden lg:hidden"></div>

    {{-- Main Content Area --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Top Bar --}}
        <header class="bg-white border-b border-[#E2E8F0] px-4 sm:px-6 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <button onclick="document.getElementById('sidebar').classList.remove('-translate-x-full')"
                        class="lg:hidden text-[#94A3B8] hover:text-[#1E293B] p-1 -ml-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="hidden sm:flex items-center space-x-2 text-sm text-[#94A3B8]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ setting('whatsapp', '08123456789') }}</span>
                </div>
            </div>
                        <div class="flex items-center space-x-3">
                <span class="text-sm text-[#94A3B8]">Halo, Admin</span>

                {{-- Dark-mode toggle (persists to localStorage, no DB write) --}}
                <button type="button"
                        x-data=""
                        @click="document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light'"
                        class="p-1.5 rounded-lg text-[#64748B] hover:text-orange-600 hover:bg-[#F1F5F9]" title="Dark mode">
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

{{-- Quick-view modal: dispatches on `quickview` event, fetches a Blade partial via fetch() --}}
<div x-data="jdQuickview()"
     @quickview.window="open($event.detail)"
     @keydown.escape.window="show = false"
     x-cloak>
    <template x-if="show">
        <div class="fixed inset-0 z-[400] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="show = false"></div>
            <div class="relative bg-[#F8FAFC] rounded-xl shadow-xl w-full max-w-3xl mx-4 my-8 overflow-hidden flex flex-col max-h-[85vh] animate-scale-in"
                 @click.stop>
                <div class="p-4 border-b border-[#E2E8F0] flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-[#1E293B]" x-text="title"></h3>
                    <button type="button"
                            @click="show = false"
                            class="text-[#94A3B8] hover:text-[#1E293B] p-1 rounded-lg hover:bg-[#F1F5F9]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"></path>
                        </svg>
                    </button>
                </div>
                <div class="overflow-y-auto flex-1 p-6" x-html="html"></div>
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
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7 7 7v4"></path>
        </svg>
    </button>
</div>

@stack('scripts')
</body>
</html>