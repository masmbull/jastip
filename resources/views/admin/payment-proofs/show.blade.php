@extends('layouts.admin')
@section('title', 'Detail Bukti Pembayaran - Admin')

@section('content')
<div class="flex-1 p-6 sm:p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('admin.payment-proofs.index') }}"
           class="inline-flex items-center gap-2 text-orange-600 hover:text-orange-700 mb-6 font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Daftar
        </a>

        <!-- Header -->
        <div class="flex items-start justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#1E293B]">{{ $paymentProof->order->order_number }}</h1>
                <p class="text-[#64748B] mt-1">Bukti Pembayaran QRIS</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                @if($paymentProof->status === 'pending')
                    bg-amber-100 text-amber-800
                @elseif($paymentProof->status === 'verified')
                    bg-emerald-100 text-emerald-800
                @else
                    bg-red-100 text-red-800
                @endif
            ">
                @if($paymentProof->status === 'pending')
                    ⏳ Menunggu Verifikasi
                @elseif($paymentProof->status === 'verified')
                    ✓ Terverifikasi
                @else
                    ✕ Ditolak
                @endif
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Proof Image -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-[#F8FAFC] p-8 flex items-center justify-center min-h-96">
                        <img src="{{ Storage::url($paymentProof->file_path) }}"
                             alt="Bukti Pembayaran"
                             class="max-w-full max-h-96 rounded-lg">
                    </div>

                    <!-- File Info -->
                    <div class="p-6 border-t border-[#E2E8F0]">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-[#64748B]">Nama File</p>
                                <p class="font-medium text-[#1E293B]">{{ $paymentProof->file_name }}</p>
                            </div>
                            <div>
                                <p class="text-[#64748B]">Ukuran</p>
                                <p class="font-medium text-[#1E293B]">{{ format_bytes($paymentProof->file_size) }}</p>
                            </div>
                            <div>
                                <p class="text-[#64748B]">Tanggal Unggah</p>
                                <p class="font-medium text-[#1E293B]">{{ $paymentProof->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-[#64748B]">Jenis File</p>
                                <p class="font-medium text-[#1E293B]">{{ strtoupper(pathinfo($paymentProof->file_name, PATHINFO_EXTENSION)) }}</p>
                            </div>
                        </div>

                        <a href="{{ route('admin.payment-proofs.download', $paymentProof) }}"
                           class="mt-6 w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download File Asli
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order & Action Panel -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-[#1E293B] mb-4">Informasi Pesanan</h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">No. Pesanan</span>
                            <span class="font-medium text-[#1E293B]">{{ $paymentProof->order->order_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">Pelanggan</span>
                            <span class="font-medium text-[#1E293B]">{{ $paymentProof->order->customer_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">WhatsApp</span>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $paymentProof->order->customer_whatsapp) }}"
                               target="_blank"
                               class="font-medium text-orange-600 hover:text-orange-700">
                                {{ $paymentProof->order->customer_whatsapp }}
                            </a>
                        </div>
                        <div class="flex justify-between border-t border-[#E2E8F0] pt-3">
                            <span class="text-[#64748B]">Total</span>
                            <span class="font-bold text-orange-600 text-lg">
                                {{ format_price($paymentProof->order->total) }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('admin.orders.show', $paymentProof->order) }}"
                       class="mt-4 w-full flex items-center justify-center gap-2 px-4 py-2 border border-orange-300 text-orange-600 hover:bg-orange-50 rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Lihat Detail Pesanan
                    </a>
                </div>

                <!-- Admin Notes (if already processed) -->
                @if($paymentProof->admin_notes)
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-bold text-[#1E293B] mb-3">Catatan Admin</h3>
                        <p class="text-[#64748B] text-sm">{{ $paymentProof->admin_notes }}</p>
                    </div>
                @endif

                <!-- Action Buttons (only if pending) -->
                @if($paymentProof->status === 'pending')
                    <div class="bg-white rounded-lg shadow-lg p-6 space-y-4">
                        <h3 class="text-lg font-bold text-[#1E293B]">Verifikasi Pembayaran</h3>

                        <!-- Verify Form -->
                        <form action="{{ route('admin.payment-proofs.verify', $paymentProof) }}"
                              method="POST"
                              onsubmit="return confirm('Apakah kamu yakin ingin memverifikasi bukti pembayaran ini?');">
                            @csrf
                            <textarea name="notes"
                                      placeholder="Catatan verifikasi (opsional)"
                                      class="w-full px-3 py-2 border border-[#E2E8F0] rounded-lg text-sm text-[#1E293B] placeholder-[#94A3B8] focus:outline-none focus:ring-2 focus:ring-orange-500 mb-3"
                                      rows="3"></textarea>
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Verifikasi & Terima Pembayaran
                            </button>
                        </form>

                        <!-- Reject Form -->
                        <form action="{{ route('admin.payment-proofs.reject', $paymentProof) }}"
                              method="POST"
                              onsubmit="return confirm('Apakah kamu yakin ingin menolak bukti pembayaran ini?');">
                            @csrf
                            <textarea name="notes"
                                      placeholder="Alasan penolakan (wajib diisi)"
                                      class="w-full px-3 py-2 border border-[#E2E8F0] rounded-lg text-sm text-[#1E293B] placeholder-[#94A3B8] focus:outline-none focus:ring-2 focus:ring-red-500 mb-3"
                                      rows="3"
                                      required></textarea>
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Tolak & Minta Upload Ulang
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Verification Time Info -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        @if($paymentProof->status === 'verified')
                            <div class="flex items-start gap-4">
                                <div class="p-3 bg-emerald-100 rounded-lg">
                                    <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-emerald-900">Pembayaran Terverifikasi</p>
                                    <p class="text-sm text-emerald-700 mt-1">
                                        Diverifikasi pada {{ $paymentProof->verified_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-start gap-4">
                                <div class="p-3 bg-red-100 rounded-lg">
                                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-red-900">Pembayaran Ditolak</p>
                                    <p class="text-sm text-red-700 mt-1">
                                        Pelanggan diminta untuk mengunggah ulang bukti pembayaran
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
