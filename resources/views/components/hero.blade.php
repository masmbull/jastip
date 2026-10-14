@props(['backgroundImage' => null])

@php
    $backgroundImage = $backgroundImage ?? (setting('homepage_hero') ? asset('storage/' . setting('homepage_hero')) : null);
@endphp

<section class="bg-[#FAF6EF] dark:bg-[#121212] border-b-2 border-[#141414] dark:border-[#FAF6EF] overflow-hidden transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20 lg:py-24">
        <div class="max-w-3xl">
            <p class="inline-block text-sm md:text-base font-black text-[#141414] dark:text-[#FAF6EF]">
                <span class="nb-marker">{{ setting('brand_tagline', 'EH, NITIP DONG!') }}</span>
            </p>

            <h1 class="mt-4 text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-[#141414] dark:text-[#FAF6EF] leading-[1.05]">
                {{ setting('brand_name', 'NITIP DI END') }}
            </h1>

            <p class="mt-5 text-base md:text-lg font-medium text-[#334155] dark:text-[#D6D3D1] leading-relaxed max-w-2xl">
                {{ setting('hero_description', 'Temukan berbagai produk impian dengan harga terbaik. Gratis ongkir untuk pemesanan di atas Rp 150.000.') }}
            </p>

            <div class="flex flex-col sm:flex-row gap-3 mt-8">
                <x-btn as="a" :href="route('products.index')" variant="accent" icon="cart"
                       class="px-7 py-3.5 text-base">
                    Mulai Belanja
                </x-btn>
                <x-btn as="a" :href="route('products.index', ['sort' => 'popular'])" variant="ghost"
                       class="px-7 py-3.5 text-base">
                    Produk Favorit
                </x-btn>
            </div>
        </div>
    </div>

    @if($backgroundImage)
        <div class="border-t-2 border-[#141414] dark:border-[#FAF6EF]">
            <img src="{{ $backgroundImage }}" alt="Suasana titipan {{ setting('brand_name', 'NITIP DI END') }}" class="w-full h-56 md:h-72 object-cover" loading="lazy" decoding="async">
        </div>
    @endif
</section>

<div class="bg-white dark:bg-[#1E1E1E] transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-5 nb-rainbow">
            <div class="nb-card p-5 bg-[#FFF7D6]! dark:bg-[#262626]! flex gap-4 text-left">
                <div class="shrink-0 w-12 h-12 bg-[#FFC800] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] flex items-center justify-center text-[#141414]">
                    <x-icon name="truck" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="font-black text-[#141414] dark:text-[#FAF6EF]">Gratis Ongkir</h2>
                    <p class="text-sm font-medium text-[#334155] dark:text-[#D6D3D1] mt-1">Untuk pemesanan di atas Rp 150.000</p>
                </div>
            </div>
            <div class="nb-card p-5 flex gap-4 text-left">
                <div class="shrink-0 w-12 h-12 bg-white dark:bg-[#1E1E1E] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] flex items-center justify-center text-[#0E7490] dark:text-[#22D3EE]">
                    <x-icon name="lock" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="font-black text-[#141414] dark:text-[#FAF6EF]">Bayar di Tempat</h2>
                    <p class="text-sm font-medium text-[#334155] dark:text-[#D6D3D1] mt-1">Cash on delivery saat barang sampai</p>
                </div>
            </div>
            <div class="nb-card p-5 flex gap-4 text-left">
                <div class="shrink-0 w-12 h-12 bg-white dark:bg-[#1E1E1E] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] flex items-center justify-center text-[#0E7490] dark:text-[#22D3EE]">
                    <x-icon name="shield-check" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="font-black text-[#141414] dark:text-[#FAF6EF]">Garansi Autentik</h2>
                    <p class="text-sm font-medium text-[#334155] dark:text-[#D6D3D1] mt-1">Produk asli terjamin</p>
                </div>
            </div>
        </div>
    </div>
</div>
