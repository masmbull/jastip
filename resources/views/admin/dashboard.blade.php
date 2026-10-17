@extends('layouts.admin')

@section('title', 'Dashboard Admin | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">
    <x-page-header title="Selamat Datang, Admin"
                   subtitle="{{ setting('brand_owner', 'Nabila Adriyana') }} · {{ setting('brand_name', 'NITIP DI END') }}">
        <x-btn as="a" :href="route('admin.products.create')" icon="plus">Tambah Produk</x-btn>
    </x-page-header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 stagger nb-rainbow">
        <x-stat-card label="Total Produk" :value="$stats['products_count'] ?? 0" icon="cube" :href="route('admin.products.index')" />
        <x-stat-card label="Total Kategori" :value="$stats['categories_count'] ?? 0" icon="tag" :href="route('admin.categories.index')" />
        <x-stat-card label="Total Pesanan" :value="$stats['orders_count'] ?? 0" icon="cart" :href="route('admin.orders.index')" />
        <x-stat-card label="Menunggu Konfirmasi" :value="$stats['orders_pending'] ?? 0" icon="clock" :href="route('admin.orders.index', ['status' => 'pending'])" />
    </div>

    <div class="bg-[#FEF3C7] dark:bg-[#262626] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-xl p-5">
        <h2 class="text-sm font-black text-[#141414] dark:text-[#FAF6EF] mb-1 flex items-center gap-1.5">
            <x-icon name="bell" class="w-5 h-5" /> Pemantauan Layanan
        </h2>
        <p class="text-sm font-medium text-[#475569] dark:text-[#D6D3D1] mb-3">Cek kesehatan sistem dan layanan yang terhubung.</p>
        <x-btn as="a" :href="route('admin.system.status')" icon="arrow-right">Buka Pemantauan</x-btn>
    </div>
</div>
@endsection
