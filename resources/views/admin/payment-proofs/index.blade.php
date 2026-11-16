@extends('layouts.admin')
@section('title', 'Bukti Pembayaran - Admin')

@section('content')
<div class="space-y-6">

    <x-page-header title="Bukti Pembayaran" subtitle="Kelola verifikasi bukti pembayaran pelanggan" class="animate-fade-up">
        <div class="text-right">
            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400 num">{{ $counts['pending'] }}</p>
            <p class="text-xs text-[#64748B] dark:text-[#cbd5e1]">Menunggu verifikasi</p>
        </div>
    </x-page-header>

    {{-- Upload bukti dari WhatsApp (bukti yang masuk lewat chat, bukan checkout) --}}
    <x-card class="animate-fade-up" style="animation-delay: 110ms">
        <details class="group" @if($q !== '') open @endif>
            <summary class="flex items-center justify-between gap-3 cursor-pointer list-none select-none">
                <span class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-[8px] bg-[#F97316] text-white">
                        <x-icon name="upload" class="w-4 h-4" />
                    </span>
                    <span>
                        <span class="block text-sm font-extrabold text-[#1E293B] dark:text-[#f1f5f9]">Upload bukti dari WhatsApp</span>
                        <span class="block text-xs text-[#64748B] dark:text-[#cbd5e1]">Untuk bukti yang dikirim pelanggan lewat chat. Setelah diunggah, pesanan langsung ditandai LUNAS.</span>
                    </span>
                </span>
                <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-[#64748B] dark:text-[#cbd5e1] transition-transform group-open:rotate-180" />
            </summary>

            <div class="mt-4 border-t border-[#E2E8F0] dark:border-[#404854] pt-4">
                <form method="GET" action="{{ route('admin.payment-proofs.index') }}" class="flex flex-col sm:flex-row gap-2" role="search">
                    <label for="bukti-q" class="sr-only">Cari pesanan</label>
                    <input id="bukti-q" type="text" name="q" value="{{ $q }}" autocomplete="off"
                           placeholder="Cari no. pesanan / nama / WA pelanggan…"
                           class="w-full px-4 py-2.5 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <x-btn type="submit" variant="outline" icon="search" class="sm:w-auto">Cari</x-btn>
                </form>

                @if($candidates !== null)
                    @if($candidates->isEmpty())
                        <p class="mt-3 text-sm text-[#64748B] dark:text-[#cbd5e1]">Tidak ada pesanan belum lunas yang cocok dengan &ldquo;{{ $q }}&rdquo;.</p>
                    @else
                        <ul class="mt-3 divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                            @foreach($candidates as $candidate)
                                <li class="py-3 flex flex-col lg:flex-row lg:items-center gap-3">
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('admin.orders.show', $candidate) }}"
                                           class="font-mono text-sm font-bold text-orange-600 dark:text-orange-400 hover:underline">{{ $candidate->order_number }}</a>
                                        <span class="ml-2 text-sm text-[#1E293B] dark:text-[#f1f5f9]">{{ $candidate->customer_name }}</span>
                                        <span class="block text-xs text-[#64748B] dark:text-[#cbd5e1] mt-0.5 num">
                                            Sisa {{ format_price($candidate->remaining) }} dari {{ format_price($candidate->total) }}
                                            &middot; {{ $candidate->payment_method ?? 'qris' }} &middot; {{ $candidate->created_at->format('d M Y H:i') }}
                                        </span>
                                    </div>
                                    <form method="POST" action="{{ route('admin.payment-proofs.upload') }}" enctype="multipart/form-data"
                                          class="flex flex-col sm:flex-row sm:items-center gap-2 shrink-0">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{ $candidate->id }}">
                                        <label class="sr-only" for="proof-{{ $candidate->id }}">File bukti pembayaran</label>
                                        <input id="proof-{{ $candidate->id }}" type="file" name="proof" accept="image/jpeg,image/png,image/jpg" required
                                               class="block w-full sm:max-w-[230px] text-xs text-[#64748B] dark:text-[#cbd5e1] file:mr-2 file:px-3 file:py-2 file:rounded-[8px] file:border-0 file:bg-[#F1F5F9] dark:file:bg-[#2e323b] file:text-[#1E293B] dark:file:text-[#f1f5f9] file:font-bold file:cursor-pointer hover:file:bg-[#E2E8F0] dark:hover:file:bg-[#404854]">
                                        <x-btn type="submit" variant="brand" icon="check-circle">Lunasi</x-btn>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                        <p class="mt-2 text-xs text-[#64748B] dark:text-[#cbd5e1]">Menampilkan maksimal 8 pesanan belum lunas terbaru yang cocok.</p>
                    @endif
                @endif
            </div>
        </details>
    </x-card>

    <div class="flex gap-1 sm:gap-2 overflow-x-auto border-b border-[#E2E8F0] dark:border-[#404854] animate-fade-up" style="animation-delay: 80ms">
        <a href="{{ route('admin.payment-proofs.index', ['status' => 'all']) }}"
           class="px-3 sm:px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition-colors {{ $status === 'all' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-[#64748B] dark:text-[#cbd5e1] hover:text-[#1E293B] dark:hover:text-[#f1f5f9]' }}"
           @if($status === 'all') aria-current="page" @endif>
            Semua ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.payment-proofs.index', ['status' => 'pending']) }}"
           class="px-3 sm:px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition-colors {{ $status === 'pending' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-[#64748B] dark:text-[#cbd5e1] hover:text-[#1E293B] dark:hover:text-[#f1f5f9]' }}"
           @if($status === 'pending') aria-current="page" @endif>
            Menunggu ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('admin.payment-proofs.index', ['status' => 'verified']) }}"
           class="px-3 sm:px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition-colors {{ $status === 'verified' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-[#64748B] dark:text-[#cbd5e1] hover:text-[#1E293B] dark:hover:text-[#f1f5f9]' }}"
           @if($status === 'verified') aria-current="page" @endif>
            Terverifikasi ({{ $counts['verified'] }})
        </a>
        <a href="{{ route('admin.payment-proofs.index', ['status' => 'rejected']) }}"
           class="px-3 sm:px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition-colors {{ $status === 'rejected' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-[#64748B] dark:text-[#cbd5e1] hover:text-[#1E293B] dark:hover:text-[#f1f5f9]' }}"
           @if($status === 'rejected') aria-current="page" @endif>
            Ditolak ({{ $counts['rejected'] }})
        </a>
    </div>

    <x-card padded="false" class="animate-fade-up" style="animation-delay: 140ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px]">
                <thead class="bg-[#F1F5F9] dark:bg-[#2e323b] border-b border-[#E2E8F0] dark:border-[#404854]">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">No. Pesanan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">Pelanggan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">File</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">Unggah</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854] stagger">
                    @forelse($proofs as $proof)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-4 py-3 text-sm font-medium">
                                <a href="{{ route('admin.orders.show', $proof->order) }}"
                                   class="font-mono text-orange-600 dark:text-orange-400 hover:underline">
                                    {{ $proof->order->order_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="text-[#1E293B] dark:text-[#f1f5f9] font-medium">{{ $proof->order->customer_name }}</div>
                                <div class="text-[#64748B] dark:text-[#cbd5e1] text-xs num">{{ substr($proof->order->customer_whatsapp, -10) }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="text-[#1E293B] dark:text-[#f1f5f9]">{{ $proof->file_name }}</div>
                                <div class="text-[#64748B] dark:text-[#cbd5e1] text-xs num">{{ format_bytes($proof->file_size) }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-[#64748B] dark:text-[#cbd5e1]">
                                {{ $proof->created_at->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3">
                                @if($proof->status === 'pending')
                                    <x-badge tone="warning"><x-icon name="clock" class="w-3.5 h-3.5" /> Menunggu</x-badge>
                                @elseif($proof->status === 'verified')
                                    <x-badge tone="success"><x-icon name="check" class="w-3.5 h-3.5" /> Terverifikasi</x-badge>
                                @else
                                    <x-badge tone="danger"><x-icon name="x" class="w-3.5 h-3.5" /> Ditolak</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.payment-proofs.show', $proof) }}"
                                   class="inline-flex items-center gap-1.5 text-sm font-medium text-orange-600 dark:text-orange-400 hover:underline rounded focus:outline-none focus:ring-2 focus:ring-orange-300">
                                    <x-icon name="eye" class="w-4 h-4" /> Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="receipt-percent" title="Tidak ada bukti pembayaran">
                                    @isset($status)
                                        Tidak ada bukti dengan status ini.
                                    @else
                                        Bukti yang diunggah pelanggan akan tampil di sini.
                                    @endisset
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    @if($proofs->hasPages())
        <div>{{ $proofs->links() }}</div>
    @endif

</div>
@endsection