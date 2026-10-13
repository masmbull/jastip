@props(['backgroundImage' => null])

@php
    $backgroundImage = $backgroundImage ?? (setting('homepage_hero') ? asset('storage/' . setting('homepage_hero')) : null);
@endphp

<section class="relative bg-gradient-to-br from-cyan-100 via-sky-50 to-white dark:from-[#1a1a1a] dark:via-[#23252b] dark:to-[#1a1a1a] overflow-hidden">
    <!-- Decorative blobs -->
    <div class="absolute -top-24 -right-24 w-72 h-72 bg-cyan-300 rounded-full mix-blend-multiply filter blur-3xl opacity-40 dark:opacity-20" aria-hidden="true"></div>
    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-teal-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 dark:opacity-20" aria-hidden="true"></div>

    @if($backgroundImage)
        <div class="absolute inset-0" aria-hidden="true">
            <img src="{{ $backgroundImage }}" alt="" class="w-full h-full object-cover opacity-10" loading="lazy" decoding="async">
        </div>
    @endif

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-28 lg:py-32">
        <div class="text-center max-w-4xl mx-auto animate-fade-up">
            <x-badge tone="brand" class="mb-6 px-4 py-1.5 text-sm font-medium">
                <x-icon name="bag" class="w-4 h-4" /> {{ setting('hero_badge', 'Titipan Spesial Untukmu') }}
            </x-badge>

            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-[#1E293B] dark:text-[#f1f5f9] mb-6 leading-tight">
                {{ setting('brand_name', 'NITIP DI END') }}
            </h1>

            <p class="text-lg md:text-2xl text-orange-600 dark:text-orange-400 font-semibold mb-6">
                {{ setting('brand_tagline', 'EH, NITIP DONG!') }}
            </p>

            <p class="text-base md:text-lg text-[#64748B] dark:text-[#cbd5e1] mb-8 max-w-2xl mx-auto">
                {{ setting('hero_description', 'Temukan berbagai produk impian dengan harga terbaik. Gratis ongkir untuk pemesanan di atas Rp 150.000.') }}
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4 mt-10">
                <x-btn as="a" :href="route('products.index')" icon="cart"
                       class="px-8 py-4 rounded-full shadow-lg text-base font-bold">
                    Mulai Belanja
                </x-btn>
                <x-btn as="a" :href="route('products.index', ['sort' => 'popular'])" variant="outline"
                       class="px-8 py-4 rounded-full shadow-lg text-base font-bold border-2 border-orange-200 dark:border-orange-500/40 text-orange-600 dark:text-orange-400">
                    Produk Favorit
                </x-btn>
            </div>
        </div>
    </div>
</section>

<!-- Feature Highlights -->
<div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 bg-white dark:bg-[#1a1a1a]">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 text-center animate-fade-up">
        <div class="p-6 rounded-xl border border-[#E2E8F0] dark:border-[#404854]">
            <div class="w-12 h-12 mx-auto bg-orange-100 dark:bg-orange-500/15 rounded-full flex items-center justify-center mb-4 text-orange-600 dark:text-orange-400">
                <x-icon name="truck" class="w-6 h-6" />
            </div>
            <h3 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-2">Gratis Ongkir</h3>
            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Untuk pemesanan di atas Rp 150.000</p>
        </div>
        <div class="p-6 rounded-xl border border-[#E2E8F0] dark:border-[#404854]">
            <div class="w-12 h-12 mx-auto bg-orange-100 dark:bg-orange-500/15 rounded-full flex items-center justify-center mb-4 text-orange-600 dark:text-orange-400">
                <x-icon name="lock" class="w-6 h-6" />
            </div>
            <h3 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-2">Bayar di Tempat</h3>
            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Cash on delivery saat barang sampai</p>
        </div>
        <div class="p-6 rounded-xl border border-[#E2E8F0] dark:border-[#404854]">
            <div class="w-12 h-12 mx-auto bg-orange-100 dark:bg-orange-500/15 rounded-full flex items-center justify-center mb-4 text-orange-600 dark:text-orange-400">
                <x-icon name="shield-check" class="w-6 h-6" />
            </div>
            <h3 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-2">Garansi Autentik</h3>
            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Produk 100% asli terjamin</p>
        </div>
    </div>
</div>