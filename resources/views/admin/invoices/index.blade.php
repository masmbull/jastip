@extends('layouts.admin')
@section('title', 'Invoice | ' . setting('brand_name'))

@section('content')
@php($activeStatus = request('status', 'all'))
<div class="space-y-6" x-data="{ open: false, sampleOpen: false, sampleLoaded: false, sampleError: false }"
     @close-manual-invoice.window="open = false"
     @keydown.escape.window="open = false; sampleOpen = false">
    <x-page-header title="Invoice" subtitle="Invoice manual yang dibuat admin" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.orders.index')" variant="ghost">Semua Pesanan</x-btn>
        <x-btn type="button" variant="outline" icon="printer"
               @click="sampleOpen = true; sampleLoaded = false; sampleError = false">Pratinjau Sampel</x-btn>
        <x-btn type="button" icon="plus" @click="open = true">Buat Invoice</x-btn>
    </x-page-header>

    <x-card class="animate-fade-up" style="animation-delay: 60ms">
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8] pointer-events-none" />
                <input type="search" name="search" value="{{ request('search') }}"
                       placeholder="Cari no. invoice, nama, atau WhatsApp"
                       class="w-full pl-9 pr-3 py-2 text-sm border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
            <select name="status"
                    class="px-3 py-2 text-sm border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                <option value="all">Semua status</option>
                @foreach(\App\Models\Order::statuses() as $value => $label)
                    <option value="{{ $value }}" @selected($activeStatus === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit"
                    class="px-4 py-2 text-sm bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                Terapkan
            </button>
            @if(request()->filled('search') || $activeStatus !== 'all')
                <a href="{{ route('admin.invoices.index') }}"
                   class="px-4 py-2 text-sm text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 rounded-lg font-medium text-center transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </x-card>

    <x-card padded="false" class="animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px]">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 py-3">No. Invoice</th>
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Item</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3">Pembayaran</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854] stagger">
                    @include('admin.invoices._rows')
                </tbody>
            </table>
        </div>
    </x-card>

    @if(method_exists($invoices, 'hasPages') && $invoices->hasPages())
        <div>{{ $invoices->links() }}</div>
    @endif

    @include('admin.invoices._modal')
    @include('admin.invoices._sample-modal')
</div>
@endsection

@include('admin.orders._manual-form-script')