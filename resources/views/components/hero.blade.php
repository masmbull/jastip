@props(['backgroundImage' => null])

@php
    $backgroundImage = $backgroundImage ?? (setting('homepage_hero') ? asset('storage/' . setting('homepage_hero')) : null);
    $primaryColor = setting('primary_color', '#F97316');
@endphp

<section class="relative bg-gradient-to-br from-orange-100 via-orange-50 to-white overflow-hidden">
    <!-- Decorative blobs -->
    <div class="absolute -top-24 -right-24 w-72 h-72 bg-orange-200 rounded-full mix-blend-multiply filter blur-3xl opacity-40"></div>
    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>

    @if($backgroundImage)
        <div class="absolute inset-0">
            <img src="{{ $backgroundImage }}" alt="Hero" class="w-full h-full object-cover opacity-10" loading="lazy" decoding="async">
        </div>
    @endif

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 lg:py-32">
        <div class="text-center max-w-4xl mx-auto animate-fade-up">
            <span class="inline-block bg-orange-100 text-orange-700 text-sm font-medium px-4 py-1.5 rounded-full mb-6">
                🛍️ {{ setting('hero_badge', 'Titipan Spesial Untukmu') }}
            </span>

            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-[#1E293B] mb-6 leading-tight">
                {{ setting('brand_name', 'NITIP DI END') }}
            </h1>

            <p class="text-xl md:text-2xl text-orange-600 font-semibold mb-6">
                {{ setting('brand_tagline', 'EH, NITIP DONG!') }}
            </p>

            <p class="text-lg text-[#64748B] mb-8 max-w-2xl mx-auto">
                {{ setting('hero_description', 'Temukan berbagai produk impian dengan harga terbaik. Gratis ongkir untuk pemesanan di atas Rp 150.000.') }}
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4 mt-10">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center justify-center px-8 py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-full shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.89 1.77M7 13h10l2-4H5.11M7 13L5.11 5.11M7 13l1.85 1.85a2 2 0 002.73.27M13 13h3m-3 0l1.85 1.85a2 2 0 01-.27 2.73z"></path>
                    </svg>
                    Mulai Belanja
                </a>
                <a href="{{ route('products.index', ['sort' => 'popular']) }}"
                   class="inline-flex items-center justify-center px-8 py-4 bg-white hover:bg-gray-50 text-orange-600 font-bold rounded-full shadow-lg hover:shadow-xl transition-all duration-200 border-2 border-orange-200 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Produk Favorit
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Feature Highlights -->
<div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 bg-white">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center animate-fade-up">
        <div class="p-6">
            <div class="w-12 h-12 mx-auto bg-orange-100 rounded-full flex items-center justify-center mb-4">
                🚚
            </div>
            <h3 class="font-semibold text-[#1E293B] mb-2">Gratis Ongkir</h3>
            <p class="text-sm text-[#94A3B8]">Untuk pemesanan di atas Rp 150.000</p>
        </div>
        <div class="p-6">
            <div class="w-12 h-12 mx-auto bg-orange-100 rounded-full flex items-center justify-center mb-4">
                🔒
            </div>
            <h3 class="font-semibold text-[#1E293B] mb-2">Bayar di Tempat</h3>
            <p class="text-sm text-[#94A3B8]">Cash on delivery saat barang sampai</p>
        </div>
        <div class="p-6">
            <div class="w-12 h-12 mx-auto bg-orange-100 rounded-full flex items-center justify-center mb-4">
                💯
            </div>
            <h3 class="font-semibold text-[#1E293B] mb-2">Garansi Autentik</h3>
            <p class="text-sm text-[#94A3B8]">Produk 100% asli terjamin</p>
        </div>
    </div>
</div>