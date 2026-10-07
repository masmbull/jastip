@extends('layouts.admin')

@section('title', 'Laporan Penjualan | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Laporan Penjualan</h1>
            <p class="text-sm text-[#94A3B8] dark:text-[#cbd5e1] mt-1">Rekap pesanan per hari</p>
        </div>
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs text-[#64748B] dark:text-[#cbd5e1] mb-1">Dari</label>
                <input type="date" name="from" value="{{ $from }}"
                    class="px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] dark:bg-[#2e323b] dark:text-[#f1f5f9] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
            <div>
                <label class="block text-xs text-[#64748B] dark:text-[#cbd5e1] mb-1">Sampai</label>
                <input type="date" name="to" value="{{ $to }}"
                    class="px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] dark:bg-[#2e323b] dark:text-[#f1f5f9] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
            <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg">Filter</button>
        </form>
    </div>

    <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Jumlah Pesanan</th>
                        <th class="px-4 py-3">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                    @forelse($orders as $row)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9]">{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9]">{{ $row->count }}</td>
                            <td class="px-4 py-3 font-medium text-orange-600 dark:text-orange-400">Rp {{ number_format((int) $row->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-12 text-center text-[#94A3B8]">Tidak ada data pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($orders->isNotEmpty())
                    <tfoot>
                        <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] font-semibold">
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9]">Total</td>
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9]">{{ $orders->sum('count') }}</td>
                            <td class="px-4 py-3 text-orange-600 dark:text-orange-400">Rp {{ number_format((int) $orders->sum('total'), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection