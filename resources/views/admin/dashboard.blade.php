@extends('layouts.admin')

@section('title', 'Dashboard Admin | ' . setting('brand_name'))

@section('content')
<div class="p-4 md:p-6 lg:p-8 space-y-4 md:space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Selamat Datang, Admin</h1>
            <p class="text-xs md:text-sm text-[#94A3B8] dark:text-[#cbd5e1] mt-1">{{ setting('brand_owner', 'Nabila Adriyana') }} — {{ setting('brand_name', 'NITIP DI END') }}</p>
        </div>
        <a href="{{ route('admin.products.create') }}"
           class="inline-flex items-center px-3 md:px-4 py-2 md:py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition-colors text-sm md:text-base whitespace-nowrap">
            <svg class="w-4 md:w-5 h-4 md:h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Produk
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm p-4 md:p-5 border border-[#E2E8F0] dark:border-[#404854]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 md:w-10 h-9 md:h-10 bg-orange-100 dark:bg-orange-500/20 rounded-lg flex items-center justify-center">
                    <svg class="h-4 md:h-5 w-4 md:w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path>
                    </svg>
                </div>
                <a href="{{ route('admin.products.index') }}" class="text-xs text-orange-600 dark:text-orange-400 font-medium hover:underline">Detail</a>
            </div>
            <p class="text-2xl md:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">{{ $stats['products_count'] ?? 0 }}</p>
            <p class="text-xs md:text-sm text-[#94A3B8] dark:text-[#cbd5e1]">Total Produk</p>
        </div>

        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm p-4 md:p-5 border border-[#E2E8F0] dark:border-[#404854]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 md:w-10 h-9 md:h-10 bg-orange-100 dark:bg-orange-500/20 rounded-lg flex items-center justify-center">
                    <svg class="h-4 md:h-5 w-4 md:w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3Z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"></path>
                    </svg>
                </div>
                <a href="{{ route('admin.categories.index') }}" class="text-xs text-orange-600 dark:text-orange-400 font-medium hover:underline">Detail</a>
            </div>
            <p class="text-2xl md:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">{{ $stats['categories_count'] ?? 0 }}</p>
            <p class="text-xs md:text-sm text-[#94A3B8] dark:text-[#cbd5e1]">Total Kategori</p>
        </div>

        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm p-4 md:p-5 border border-[#E2E8F0] dark:border-[#404854]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 md:w-10 h-9 md:h-10 bg-orange-100 dark:bg-orange-500/20 rounded-lg flex items-center justify-center">
                    <svg class="h-4 md:h-5 w-4 md:w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0Zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0Z"></path>
                    </svg>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-orange-600 dark:text-orange-400 font-medium hover:underline">Detail</a>
            </div>
            <p class="text-2xl md:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">{{ $stats['orders_count'] ?? 0 }}</p>
            <p class="text-xs md:text-sm text-[#94A3B8] dark:text-[#cbd5e1]">Total Pesanan</p>
        </div>

        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm p-4 md:p-5 border border-[#E2E8F0] dark:border-[#404854]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 md:w-10 h-9 md:h-10 bg-orange-100 dark:bg-orange-500/20 rounded-lg flex items-center justify-center">
                    <svg class="h-4 md:h-5 w-4 md:w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0Z"></path>
                    </svg>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-orange-600 dark:text-orange-400 font-medium hover:underline">Detail</a>
            </div>
            <p class="text-2xl md:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">{{ $stats['orders_pending'] ?? 0 }}</p>
            <p class="text-xs md:text-sm text-[#94A3B8] dark:text-[#cbd5e1]">Menunggu Konfirmasi</p>
        </div>
    </div>

    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2"><x-icon name="bell" class="inline-block w-5 h-5 align-text-bottom" /> Service Monitoring</h3>
        <p class="text-sm text-blue-700 dark:text-blue-300 mb-3">Monitor kesehatan sistem dan services</p>
        <a href="{{ route('admin.services.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
            Buka Service Monitor <x-icon name="arrow-right" class="inline-block w-5 h-5 align-text-bottom" />
        </a>
    </div>
</div>
@endsection
