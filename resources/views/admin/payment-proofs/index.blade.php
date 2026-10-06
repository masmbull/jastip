@extends('layouts.admin')
@section('title', 'Bukti Pembayaran - Admin')

@section('content')
<div class="flex-1 p-6 sm:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#1E293B]">Bukti Pembayaran</h1>
                <p class="text-[#64748B] mt-1">Kelola verifikasi bukti pembayaran pelanggan</p>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-orange-600">{{ $counts['pending'] }}</p>
                <p class="text-sm text-[#64748B]">Menunggu verifikasi</p>
            </div>
        </div>

        <!-- Status Filter Tabs -->
        <div class="flex gap-2 mb-6 border-b border-[#E2E8F0]">
            <a href="{{ route('admin.payment-proofs.index', ['status' => 'all']) }}"
               class="px-4 py-2 font-medium {{ $status === 'all' ? 'border-b-2 border-orange-500 text-orange-600' : 'text-[#64748B] hover:text-[#1E293B]' }}">
                Semua ({{ $counts['all'] }})
            </a>
            <a href="{{ route('admin.payment-proofs.index', ['status' => 'pending']) }}"
               class="px-4 py-2 font-medium {{ $status === 'pending' ? 'border-b-2 border-orange-500 text-orange-600' : 'text-[#64748B] hover:text-[#1E293B]' }}">
                Menunggu ({{ $counts['pending'] }})
            </a>
            <a href="{{ route('admin.payment-proofs.index', ['status' => 'verified']) }}"
               class="px-4 py-2 font-medium {{ $status === 'verified' ? 'border-b-2 border-orange-500 text-orange-600' : 'text-[#64748B] hover:text-[#1E293B]' }}">
                Terverifikasi ({{ $counts['verified'] }})
            </a>
            <a href="{{ route('admin.payment-proofs.index', ['status' => 'rejected']) }}"
               class="px-4 py-2 font-medium {{ $status === 'rejected' ? 'border-b-2 border-orange-500 text-orange-600' : 'text-[#64748B] hover:text-[#1E293B]' }}">
                Ditolak ({{ $counts['rejected'] }})
            </a>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-[#1E293B]">No. Pesanan</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-[#1E293B]">Pelanggan</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-[#1E293B]">File</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-[#1E293B]">Unggah</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-[#1E293B]">Status</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-[#1E293B]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @forelse($proofs as $proof)
                        <tr class="hover:bg-[#F8FAFC] transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-[#1E293B]">
                                <a href="{{ route('admin.orders.show', $proof->order) }}" 
                                   class="text-orange-600 hover:text-orange-700 font-semibold">
                                    {{ $proof->order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="text-[#1E293B] font-medium">{{ $proof->order->customer_name }}</div>
                                <div class="text-[#64748B] text-xs">{{ substr($proof->order->customer_whatsapp, -10) }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="text-[#1E293B]">{{ $proof->file_name }}</div>
                                <div class="text-[#64748B] text-xs">{{ format_bytes($proof->file_size) }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#64748B]">
                                {{ $proof->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($proof->status === 'pending')
                                        bg-amber-100 text-amber-800
                                    @elseif($proof->status === 'verified')
                                        bg-emerald-100 text-emerald-800
                                    @else
                                        bg-red-100 text-red-800
                                    @endif
                                ">
                                    @if($proof->status === 'pending')
                                        ⏳ Menunggu
                                    @elseif($proof->status === 'verified')
                                        ✓ Terverifikasi
                                    @else
                                        ✕ Ditolak
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex gap-2 justify-end">
                                    <a href="{{ route('admin.payment-proofs.show', $proof) }}"
                                       class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                                        Lihat
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-[#64748B]">
                                Tidak ada bukti pembayaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $proofs->links() }}
        </div>
    </div>
</div>
@endsection
