@php
    $cartCount = app(\App\Services\CartService::class)->count();
    $cartSubtotal = app(\App\Services\CartService::class)->subtotal();
    $brandName = setting('brand_name', 'NITIP DI END');
    $brandLogo = setting('brand_logo') ? asset('storage/' . setting('brand_logo')) : null;
    $brandOwner = setting('brand_owner', 'Nabila Adriyana');
@endphp

<nav class="bg-white dark:bg-[#1a1a1a] dark:border-[#404854] backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-[#E2E8F0] transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo & Brand --}}
            <div class="flex-shrink-0 flex items-center space-x-3">
                @if($brandLogo)
                    <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-10 w-10 object-contain" loading="lazy" decoding="async">
                @else
                    <div class="h-10 w-10 rounded-full bg-orange-200 dark:bg-orange-500/20 flex items-center justify-center">
                        <span class="text-orange-700 dark:text-orange-400 font-bold text-lg">N</span>
                    </div>
                @endif
                <div>
                    <span class="font-bold text-xl text-[#1E293B] dark:text-[#f1f5f9]">{{ $brandName }}</span>
                    <span class="block text-xs text-orange-500 dark:text-orange-400 -mt-1">{{ setting('brand_tagline', 'EH, NITIP DONG!') }}</span>
                </div>
            </div>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex md:space-x-6">
                @php
                    $links = [
                        ['label' => 'Beranda', 'route' => 'home'],
                        ['label' => 'Produk', 'route' => 'products.index'],
                        ['label' => 'Produk Viral', 'route' => 'products.viral'],
                        ['label' => 'Kategori', 'route' => 'categories.index'],
                        ['label' => 'Cek Ongkir', 'route' => 'shipping.index'],
                        ['label' => 'Tentang', 'route' => 'about'],
                        ['label' => 'Cara Nitip', 'route' => 'how-to'],
                        ['label' => 'Kontak', 'route' => 'contact'],
                    ];
                @endphp
                                @foreach($links as $link)
                    @if(\Route::has($link['route']))
                        <a href="{{ route($link['route']) }}"
                           class="px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs($link['route']) ? 'text-orange-600 dark:text-orange-400 border-b-2 border-orange-500' : 'text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400' }}">
                            {{ $link['label'] }}
                        </a>
                    @else
                        <span class="px-3 py-2 text-sm font-medium text-[#94A3B8] dark:text-[#64748B] cursor-default"
                              title="Route {{ $link['route'] }} belum tersedia">
                            {{ $link['label'] }}
                        </span>
                    @endif
                @endforeach
            </div>

            {{-- Desktop Right Side --}}
            <div class="flex items-center space-x-4">
                {{-- Search --}}
                <button @click="searchOpen = true" class="p-2 text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a8 8 0 11-16 0 8 8 0 0116 0z"></path>
                    </svg>
                </button>

                {{-- Theme Toggle --}}
                <button type="button"
                        @click="$dispatch('theme-toggle')"
                        class="p-2 text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] rounded-lg transition-colors" title="Toggle dark mode">
                    <svg x-show="!document.documentElement.classList.contains('dark')" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1m-16 0H1m15.364 1.636l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <svg x-show="document.documentElement.classList.contains('dark')" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>

                                {{-- Cart --}}
                <a href="{{ route('cart.index') }}"
                   class="relative p-2 text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 11V7a4 4 0 00-8 0v4M8 11h8m-2 4h-4M8 15l-2 4h8l-2-4"></path>
                    </svg>
                    <span id="cart-count"
                          class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center {{ $cartCount > 0 ? '' : 'hidden' }}">
                        {{ $cartCount }}
                    </span>
                </a>

                {{-- Mobile Menu Button --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileMenuOpen" x-transition x-cloak class="md:hidden bg-white dark:bg-[#1a1a1a] border-t border-[#E2E8F0] dark:border-[#404854] shadow-lg transition-colors">
        <div class="px-2 pt-2 pb-3 space-y-1">
                        @foreach($links as $link)
                @if(\Route::has($link['route']))
                    <a href="{{ route($link['route']) }}"
                       class="block px-3 py-2 text-sm font-medium {{ request()->routeIs($link['route']) ? 'text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-500/10' : 'text-[#64748B] dark:text-[#cbd5e1] hover:bg-orange-50 dark:hover:bg-orange-500/10 hover:text-orange-600 dark:hover:text-orange-400' }} rounded transition-colors">
                        {{ $link['label'] }}
                    </a>
                @else
                    <span class="block px-3 py-2 text-sm font-medium text-[#94A3B8] dark:text-[#64748B] cursor-default">
                        {{ $link['label'] }}
                    </span>
                @endif
            @endforeach
        </div>
    </div>
</nav>