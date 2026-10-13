@extends('layouts.admin')
@section('title', 'Invoice | ' . setting('brand_name'))

@section('content')
@php($activeStatus = request('status', 'all'))
<div class="space-y-5" x-data="{ open: false, sampleOpen: false, sampleLoaded: false, sampleError: false }"
     @close-manual-invoice.window="open = false"
     @keydown.escape.window="open = false; sampleOpen = false">
    <x-page-header title="Invoice" subtitle="Invoice manual yang dibuat admin">
        <x-btn as="a" :href="route('admin.orders.index')" variant="ghost">Semua Pesanan</x-btn>
        <x-btn type="button" variant="outline" icon="printer"
               @click="sampleOpen = true; sampleLoaded = false; sampleError = false">Pratinjau Sampel</x-btn>
        <x-btn type="button" icon="plus" @click="open = true">Buat Invoice</x-btn>
    </x-page-header>

    <x-card>
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="flex flex-col sm:flex-row gap-2.5" role="search">
            <div class="relative flex-1">
                <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#475569] pointer-events-none" />
                <label class="sr-only" for="inv-search">Cari invoice</label>
                <input id="inv-search" type="search" name="search" value="{{ request('search') }}"
                       placeholder="Cari no. invoice, nama, atau WhatsApp"
                       class="nb-input w-full pl-9 pr-3 py-2 text-sm">
            </div>
            <label class="sr-only" for="inv-status">Filter status</label>
            <select id="inv-status" name="status" class="nb-input px-3 py-2 text-sm font-bold">
                <option value="all">Semua status</option>
                @foreach(\App\Models\Order::statuses() as $value => $label)
                    <option value="{{ $value }}" @selected($activeStatus === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="nb-btn px-4 py-2 text-sm bg-[#0E7490] text-white">
                Terapkan
            </button>
            @if(request()->filled('search') || $activeStatus !== 'all')
                <a href="{{ route('admin.invoices.index') }}"
                   class="nb-btn px-4 py-2 text-sm bg-white dark:bg-[#1E1E1E] text-[#475569]">
                    Reset
                </a>
            @endif
        </form>
    </x-card>

    <div class="nb-table-shell">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px]">
                <thead>
                    <tr class="nb-table-head text-left text-xs uppercase">
                        <th scope="col" class="px-4 py-3 font-black">No. Invoice</th>
                        <th scope="col" class="px-4 py-3 font-black">Pelanggan</th>
                        <th scope="col" class="px-4 py-3 font-black">Tanggal</th>
                        <th scope="col" class="px-4 py-3 font-black">Item</th>
                        <th scope="col" class="px-4 py-3 text-right font-black">Total</th>
                        <th scope="col" class="px-4 py-3 font-black">Pembayaran</th>
                        <th scope="col" class="px-4 py-3 font-black">Status</th>
                        <th scope="col" class="px-4 py-3 font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#141414]/15 dark:divide-[#FAF6EF]/15">
                    @include('admin.invoices._rows')
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($invoices, 'hasPages') && $invoices->hasPages())
        <div>{{ $invoices->links() }}</div>
    @endif

    @include('admin.invoices._modal')
    @include('admin.invoices._sample-modal')
</div>
@endsection

@include('admin.orders._manual-form-script')
