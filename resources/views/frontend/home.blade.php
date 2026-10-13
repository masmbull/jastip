@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <x-hero />

    <section class="py-10 md:py-14">
        <div class="flex flex-wrap items-end justify-between gap-3 mb-6">
            <div>
                <h2 class="text-2xl md:text-3xl font-black tracking-tight"><span class="nb-marker">Jelajahi Kategori</span></h2>
                <p class="font-medium text-[#475569] dark:text-[#D6D3D1] mt-2">Pilih kategori yang kamu inginkan.</p>
            </div>
            <x-btn as="a" :href="route('categories.index')" variant="ghost">Semua Kategori</x-btn>
        </div>

        @if($categories->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                @foreach($categories as $category)
                    <x-category-card :category="$category" />
                @endforeach
            </div>
        @else
            <x-empty-state icon="tag" title="Belum ada kategori tersedia." />
        @endif
    </section>

    <section class="my-4 md:my-6 nb-card p-0! overflow-hidden">
        <div class="px-5 sm:px-7 pt-6 sm:pt-8 pb-2 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="text-2xl md:text-3xl font-black tracking-tight">Produk Unggulan</h2>
                <p class="font-medium text-[#475569] dark:text-[#D6D3D1] mt-2">Pilihan produk yang sedang ramai dibeli.</p>
            </div>
            <x-btn as="a" :href="route('products.index')">Lihat Semua Produk</x-btn>
        </div>

        @if($featuredProducts->isNotEmpty())
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5 p-5 sm:p-7 pt-5">
                @foreach($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @else
            <x-empty-state icon="cube" title="Belum ada produk." />
        @endif
    </section>

    <section class="py-10 md:py-14 border-t-2 border-[#141414] dark:border-[#FAF6EF] mt-10">
        <div class="mb-6">
            <h2 class="text-2xl md:text-3xl font-black tracking-tight">Produk Populer</h2>
            <p class="font-medium text-[#475569] dark:text-[#D6D3D1] mt-2">Produk terlaris pilihan pelanggan.</p>
        </div>

        @if($popularProducts->isNotEmpty())
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5">
                @foreach($popularProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @else
            <x-empty-state icon="cube" title="Belum ada produk populer." />
        @endif
    </section>

    @if(isset($viralProducts) && $viralProducts->isNotEmpty())
        <section class="pb-10 md:pb-14">
            <div class="bg-[#141414] dark:bg-[#FAF6EF] rounded-[12px] border-2 border-[#141414] dark:border-[#FAF6EF] p-5 sm:p-7 mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl md:text-3xl font-black tracking-tight text-white dark:text-[#141414]">Produk Viral</h2>
                    <p class="font-medium text-white/70 dark:text-[#141414]/70 mt-2">Dikurasi admin dari katalog yang lagi ramai.</p>
                </div>
                <a href="{{ route('products.viral') }}" class="nb-btn px-5 py-2.5 bg-[#FFC800] text-[#141414] border-[#FFC800]!">Lihat Semua</a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5">
                @foreach($viralProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    <section class="mb-12 bg-[#141414] dark:bg-[#FAF6EF] rounded-[12px] border-2 border-[#141414] dark:border-[#FAF6EF] p-8 md:p-12 text-center" style="box-shadow: 6px 6px 0 #FFC800">
        <h2 class="text-2xl md:text-4xl font-black tracking-tight text-white dark:text-[#141414]">Butuh Bantuan?</h2>
        <p class="text-base md:text-lg font-medium text-white/75 dark:text-[#141414]/75 mt-3 max-w-2xl mx-auto">
            Ada pertanyaan? Hubungi admin via WhatsApp untuk bantuan cepat.
        </p>
        <a href="{{ \App\Services\WhatsappService::contactUrl('Halo Kak, saya mau tanya soal nitipan di ' . setting('brand_name', 'NITIP DI END')) }}"
           target="_blank" rel="noopener noreferrer"
           class="nb-btn px-8 py-4 text-base bg-[#FFC800] text-[#141414] border-[#FFC800]! mt-7">
            <x-icon name="chat" class="w-5 h-5" />
            Chat Admin
        </a>
    </section>

</div>
@endsection
