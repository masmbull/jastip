@php
    $cartCount = app(\App\Services\CartService::class)->count();
    $cartSubtotal = app(\App\Services\CartService::class)->subtotal();
    $brandName = setting('brand_name', 'NITIP DI END');
    $brandLogo = setting('brand_logo') ? asset('storage/' . setting('brand_logo')) : null;
    $brandOwner = setting('brand_owner', 'Nabila Adriyana');
@endphp

<nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-[#E2E8F0]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo & Brand --}}
            <div class="flex-shrink-0 flex items-center space-x-3">
                @if($brandLogo)
                    <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-10 w-10 object-contain" loading="lazy" decoding="async">
                @else
                    <div class="h-10 w-10 rounded-full bg-orange-200 flex items-center justify-center">
                        <span class="text-orange-700 font-bold text-lg">N</span>
                    </div>
                @endif
                <div>
                    <span class="font-bold text-xl text-[#1E293B]">{{ $brandName }}</span>
                    <span class="block text-xs text-orange-500 -mt-1">{{ setting('brand_tagline', 'EH, NITIP DONG!') }}</span>
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
                           class="px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs($link['route']) ? 'text-orange-600 border-b-2 border-orange-500' : 'text-[#64748B] hover:text-orange-600' }}">
                            {{ $link['label'] }}
                        </a>
                    @else
                        <span class="px-3 py-2 text-sm font-medium text-[#94A3B8] cursor-default"
                              title="Route {{ $link['route'] }} belum tersedia">
                            {{ $link['label'] }}
                        </span>
                    @endif
                @endforeach
            </div>

            {{-- Desktop Right Side --}}
            <div class="flex items-center space-x-4">
                {{-- Search --}}
                <button @click="searchOpen = true" class="p-2 text-[#64748B] hover:text-orange-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a8 8 0 11-16 0 8 8 0 0116 0z"></path>
                    </svg>
                </button>

                                {{-- Cart --}}
                <a href="{{ route('cart.index') }}"
                   class="relative p-2 text-[#64748B] hover:text-orange-600 transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">
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
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-[#64748B] hover:text-orange-600 transition-colors">
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
    <div x-show="mobileMenuOpen" x-transition x-cloak class="md:hidden bg-white border-t border-[#E2E8F0] shadow-lg">
        <div class="px-2 pt-2 pb-3 space-y-1">
                        @foreach($links as $link)
                @if(\Route::has($link['route']))
                    <a href="{{ route($link['route']) }}"
                       class="block px-3 py-2 text-sm font-medium {{ request()->routeIs($link['route']) ? 'text-orange-600 bg-orange-50' : 'text-[#64748B] hover:bg-orange-50 hover:text-orange-600' }} rounded">
                        {{ $link['label'] }}
                    </a>
                @else
                    <span class="block px-3 py-2 text-sm font-medium text-[#94A3B8] cursor-default">
                        {{ $link['label'] }}
                    </span>
                @endif
            @endforeach
        </div>
    </div>
</nav>