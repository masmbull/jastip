@extends('layouts.admin')
@section('title', 'Invoice | ' . setting('brand_name'))

@section('content')
<div class="p-4 sm:p-6 space-y-6" x-data="{ open: false }" @close-manual-invoice.window="open = false"
     @keydown.escape.window="open = false">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-up">
        <div>
            <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Invoice</h1>
            <p class="text-sm text-[#94A3B8] dark:text-[#cbd5e1] mt-1">Invoice manual yang dibuat admin</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.orders.index') }}"
               class="px-4 py-2 text-sm bg-[#E2E8F0] dark:bg-[#2e323b] text-[#64748B] dark:text-[#cbd5e1] rounded-lg hover:bg-[#CBD5E1] dark:hover:bg-[#404854] font-medium transition-colors">
                Semua Pesanan
            </a>
            <button type="button" @click="open = true"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
                <x-icon name="plus" class="w-4 h-4" /> Buat Invoice
            </button>
        </div>
    </div>

    @php($activeStatus = request('status', 'all'))
    <form method="GET" action="{{ route('admin.invoices.index') }}"
          class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] p-4 flex flex-col sm:flex-row gap-3 animate-fade-up"
          style="animation-delay: 60ms">
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
                class="px-4 py-2 text-sm bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
            Terapkan
        </button>
        @if(request()->filled('search') || $activeStatus !== 'all')
            <a href="{{ route('admin.invoices.index') }}"
               class="px-4 py-2 text-sm text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 rounded-lg font-medium text-center transition-colors">
                Reset
            </a>
        @endif
    </form>

    <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] overflow-hidden animate-fade-up transition-colors" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px]">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 py-3">No. Invoice</th>
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Item</th>
                        <th class="px-4 py-3">Total</th>
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
    </div>

    @if($invoices->hasPages())
        <div>{{ $invoices->links() }}</div>
    @endif

    @include('admin.invoices._modal')
</div>
@endsection

@include('admin.orders._manual-form-script')