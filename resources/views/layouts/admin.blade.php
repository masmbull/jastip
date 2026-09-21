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
<body class="bg-[#f5f0e8] text-[#333333] font-sans antialiased min-h-screen">

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
        <div class="w-12 h-12 border-2 border-rose-500 border-t-transparent rounded-full animate-spin"></div>
        <span class="text-sm text-[#999999]">Memuat panel…</span>
    </div>
</div>

<div class="flex h-screen">

    {{-- Sidebar --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-[#E8E0D8] shadow-lg -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">

        {{-- Logo --}}
        <div class="flex items-center justify-between p-6 border-b border-[#E8E0D8]">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 bg-rose-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-xl">{{ strtoupper(substr(setting('brand_name', 'NITIP DI END'), 0, 1)) }}</span>
                </div>
                <div>
                    <span class="font-bold text-lg text-[#333333]">{{ setting('brand_name', 'NITIP DI END') }}</span>
                    <span class="text-xs text-rose-500 block">Admin Panel</span>
                </div>
            </div>
            <button onclick="document.getElementById('sidebar').classList.add('-translate-x-full')"
                    class="lg:hidden text-[#999999] hover:text-[#333333] p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="p-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-rose-50 text-rose-600 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"></path>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.products.index') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg text-[#666666] hover:bg-rose-50 hover:text-rose-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4v4H5v10h12v4l8-4v-4H5V7h12z"></path>
                </svg>
                <span>Produk</span>
            </a>

                        <a href="{{ route('admin.orders.index') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg text-[#666666] hover:bg-rose-50 hover:text-rose-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0 2 2 0 01-.001-2.828l7-7a2 2 0 012.828 0Z"></path>
                </svg>
                <span>Pesanan</span>
            </a>

            <a href="{{ route('admin.settings.index') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg text-[#666666] hover:bg-rose-50 hover:text-rose-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 003.35 0 1.724 1.724 0 003.35 0 1.724 1.724 0 003.35 0"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Pengaturan</span>
            </a>
        </nav>

        {{-- Footer --}}
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-[#E8E0D8]">
            <div class="flex items-center justify-between text-xs text-[#999999]">
                <span class="px-4 py-2 rounded-lg bg-rose-50 font-medium text-rose-600">{{ setting('brand_name', 'NITIP DI END') }}</span>
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
        <header class="bg-white border-b border-[#E8E0D8] px-4 sm:px-6 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <button onclick="document.getElementById('sidebar').classList.remove('-translate-x-full')"
                        class="lg:hidden text-[#999999] hover:text-[#333333] p-1 -ml-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="hidden sm:flex items-center space-x-2 text-sm text-[#999999]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ setting('whatsapp', '08123456789') }}</span>
                </div>
            </div>
                        <div class="flex items-center space-x-3">
                <span class="text-sm text-[#999999]">Halo, Admin</span>

                {{-- Dark-mode toggle (persists to localStorage, no DB write) --}}
                <button type="button"
                        x-data=""
                        @click="document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light'"
                        class="p-1.5 rounded-lg text-[#666666] hover:text-rose-600 hover:bg-[#F5F5F0]" title="Dark mode">
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
                            class="px-3 py-1.5 text-sm bg-rose-500 hover:bg-rose-600 text-white rounded-lg shadow-sm">
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
            <div class="relative bg-[#FAFAF8] rounded-xl shadow-xl w-full max-w-3xl mx-4 my-8 overflow-hidden flex flex-col max-h-[85vh] animate-scale-in"
                 @click.stop>
                <div class="p-4 border-b border-[#E8E0D8] flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-[#333333]" x-text="title"></h3>
                    <button type="button"
                            @click="show = false"
                            class="text-[#999999] hover:text-[#333333] p-1 rounded-lg hover:bg-[#F5F5F5]">
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
            class="w-11 h-11 rounded-full bg-rose-500 hover:bg-rose-600 text-white shadow-lg flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-rose-300">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7 7 7v4"></path>
        </svg>
    </button>
</div>

@stack('scripts')
</body>
</html>