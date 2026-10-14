@extends('layouts.app')

@section('title', 'Pesanan Dikirim')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 dark:bg-orange-500/15 rounded-full mb-4 text-orange-600 dark:text-orange-400">
            <x-icon name="check" class="w-9 h-9" :stroke="2" />
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Pesanan Berhasil!</h1>
        <p class="text-[#64748B] dark:text-[#cbd5e1] mt-2">
            @if (qris_enabled())
                Selesaikan pembayaran via QRIS agar pesanan kamu segera diproses.
            @else
                Pesanan kamu sudah kami terima. Admin akan kabari cara bayarnya lewat WhatsApp.
            @endif
        </p>
    </div>

    {{-- QRIS Card --}}
    <x-card class="mb-6 rounded-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9]">
                {{ qris_enabled() ? 'Bayar via QRIS' : 'Rincian Pesanan' }}
            </h3>
            @if (qris_enabled())
                <x-badge tone="brand">QRIS</x-badge>
            @endif
        </div>

        @if (! empty($orderData['qris_image']))
            <div class="bg-[#F1F5F9] dark:bg-[#2e323b] rounded-xl p-4 flex flex-col items-center">
                <img src="{{ $orderData['qris_image'] }}"
                     alt="Kode QRIS {{ $orderData['qris_merchant'] ?? 'NITIP DI END' }}"
                     class="w-64 sm:w-72 h-auto rounded-lg border-4 border-white dark:border-[#404854] shadow-sm"
                     loading="eager">
                <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-3 text-center">
                    Buka <strong>OVO / GoPay / Dana / ShopeePay / m-Banking</strong>, lalu scan QR ini.
                </p>
            </div>
        @elseif (qris_enabled())
            <div class="bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 rounded-xl p-4 text-sm text-orange-700 dark:text-orange-300">
                QRIS belum diunggah admin. Silakan konfirmasi pesanan ke WhatsApp admin untuk diproses manual.
            </div>
        @else
            <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-xl p-4 text-sm text-amber-800 dark:text-amber-300">
                Pembayaran QRIS sedang nonaktif. Konfirmasi pesanan ke WhatsApp admin, nanti admin yang kirim cara bayarnya.
            </div>
        @endif

        {{-- Amount card --}}
        <div class="mt-6 bg-[#F1F5F9] dark:bg-[#2e323b] rounded-xl p-5">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-[#64748B] dark:text-[#cbd5e1]">No. Pesanan</span>
                <span class="font-mono font-bold text-[#1E293B] dark:text-[#f1f5f9] num">{{ $orderData['order_number'] ?? '-' }}</span>
            </div>
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-[#64748B] dark:text-[#cbd5e1]">Subtotal Produk</span>
                <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($orderData['subtotal'] ?? 0) }}</span>
            </div>
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-[#64748B] dark:text-[#cbd5e1]">Metode Pembayaran</span>
                <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ qris_enabled() ? 'QRIS' : 'Belum ditentukan' }}</span>
            </div>
            <div class="flex items-center justify-between text-sm mb-3">
                <span class="text-[#64748B] dark:text-[#cbd5e1]">Ongkir</span>
                <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($orderData['shipping_cost'] ?? 0) }}</span>
            </div>
            @if(($orderData['fee'] ?? 0) > 0)
            <div class="flex items-center justify-between text-sm mb-3">
                <span class="text-[#64748B] dark:text-[#cbd5e1]">Biaya Fee</span>
                <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($orderData['fee'] ?? 0) }}</span>
            </div>
            @endif
            @if(($orderData['discount'] ?? 0) > 0)
            <div class="flex items-center justify-between text-sm mb-3">
                <span class="text-[#64748B] dark:text-[#cbd5e1]">Diskon{{ !empty($orderData['coupon_code']) ? ' (' . $orderData['coupon_code'] . ')' : '' }}</span>
                <span class="font-medium text-green-600 dark:text-green-400 num">-{{ format_price($orderData['discount']) }}</span>
            </div>
            @endif
            <div class="border-t border-[#E2E8F0] dark:border-[#404854] pt-3 flex items-center justify-between">
                <span class="text-base font-bold text-[#1E293B] dark:text-[#f1f5f9]">Total Bayar</span>
                <span class="text-2xl font-bold text-orange-600 dark:text-orange-400 num">{{ format_price($orderData['total'] ?? 0) }}</span>
            </div>
        </div>

        {{-- Steps --}}
        @if (qris_enabled())
            <ol class="mt-6 space-y-2 text-sm text-[#64748B] dark:text-[#cbd5e1]">
                <li class="flex gap-3"><span class="font-bold text-orange-600 dark:text-orange-400 num">1.</span> Buka aplikasi e-wallet / m-banking kamu.</li>
                <li class="flex gap-3"><span class="font-bold text-orange-600 dark:text-orange-400 num">2.</span> Scan QRIS di atas dan bayar sesuai nominal total.</li>
                <li class="flex gap-3"><span class="font-bold text-orange-600 dark:text-orange-400 num">3.</span> Setelah bayar, klik tombol WhatsApp untuk kirim bukti.</li>
            </ol>
        @else
            <ol class="mt-6 space-y-2 text-sm text-[#64748B] dark:text-[#cbd5e1]">
                <li class="flex gap-3"><span class="font-bold text-orange-600 dark:text-orange-400 num">1.</span> Klik tombol WhatsApp di bawah buat konfirmasi pesanan.</li>
                <li class="flex gap-3"><span class="font-bold text-orange-600 dark:text-orange-400 num">2.</span> Admin balas dengan total final dan cara bayarnya.</li>
                <li class="flex gap-3"><span class="font-bold text-orange-600 dark:text-orange-400 num">3.</span> Bayar, lalu kirim bukti transfer ke WhatsApp yang sama.</li>
            </ol>
        @endif
    </x-card>

    {{-- CTA buttons --}}
    <div class="space-y-3">
        @if (! empty($orderData['whatsapp_url']))
            <a href="{{ $orderData['whatsapp_url'] }}"
               target="_blank" rel="noopener noreferrer"
               class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-full shadow transition-colors focus:outline-none focus:ring-2 focus:ring-green-300">
                <x-icon name="chat" class="w-5 h-5" />
                {{ qris_enabled() ? 'Sudah Bayar? Konfirmasi via WhatsApp' : 'Konfirmasi Pesanan via WhatsApp' }}
            </a>
        @endif

        <div class="flex flex-wrap gap-3 justify-center pt-2">
            <x-btn as="a" :href="route('home')" variant="ghost" class="px-5 py-2">
                Kembali ke Beranda
            </x-btn>
            <x-btn as="a" :href="route('products.index')" variant="brand" class="px-5 py-2 rounded-full">
                Lanjut Belanja
            </x-btn>
        </div>
    </div>
</div>
@endsection