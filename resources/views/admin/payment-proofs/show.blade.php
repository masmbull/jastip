@extends('layouts.admin')
@section('title', 'Detail Bukti Pembayaran - Admin')

@section('content')
<div class="space-y-6">

    <x-page-header title="{{ $paymentProof->order->order_number }}"
                   subtitle="Bukti Pembayaran QRIS"
                   class="animate-fade-up">
        <x-btn as="a" :href="route('admin.payment-proofs.index')" variant="ghost" icon="arrow-left">Kembali</x-btn>
        @if($paymentProof->status === 'pending')
            <x-badge tone="warning" class="text-sm px-3 py-1"><x-icon name="clock" class="w-4 h-4" /> Menunggu Verifikasi</x-badge>
        @elseif($paymentProof->status === 'verified')
            <x-badge tone="success" class="text-sm px-3 py-1"><x-icon name="check" class="w-4 h-4" /> Terverifikasi</x-badge>
        @else
            <x-badge tone="danger" class="text-sm px-3 py-1"><x-icon name="x" class="w-4 h-4" /> Ditolak</x-badge>
        @endif
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-card padded="false" class="lg:col-span-2 animate-fade-up" style="animation-delay: 80ms">
            <div class="bg-[#F8FAFC] dark:bg-[#2e323b] p-4 sm:p-8 flex items-center justify-center min-h-64 sm:min-h-96">
                <img src="{{ Storage::url($paymentProof->file_path) }}"
                     alt="Bukti Pembayaran"
                     class="max-w-full max-h-96 rounded-lg">
            </div>

            <div class="p-5 sm:p-6 border-t border-[#E2E8F0] dark:border-[#404854]">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="min-w-0">
                        <p class="text-[#64748B] dark:text-[#cbd5e1]">Nama File</p>
                        <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9] break-all">{{ $paymentProof->file_name }}</p>
                    </div>
                    <div>
                        <p class="text-[#64748B] dark:text-[#cbd5e1]">Ukuran</p>
                        <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_bytes($paymentProof->file_size) }}</p>
                    </div>
                    <div>
                        <p class="text-[#64748B] dark:text-[#cbd5e1]">Tanggal Unggah</p>
                        <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ $paymentProof->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-[#64748B] dark:text-[#cbd5e1]">Jenis File</p>
                        <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ strtoupper(pathinfo($paymentProof->file_name, PATHINFO_EXTENSION)) }}</p>
                    </div>
                </div>

                <x-btn as="a" :href="route('admin.payment-proofs.download', $paymentProof)" variant="outline" icon="download" class="mt-6 w-full">
                    Unduh File Asli
                </x-btn>
            </div>
        </x-card>

        <div class="space-y-6 animate-fade-up" style="animation-delay: 140ms">
            <x-card>
                <h3 class="text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4">Informasi Pesanan</h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="text-[#64748B] dark:text-[#cbd5e1]">No. Pesanan</span>
                        <span class="font-mono font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $paymentProof->order->order_number }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-[#64748B] dark:text-[#cbd5e1]">Pelanggan</span>
                        <span class="font-medium text-right text-[#1E293B] dark:text-[#f1f5f9]">{{ $paymentProof->order->customer_name }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-[#64748B] dark:text-[#cbd5e1]">WhatsApp</span>
                        <a href="{{ whatsapp_url($paymentProof->order->customer_whatsapp) }}"
                           target="_blank" rel="noopener"
                           class="font-medium text-orange-600 dark:text-orange-400 hover:underline num">
                            {{ $paymentProof->order->customer_whatsapp }}
                        </a>
                    </div>
                    <div class="flex justify-between gap-4 border-t border-[#E2E8F0] dark:border-[#404854] pt-3">
                        <span class="text-[#64748B] dark:text-[#cbd5e1]">Total</span>
                        <span class="font-bold text-orange-600 dark:text-orange-400 text-lg num">
                            {{ format_price($paymentProof->order->total) }}
                        </span>
                    </div>
                </div>

                <x-btn as="a" :href="route('admin.orders.show', $paymentProof->order)" variant="outline" icon="receipt-percent" class="mt-4 w-full">
                    Lihat Detail Pesanan
                </x-btn>
            </x-card>

            @if($paymentProof->admin_notes)
                <x-card>
                    <h3 class="text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-3">Catatan Admin</h3>
                    <p class="text-[#64748B] dark:text-[#cbd5e1] text-sm whitespace-pre-wrap">{{ $paymentProof->admin_notes }}</p>
                </x-card>
            @endif

            @if($paymentProof->status === 'pending')
                <x-card class="space-y-5">
                    <h3 class="text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9]">Verifikasi Pembayaran</h3>

                    <form action="{{ route('admin.payment-proofs.verify', $paymentProof) }}"
                          method="POST"
                          data-confirm="Verifikasi bukti pembayaran ini dan konfirmasi pesanan?"
                          class="space-y-3">
                        @csrf
                        <label for="verify-notes" class="sr-only">Catatan verifikasi</label>
                        <textarea id="verify-notes" name="notes"
                                  placeholder="Catatan verifikasi (opsional)"
                                  class="w-full px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] rounded-lg text-sm text-[#1E293B] dark:text-[#f1f5f9] placeholder-[#94A3B8] focus:outline-none focus:ring-2 focus:ring-orange-500"
                                  rows="3"></textarea>
                        <x-btn type="submit" variant="brand" icon="check-circle" class="w-full">Verifikasi &amp; Terima Pembayaran</x-btn>
                    </form>

                    <form action="{{ route('admin.payment-proofs.reject', $paymentProof) }}"
                          method="POST"
                          data-confirm="Tolak bukti pembayaran ini? Pelanggan akan diminta mengunggah ulang."
                          class="space-y-3 pt-5 border-t border-[#E2E8F0] dark:border-[#404854]">
                        @csrf
                        <label for="reject-notes" class="sr-only">Alasan penolakan</label>
                        <textarea id="reject-notes" name="notes"
                                  placeholder="Alasan penolakan (wajib diisi)"
                                  class="w-full px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] rounded-lg text-sm text-[#1E293B] dark:text-[#f1f5f9] placeholder-[#94A3B8] focus:outline-none focus:ring-2 focus:ring-red-500"
                                  rows="3"
                                  required></textarea>
                        <x-btn type="submit" variant="danger" icon="x" class="w-full">Tolak &amp; Minta Upload Ulang</x-btn>
                    </form>
                </x-card>
            @else
                <x-card>
                    @if($paymentProof->status === 'verified')
                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-emerald-100 dark:bg-emerald-500/20 rounded-lg shrink-0">
                                <x-icon name="check-circle" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div>
                                <p class="font-medium text-emerald-900 dark:text-emerald-200">Pembayaran Terverifikasi</p>
                                <p class="text-sm text-emerald-700 dark:text-emerald-300 mt-1 num">
                                    Diverifikasi pada {{ $paymentProof->verified_at->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-red-100 dark:bg-red-500/20 rounded-lg shrink-0">
                                <x-icon name="x" class="w-6 h-6 text-red-600 dark:text-red-400" />
                            </div>
                            <div>
                                <p class="font-medium text-red-900 dark:text-red-200">Pembayaran Ditolak</p>
                                <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                    Pelanggan diminta untuk mengunggah ulang bukti pembayaran
                                </p>
                            </div>
                        </div>
                    @endif
                </x-card>
            @endif
        </div>
    </div>

</div>
@endsection