@extends('layouts.admin')

@section('title', 'Laporan Penjualan | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">
    <x-page-header title="Laporan Penjualan" subtitle="Rekap pesanan per hari" class="animate-fade-up">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="from" class="block text-xs text-[#64748B] dark:text-[#cbd5e1] mb-1">Dari</label>
                <input id="from" type="date" name="from" value="{{ $from }}"
                    class="px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
            <div>
                <label for="to" class="block text-xs text-[#64748B] dark:text-[#cbd5e1] mb-1">Sampai</label>
                <input id="to" type="date" name="to" value="{{ $to }}"
                    class="px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
            <x-btn type="submit" variant="brand" icon="search">Filter</x-btn>
        </form>
    </x-page-header>

    <x-card padded="false" class="animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px]">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-right">Jumlah Pesanan</th>
                        <th class="px-4 py-3 text-right">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854] stagger">
                    @forelse($orders as $row)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9] num">{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right text-[#1E293B] dark:text-[#f1f5f9] num">{{ $row->count }}</td>
                            <td class="px-4 py-3 text-right font-medium text-orange-600 dark:text-orange-400 num">Rp {{ number_format((int) $row->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <x-empty-state icon="clipboard" title="Tidak ada data pada periode ini.">
                                    Ubah rentang tanggal lalu tekan Filter.
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($orders->isNotEmpty())
                    <tfoot>
                        <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] font-semibold">
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9]">Total</td>
                            <td class="px-4 py-3 text-right text-[#1E293B] dark:text-[#f1f5f9] num">{{ $orders->sum('count') }}</td>
                            <td class="px-4 py-3 text-right text-orange-600 dark:text-orange-400 num">Rp {{ number_format((int) $orders->sum('total'), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </x-card>
</div>
@endsection