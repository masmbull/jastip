@php
    $cartCount = app(\App\Services\CartService::class)->count();
    $cartSubtotal = app(\App\Services\CartService::class)->subtotal();
    $brandName = setting('brand_name', 'NITIP DI END');
    $brandLogo = setting('brand_logo') ? asset('storage/' . setting('brand_logo')) : null;
    $brandOwner = setting('brand_owner', 'Nabila Adriyana');
@endphp

<nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-[#E8E0D8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo & Brand --}}
            <div class="flex-shrink-0 flex items-center space-x-3">
                @if($brandLogo)
                    <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-10 w-10 object-contain">
                @else
                    <div class="h-10 w-10 rounded-full bg-rose-200 flex items-center justify-center">
                        <span class="text-rose-700 font-bold text-lg">N</span>
                    </div>
                @endif
                <div>
                    <span class="font-bold text-xl text-[#333333]">{{ $brandName }}</span>
                    <span class="block text-xs text-rose-500 -mt-1">{{ setting('brand_tagline', 'EH, NITIP DONG!') }}</span>
                </div>
            </div>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex md:space-x-8">
                <a href="{{ route('home') }}" class="text-[#333333] hover:text-rose-600 px-3 py-2 text-sm font-medium transition-colors">Beranda</a>
                <a href="{{ route('products.index') }}" class="text-[#666666] hover:text-rose-600 px-3 py-2 text-sm font-medium transition-colors">Produk</a>
                <a href="{{ route('categories.index') }}" class="text-[#666666] hover:text-rose-600 px-3 py-2 text-sm font-medium transition-colors">Kategori</a>
                <a href="{{ route('about') }}" class="text-[#666666] hover:text-rose-600 px-3 py-2 text-sm font-medium transition-colors">Tentang</a>
                <a href="{{ route('how-to') }}" class="text-[#666666] hover:text-rose-600 px-3 py-2 text-sm font-medium transition-colors">Cara Nitip</a>
                <a href="{{ route('contact') }}" class="text-[#666666] hover:text-rose-600 px-3 py-2 text-sm font-medium transition-colors">Kontak</a>
            </div>

            {{-- Desktop Right Side --}}
            <div class="flex items-center space-x-4">
                {{-- Search --}}
                <button @click="searchOpen = true" class="p-2 text-[#666666] hover:text-rose-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a8 8 0 11-16 0 8 8 0 0116 0z"></path>
                    </svg>
                </button>

                {{-- Cart --}}
                <div class="relative">
                    <button @click="cartOpen = true" class="relative p-2 text-[#666666] hover:text-rose-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 11V7a4 4 0 00-8 0v4M8 11h8m-2 4h-4M8 15l-2 4h8l-2-4"></path>
                        </svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </button>
                </div>

                {{-- Mobile Menu Button --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-[#666666] hover:text-rose-600 transition-colors">
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
    <div x-show="mobileMenuOpen" x-transition x-cloak class="md:hidden bg-white border-t border-[#E8E0D8] shadow-lg">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 text-sm font-medium text-[#333333] hover:text-rose-600 hover:bg-rose-50 rounded">Beranda</a>
            <a href="{{ route('products.index') }}" class="block px-3 py-2 text-sm font-medium text-[#666666] hover:text-rose-600 hover:bg-rose-50 rounded">Produk</a>
            <a href="{{ route('categories.index') }}" class="block px-3 py-2 text-sm font-medium text-[#666666] hover:text-rose-600 hover:bg-rose-50 rounded">Kategori</a>
            <a href="{{ route('about') }}" class="block px-3 py-2 text-sm font-medium text-[#666666] hover:text-rose-600 hover:bg-rose-50 rounded">Tentang</a>
            <a href="{{ route('how-to') }}" class="block px-3 py-2 text-sm font-medium text-[#666666] hover:text-rose-600 hover:bg-rose-50 rounded">Cara Nitip</a>
            <a href="{{ route('contact') }}" class="block px-3 py-2 text-sm font-medium text-[#666666] hover:text-rose-600 hover:bg-rose-50 rounded">Kontak</a>
        </div>
    </div>
</nav>