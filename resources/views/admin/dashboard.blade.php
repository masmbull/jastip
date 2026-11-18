@extends('layouts.admin')

@section('title', 'Dashboard Admin | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">
    <x-page-header title="Halo, {{ auth()->user()->name ?? 'Admin' }}"
                   subtitle="{{ setting('brand_owner', 'Nabila Adriyana') }} · {{ setting('brand_name', 'NITIP DI END') }}">
        <x-btn as="a" :href="route('admin.products.create')" icon="plus">Tambah Produk</x-btn>
        <x-btn as="a" :href="route('admin.orders.index')" variant="ghost" icon="bag">Lihat Pesanan</x-btn>
    </x-page-header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 stagger nb-rainbow">
        <x-stat-card label="Total Produk" :value="$stats['products_count'] ?? 0" icon="cube" :href="route('admin.products.index')" />
        <x-stat-card label="Total Kategori" :value="$stats['categories_count'] ?? 0" icon="tag" :href="route('admin.categories.index')" />
        <x-stat-card label="Total Pesanan" :value="$stats['orders_count'] ?? 0" icon="bag" :href="route('admin.orders.index')" />
        <x-stat-card label="Menunggu Konfirmasi" :value="$stats['orders_pending'] ?? 0" icon="clock" :href="route('admin.orders.index', ['status' => 'pending'])" />
    </div>

    <div class="nb-card p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-[#FFC800]!">
        <div>
            <h2 class="font-display text-base font-extrabold text-[#141414] flex items-center gap-2">
                <x-icon name="server" class="w-5 h-5" stroke="2" />
                Pemantauan Layanan
            </h2>
            <p class="text-sm font-medium text-[#141414]/80 mt-1">Cek kesehatan sistem dan layanan yang terhubung.</p>
        </div>
        <x-btn as="a" :href="route('admin.system.status')" variant="ghost" icon="arrow-right" class="shrink-0">
            Buka Pemantauan
        </x-btn>
    </div>
</div>
@endsection
