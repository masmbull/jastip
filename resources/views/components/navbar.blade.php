@php
    $cartCount = app(\App\Services\CartService::class)->count();
    $cartSubtotal = app(\App\Services\CartService::class)->subtotal();
    $brandName = setting('brand_name', 'NITIP DI END');
    $brandLogo = setting('brand_logo') ? asset('storage/' . setting('brand_logo')) : null;
    $brandOwner = setting('brand_owner', 'Nabila Adriyana');
@endphp

<nav class="bg-white dark:bg-[#1E1E1E] sticky top-0 z-50 border-b-2 border-[#141414] dark:border-[#FAF6EF] transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 gap-2">
            <div class="flex-shrink-0 flex items-center space-x-3 min-w-0">
                @if($brandLogo)
                    <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-10 w-10 object-contain" loading="lazy" decoding="async">
                @else
                    <div class="h-10 w-10 bg-[#FFC800] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] flex items-center justify-center shrink-0">
                        <span class="text-[#141414] font-black text-lg">N</span>
                    </div>
                @endif
                <div class="min-w-0">
                    <span class="font-black text-lg leading-tight text-[#141414] dark:text-[#FAF6EF] truncate block">{{ $brandName }}</span>
                    <span class="block text-[11px] font-extrabold uppercase tracking-widest text-[#0E7490] dark:text-[#22D3EE] truncate">{{ setting('brand_tagline', 'EH, NITIP DONG!') }}</span>
                </div>
            </div>

            <div class="hidden lg:flex lg:items-center lg:gap-1">
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
                           @if(request()->routeIs($link['route'])) aria-current="page" @endif
                           class="px-2.5 py-2 text-sm whitespace-nowrap border-b-4 transition-colors min-h-[44px] inline-flex items-center {{ request()->routeIs($link['route']) ? 'font-black text-[#141414] dark:text-[#FAF6EF] border-[#FFC800]' : 'font-bold text-[#475569] dark:text-[#D6D3D1] border-transparent hover:text-[#141414] dark:hover:text-[#FAF6EF] hover:border-[#141414] dark:hover:border-[#FAF6EF]' }}">
                            {{ $link['label'] }}
                        </a>
                    @endif
                @endforeach
            </div>

            <div class="flex items-center gap-1 shrink-0">
                <button @click="searchOpen = true"
                        class="w-11 h-11 inline-flex items-center justify-center text-[#141414] dark:text-[#FAF6EF] hover:bg-[#FFF7D6] dark:hover:bg-[#262626] rounded-[8px] border-2 border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF] transition-colors"
                        aria-label="Cari" title="Cari produk">
                    <x-icon name="search" class="w-5 h-5" />
                </button>

                <button type="button"
                        @click="$dispatch('theme-toggle')"
                        class="w-11 h-11 inline-flex items-center justify-center text-[#141414] dark:text-[#FAF6EF] hover:bg-[#FFF7D6] dark:hover:bg-[#262626] rounded-[8px] border-2 border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF] transition-colors"
                        aria-label="Ganti tema" title="Ganti mode gelap/terang">
                    <x-icon name="sun" class="w-5 h-5 dark:hidden" />
                    <x-icon name="moon" class="hidden w-5 h-5 dark:block" />
                </button>

                <a href="{{ route('cart.index') }}"
                   class="relative w-11 h-11 inline-flex items-center justify-center text-[#141414] dark:text-[#FAF6EF] hover:bg-[#FFF7D6] dark:hover:bg-[#262626] rounded-[8px] border-2 border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF] transition-colors"
                   aria-label="Titipan" title="Titipan">
                    <x-icon name="bag" class="w-6 h-6" />
                    <span id="cart-count"
                          class="absolute -top-1.5 -right-1.5 bg-[#FFC800] text-[#141414] text-[11px] font-black border-2 border-[#141414] rounded-md min-w-5 h-5 px-0.5 flex items-center justify-center num {{ $cartCount > 0 ? '' : 'hidden' }}">
                        {{ $cartCount }}
                    </span>
                </a>

                @auth
                    @php($accountUser = auth()->user())
                    <div class="relative" x-data="{ accountOpen: false }" @click.outside="accountOpen = false" @keydown.escape.window="accountOpen = false">
                        <button type="button" @click="accountOpen = !accountOpen"
                                class="flex items-center p-1 rounded-[8px] border-2 border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF] transition-colors"
                                title="Akun saya" aria-haspopup="true" :aria-expanded="accountOpen">
                            <span class="h-8 w-8 rounded-[8px] bg-[#141414] dark:bg-[#FFC800] text-white dark:text-[#141414] border-2 border-[#141414] dark:border-[#FAF6EF] text-sm font-black flex items-center justify-center">
                                {{ strtoupper(substr($accountUser->name, 0, 1)) }}
                            </span>
                        </button>
                        <div x-show="accountOpen" x-transition x-cloak
                             class="absolute right-0 mt-2 w-56 py-2 bg-white dark:bg-[#1E1E1E] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] z-50" style="box-shadow: 4px 4px 0 #141414" role="menu">
                            @if($accountUser->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center min-h-[44px] px-4 py-2 text-sm font-bold text-[#141414] dark:text-[#FAF6EF] hover:bg-[#FFF7D6] dark:hover:bg-[#262626]">Panel Admin</a>
                            @endif
                            <a href="{{ route('profile.show') }}" class="flex items-center min-h-[44px] px-4 py-2 text-sm font-bold text-[#141414] dark:text-[#FAF6EF] hover:bg-[#FFF7D6] dark:hover:bg-[#262626]">Profil Saya</a>
                            <a href="{{ route('profile.orders') }}" class="flex items-center min-h-[44px] px-4 py-2 text-sm font-bold text-[#141414] dark:text-[#FAF6EF] hover:bg-[#FFF7D6] dark:hover:bg-[#262626]">Pesanan Saya</a>
                            <a href="{{ route('profile.addresses') }}" class="flex items-center min-h-[44px] px-4 py-2 text-sm font-bold text-[#141414] dark:text-[#FAF6EF] hover:bg-[#FFF7D6] dark:hover:bg-[#262626]">Alamat</a>
                            <a href="{{ route('profile.wishlist') }}" class="flex items-center min-h-[44px] px-4 py-2 text-sm font-bold text-[#141414] dark:text-[#FAF6EF] hover:bg-[#FFF7D6] dark:hover:bg-[#262626]">Wishlist</a>
                            <a href="{{ route('chat.index') }}" class="flex items-center min-h-[44px] px-4 py-2 text-sm font-bold text-[#141414] dark:text-[#FAF6EF] hover:bg-[#FFF7D6] dark:hover:bg-[#262626]">Bantuan dan Chat</a>
                            <a href="{{ route('notifications.index') }}" class="flex items-center min-h-[44px] px-4 py-2 text-sm font-bold text-[#141414] dark:text-[#FAF6EF] hover:bg-[#FFF7D6] dark:hover:bg-[#262626]">Notifikasi</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 min-h-[44px] text-sm font-black text-[#991B1B] dark:text-[#FECACA] hover:bg-[#FEE2E2] dark:hover:bg-[#7F1D1D]">Keluar</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                       class="w-11 h-11 inline-flex items-center justify-center text-[#141414] dark:text-[#FAF6EF] hover:bg-[#FFF7D6] dark:hover:bg-[#262626] rounded-[8px] border-2 border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF] transition-colors"
                       title="Masuk" aria-label="Masuk">
                        <x-icon name="user-circle" class="w-6 h-6" />
                    </a>
                @endauth

                <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="lg:hidden w-11 h-11 inline-flex items-center justify-center text-[#141414] dark:text-[#FAF6EF] hover:bg-[#FFF7D6] dark:hover:bg-[#262626] rounded-[8px] border-2 border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF] transition-colors"
                        aria-label="Menu" title="Menu" :aria-expanded="mobileMenuOpen">
                    <x-icon name="menu" class="w-6 h-6" x-show="!mobileMenuOpen" />
                    <x-icon name="x" class="w-6 h-6" x-show="mobileMenuOpen" x-cloak />
                </button>
            </div>
        </div>
    </div>

    <div x-show="mobileMenuOpen" x-transition x-cloak class="lg:hidden bg-white dark:bg-[#1E1E1E] border-t-2 border-[#141414] dark:border-[#FAF6EF] transition-colors">
        <div class="px-3 pt-2 pb-4 space-y-1">
            @foreach($links as $link)
                @if(\Route::has($link['route']))
                    <a href="{{ route($link['route']) }}"
                       @if(request()->routeIs($link['route'])) aria-current="page" @endif
                       class="flex items-center min-h-[44px] px-3 py-2.5 text-sm rounded-[8px] border-2 transition-colors {{ request()->routeIs($link['route']) ? 'font-black text-[#141414] bg-[#FFC800] border-[#141414]' : 'font-bold text-[#141414] dark:text-[#FAF6EF] border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF]' }}">
                        {{ $link['label'] }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</nav>
