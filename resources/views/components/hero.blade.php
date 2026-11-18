@props(['backgroundImage' => null])

@php
    $backgroundImage = $backgroundImage ?? (setting('homepage_hero') ? asset('storage/' . setting('homepage_hero')) : null);
    $brandName = setting('brand_name', 'NITIP DI END');
    $tagline = setting('brand_tagline', 'EH, NITIP DONG!');
    $heroCopy = setting('hero_description', 'Kamu wishlist, kami beliin. Katalog jastip lokal buat titip parfum, tumbler, lifestyle, dan barang pilihan.');
@endphp

<section class="bg-[#FAF6EF] dark:bg-[#121212] border-b-2 border-[#141414] dark:border-[#FAF6EF] overflow-hidden transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">
        <div class="max-w-3xl animate-fade-up">
            <p class="inline-block text-sm md:text-base font-black text-[#141414] dark:text-[#FAF6EF]">
                <span class="nb-marker">{{ $tagline }}</span>
            </p>

            <h1 class="font-display mt-4 text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-[#141414] dark:text-[#FAF6EF] leading-[1.05]">
                {{ $brandName }}
            </h1>

            <p class="mt-5 text-base md:text-lg font-medium text-[#334155] dark:text-[#D6D3D1] leading-relaxed max-w-2xl">
                {{ $heroCopy }}
            </p>

            <div class="flex flex-col sm:flex-row gap-3 mt-8">
                <x-btn as="a" :href="route('products.index')" variant="accent" icon="bag"
                       class="px-7 py-3.5 text-base">
                    Lihat Katalog
                </x-btn>
                <x-btn as="a" :href="route('how-to')" variant="ghost"
                       class="px-7 py-3.5 text-base">
                    Cara Nitip
                </x-btn>
            </div>
        </div>
    </div>

    @if($backgroundImage)
        <div class="border-t-2 border-[#141414] dark:border-[#FAF6EF]">
            <img src="{{ $backgroundImage }}"
                 alt="Suasana titipan {{ $brandName }}"
                 class="w-full h-48 sm:h-56 md:h-72 object-cover"
                 loading="lazy"
                 decoding="async">
        </div>
    @endif
</section>

<div class="bg-white dark:bg-[#1E1E1E] transition-colors border-b-2 border-[#141414]/10 dark:border-[#FAF6EF]/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10">
        {{-- Hierarchy: first perk carries accent fill; others stay paper. --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
            <div class="nb-card nb-card-lift p-5 flex gap-4 text-left bg-[#FFC800]! border-[#141414] dark:border-[#FAF6EF]">
                <div class="shrink-0 w-12 h-12 bg-white border-2 border-[#141414] rounded-[10px] flex items-center justify-center text-[#141414]">
                    <x-icon name="truck" class="w-6 h-6" stroke="2" />
                </div>
                <div>
                    <h2 class="font-display font-extrabold text-[#141414]">Gratis Ongkir</h2>
                    <p class="text-sm font-medium text-[#141414]/80 mt-1">Untuk pemesanan di atas Rp 150.000</p>
                </div>
            </div>
            <div class="nb-card nb-card-lift p-5 flex gap-4 text-left">
                <div class="shrink-0 w-12 h-12 bg-[#FAF6EF] dark:bg-[#262626] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] flex items-center justify-center text-[#141414] dark:text-[#FAF6EF]">
                    <x-icon name="banknotes" class="w-6 h-6" stroke="2" />
                </div>
                <div>
                    <h2 class="font-display font-extrabold text-[#141414] dark:text-[#FAF6EF]">Bayar di Tempat</h2>
                    <p class="text-sm font-medium text-[#334155] dark:text-[#D6D3D1] mt-1">Cash on delivery saat barang sampai</p>
                </div>
            </div>
            <div class="nb-card nb-card-lift p-5 flex gap-4 text-left">
                <div class="shrink-0 w-12 h-12 bg-[#FAF6EF] dark:bg-[#262626] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] flex items-center justify-center text-[#141414] dark:text-[#FAF6EF]">
                    <x-icon name="shield-check" class="w-6 h-6" stroke="2" />
                </div>
                <div>
                    <h2 class="font-display font-extrabold text-[#141414] dark:text-[#FAF6EF]">Barang Asli</h2>
                    <p class="text-sm font-medium text-[#334155] dark:text-[#D6D3D1] mt-1">Dibeli langsung dari sumber terpercaya</p>
                </div>
            </div>
        </div>
    </div>
</div>
