@extends('layouts.app')

@section('title', 'Pesanan Dikirim')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 rounded-full mb-4">
            <svg class="w-9 h-9 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-[#1E293B]">Pesanan Berhasil!</h1>
        <p class="text-[#64748B] mt-2">
            Selesaikan pembayaran via QRIS agar pesanan kamu segera diproses.
        </p>
    </div>

    {{-- QRIS Card --}}
    <div class="bg-white rounded-2xl shadow-md p-6 md:p-8 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-[#1E293B]">Bayar via QRIS</h3>
            <span class="text-xs font-medium px-2.5 py-1 bg-orange-100 text-orange-700 rounded-full">QRIS</span>
        </div>

        @if (! empty($orderData['qris_image']))
            <div class="bg-[#F1F5F9] rounded-xl p-4 flex flex-col items-center">
                <img src="{{ $orderData['qris_image'] }}"
                     alt="QRIS {{ $orderData['qris_merchant'] ?? 'NITIP DI END' }}"
                     class="w-72 h-auto rounded-lg border-4 border-white shadow-sm"
                     loading="eager">
                <p class="text-xs text-[#64748B] mt-3 text-center">
                    Buka <strong>OVO / GoPay / Dana / ShopeePay / m-Banking</strong> &rarr; Scan QR ini.
                </p>
            </div>
        @else
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-sm text-orange-700">
                QRIS belum diunggah admin. Silakan konfirmasi pesanan ke WhatsApp admin untuk diproses manual.
            </div>
        @endif

        {{-- Amount card --}}
        <div class="mt-6 bg-[#F1F5F9] rounded-xl p-5">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-[#64748B]">No. Pesanan</span>
                <span class="font-mono font-bold text-[#1E293B]">{{ $orderData['order_number'] ?? '—' }}</span>
            </div>
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-[#64748B]">Subtotal Produk</span>
                <span class="font-medium text-[#1E293B]">{{ format_price($orderData['subtotal'] ?? 0) }}</span>
                <div class="flex items-center justify-between text-sm mb-2">
                    <span class="text-[#64748B]">Metode Pembayaran</span>
                    <span class="font-medium text-[#1E293B]">QRIS</span>
                </div>
            </div>
                                    <div class="flex items-center justify-between text-sm mb-3">
                <span class="text-[#64748B]">Ongkir</span>
                <span class="font-medium text-[#1E293B]">{{ format_price($orderData['shipping_cost'] ?? 0) }}</span>
            </div>
            @if(($orderData['fee'] ?? 0) > 0)
            <div class="flex items-center justify-between text-sm mb-3">
                <span class="text-[#64748B]">Biaya Fee</span>
                <span class="font-medium text-[#1E293B]">{{ format_price($orderData['fee'] ?? 0) }}</span>
            </div>
            @endif
            <div class="border-t border-[#E2E8F0] pt-3 flex items-center justify-between">
                <span class="text-base font-bold text-[#1E293B]">Total Bayar</span>
                <span class="text-2xl font-bold text-orange-600">{{ format_price($orderData['total'] ?? 0) }}</span>
            </div>
        </div>

        {{-- Steps --}}
        <ol class="mt-6 space-y-2 text-sm text-[#64748B]">
            <li class="flex gap-3"><span class="font-bold text-orange-600">1.</span> Buka aplikasi e-wallet / m-banking kamu.</li>
            <li class="flex gap-3"><span class="font-bold text-orange-600">2.</span> Scan QRIS di atas dan bayar sesuai nominal total.</li>
            <li class="flex gap-3"><span class="font-bold text-orange-600">3.</span> Setelah bayar, klik tombol WA untuk kirim bukti.</li>
        </ol>
    </div>

    {{-- CTA buttons --}}
    <div class="space-y-3">
        @if (! empty($orderData['whatsapp_url']))
            <a href="{{ $orderData['whatsapp_url'] }}"
               target="_blank" rel="noopener noreferrer"
               class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-full shadow transition-colors focus:outline-none focus:ring-2 focus:ring-green-300">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20.52 3.48A11.94 11.94 0 0012 0a11.96 11.96 0 00-8.44 20.73l-2.53 7.55 7.67-2.08A11.88 11.88 0 0012 24c6.62 0 12-5.38 12-12 0-3.21-1.25-6.21-3.48-8.42z"/>
                </svg>
                Sudah Bayar? Konfirmasi via WhatsApp
            </a>
        @endif

        <div class="flex gap-3 justify-center pt-2">
            <a href="{{ route('home') }}" class="px-5 py-2 text-sm text-[#64748B] hover:text-[#1E293B] font-medium">
                Kembali ke Beranda
            </a>
            <a href="{{ route('products.index') }}" class="px-5 py-2 text-sm bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-full shadow">
                Lanjut Belanja
            </a>
        </div>
    </div>
</div>
@endsection