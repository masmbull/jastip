@extends('layouts.app')

@section('title', 'Pesanan Dikirim')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
    {{-- Checkmark animation --}}
    <div class="mb-8 animate-scale-in">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-6">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-[#333333] animate-fade-up">Terima kasih!</h1>
        <p class="text-[#666666] mt-2 animate-fade-up" style="animation-delay: 120ms">
            Pesanan kamu sudah siap dikirim via WhatsApp.
        </p>
    </div>

    {{-- Order info card --}}
    <div class="bg-white rounded-xl shadow-md p-6 mb-8 animate-fade-up" style="animation-delay: 240ms">
        <h3 class="text-lg font-semibold text-[#333333] mb-4">Nomor Resi</h3>
        <p class="text-2xl font-mono font-bold text-rose-600 mb-4">
            {{ $orderData['order_number'] ?? session('order_number', '—') }}
        </p>

        <p class="text-sm text-[#666666] mb-4">
            Total pembayaran:
            <span class="font-semibold text-[#333333]">{{ format_price($orderData['total'] ?? session('order_total', 0)) }}</span>
        </p>

        <div class="bg-[#F8F5F0] rounded-lg p-4 mb-4">
            <p class="text-sm text-[#666666]">
                Cek WhatsApp admin untuk konfirmasi & pembayaran.
            </p>
        </div>

        @if(! empty($orderData['whatsapp_url']))
                        <a href="{{ $orderData['whatsapp_url'] }}"
               target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow transition-colors focus:outline-none focus:ring-2 focus:ring-green-300">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20.52 3.48A11.94 11.94 0 0012 0a11.96 11.96 0 00-8.44 20.73l-2.53 7.55 7.67-2.08A11.88 11.88 0 0012 24c6.62 0 12-5.38 12-12 0-3.21-1.25-6.21-3.48-8.42z" />
                </svg>
                Chat ke Admin
            </a>
        @endif
    </div>

    {{-- Navigation links --}}
    <div class="flex gap-4 justify-center animate-fade-up" style="animation-delay: 360ms">
        <a href="{{ route('home') }}"
           class="px-6 py-3 text-[#666666] font-medium hover:text-[#333333] transition-colors">
            Kembali ke Beranda
        </a>
        <a href="{{ route('products.index') }}"
           class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg shadow transition-colors focus:outline-none focus:ring-2 focus:ring-rose-300">
            Lanjut Belanja
        </a>
    </div>
</div>
@endsection