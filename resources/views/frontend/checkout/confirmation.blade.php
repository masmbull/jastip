@extends('layouts.app')

@section('title', 'Pesanan Dikirim')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
    <div class="mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-[#333333]">Terima kasih!</h1>
        <p class="text-[#666666] mt-2">Pesanan kamu sudah siap dikirim via WhatsApp.</p>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 mb-8 text-left">
        <h3 class="text-lg font-semibold text-[#333333] mb-4">Nomor Resi</h3>
        <p class="text-2xl font-mono font-bold text-rose-600 mb-4">{{ $orderData['order_number'] ?? '—' }}</p>

        <p class="text-sm text-[#666666] mb-4">
            Total pembayaran: <span class="font-semibold text-[#333333]">{{ format_price($orderData['total'] ?? 0) }}</span>
        </p>

        <div class="bg-[#F8F5F0] rounded-lg p-4 mb-4">
            <p class="text-sm text-[#666666]">Cek WhatsApp admin untuk konfirmasi & pembayaran.</p>
        </div>

        <a href="{{ $orderData['whatsapp_url'] ?? url('') }}"
           target="_blank"
           class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow transition-colors">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.124-.592-1.124-.592s-.837-.39-1.188-.593a2.54 2.54 0 00-1.188.593l-.638.637a2.54 2.54 0 00-.34.857 49.58 49.58 0 005.658 5.566l.348.348a2.54 2.54 0 001.189-.593l.638-.637a2.54 2.54 0 00.34-.857 49.58 49.58 0 005.658-5.566 2.54 2.54 0 00-.593-1.188l-.637-.638a2.54 2.54 0 00-.857-.34m-5.08 5.08c.297.149 1.124.593 1.124.593s.837.39 1.188.593a2.54 2.54 0 011.188-.593l.638-.637a2.54 2.54 0 01.34-.857 49.58 49.58 0 01-5.658-5.566l-.348-.348a2.54 2.54 0 01-1.189.593l-.638.637a2.54 2.54 0 01-.34.857 49.58 49.58 0 01-5.658 5.566 2.54 2.54 0 01-.593 1.188l-.637.638a2.54 2.54 0 01-1.188.593l-.637-.638a2.54 2.54 0 01-.857-.34m-5.08 5.08c.297.149 1.124.593 1.124.593s.837.39 1.188.593a2.54 2.54 0 011.188-.593l.638-.637a2.54 2.54 0 01.34-.857 49.58 49.58 0 01-5.658-5.566l-.348-.348a2.54 2.54 0 01-1.189.593l-.638.637a2.54 2.54 0 01-.34.857 49.58 49.58 0 01-5.658 5.566z"/>
            </svg>
            Chat ke Admin
        </a>
    </div>

    <div class="flex gap-4 justify-center">
        <a href="{{ route('home') }}"
           class="px-6 py-3 text-[#666666] font-medium hover:text-[#333333] transition-colors">
            Kembali ke Beranda
        </a>
        <a href="{{ route('products.index') }}"
           class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg shadow transition-colors">
            Lanjut Belanja
        </a>
    </div>
</div>
@endsection