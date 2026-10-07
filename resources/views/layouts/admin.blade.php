<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches), sidebarOpen: false }" :class="{ 'dark': darkMode }" @theme-toggle.window="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')">
<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#F5A623">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 text-slate-900 dark:text-slate-100">
    <!-- Sidebar -->
    <aside class="fixed left-0 top-0 z-40 h-screen w-64 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 transform transition-transform duration-300"
           :class="{ '-translate-x-full md:translate-x-0': !sidebarOpen, 'translate-x-0': sidebarOpen }">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-bold text-xl text-orange-600 dark:text-orange-400">
                🏪 NITIP Admin
            </a>
        </div>

        <nav class="p-4 space-y-2 overflow-y-auto h-[calc(100vh-88px)]">
            <a href="{{ route('admin.dashboard') }}" 
               class="block px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                📊 Dashboard
            </a>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                <p class="px-4 py-2 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Manajemen</p>
                
                <a href="{{ route('admin.products.index') }}"
                   class="block px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.products.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    📦 Produk
                </a>

                <a href="{{ route('admin.categories.index') }}"
                   class="block px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.categories.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    🏷️ Kategori
                </a>

                <a href="{{ route('admin.orders.index') }}"
                   class="block px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.orders.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    🛍️ Pesanan
                </a>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                <p class="px-4 py-2 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Fitur</p>

                <a href="{{ route('admin.payment-proofs.index') }}"
                   class="block px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.payment-proofs.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    💳 Bukti Pembayaran
                </a>

                <a href="{{ route('admin.reviews.index') }}"
                   class="block px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.reviews.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    ⭐ Ulasan & Rating
                </a>

                <a href="{{ route('admin.shipping.index') }}"
                   class="block px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.shipping.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    🚚 Pengiriman
                </a>

                <a href="{{ route('admin.admin.kupon.index') }}"
                   class="block px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.admin.kupon.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    🎟️ Kupon
                </a>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                <p class="px-4 py-2 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Laporan & Tools</p>

                <a href="{{ route('admin.reports.sales') }}"
                   class="block px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.reports.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    📈 Laporan
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="block px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    👥 Pengguna
                </a>

                <a href="{{ route('admin.import.form') }}"
                   class="block px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.import.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    📥 Import Data
                </a>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                <p class="px-4 py-2 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Monitoring</p>

                <a href="{{ route('admin.services.index') }}"
                   class="block px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.services.*') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    📡 Service Monitoring
                </a>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                <p class="px-4 py-2 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Pengaturan</p>

                <a href="{{ route('admin.settings.index') }}"
                   class="block px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    ⚙️ Pengaturan Toko
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="md:ml-64">
        <!-- Top Bar -->
        <header class="sticky top-0 z-30 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between p-4 md:p-6">
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <div class="flex items-center gap-4">
                    <!-- Theme Toggle -->
                    <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')"
                            class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.121-10.121l.707-.707a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414zM4.464 4.465l.707-.707A1 1 0 003.757 2.343l-.707.707a1 1 0 001.414 1.414zM2.343 16.243l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zM20 11a1 1 0 110-2h-1a1 1 0 110 2h1zm-9 8a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5 10a1 1 0 110-2H4a1 1 0 110 2h1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    <!-- User Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg">
                            <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium hidden sm:inline">{{ auth()->user()->name }}</span>
                        </button>

                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg shadow-lg overflow-hidden">
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2.5 text-sm hover:bg-slate-100 dark:hover:bg-slate-600">
                                👤 Profile Saya
                            </a>
                            <form method="POST" action="{{ route('admin.logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-100 dark:hover:bg-slate-600 text-red-600 dark:text-red-400">
                                    🚪 Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 mt-12 p-6 text-center text-sm text-slate-600 dark:text-slate-400">
            <p>&copy; {{ date('Y') }} {{ setting('brand_name', 'NITIP DI END') }} Admin. Semua hak dilindungi.</p>
        </footer>
    </div>

    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>
