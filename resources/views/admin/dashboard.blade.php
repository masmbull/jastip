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

    <div class="bg-sky-50 dark:bg-sky-500/10 border border-sky-200 dark:border-sky-500/30 rounded-xl p-5">
        <h3 class="text-sm font-semibold text-sky-900 dark:text-sky-200 mb-1 flex items-center gap-1.5">
            <x-icon name="bell" class="w-5 h-5" /> Pemantauan Layanan
        </h3>
        <p class="text-sm text-sky-800 dark:text-sky-300 mb-3">Cek kesehatan sistem dan layanan yang terhubung.</p>
        <x-btn as="a" :href="route('admin.services.index')" icon="arrow-right">Buka Pemantauan</x-btn>
    </div>
</div>
@endsection
