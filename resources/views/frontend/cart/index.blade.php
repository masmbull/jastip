@extends('layouts.app')

@section('title', 'Titipan Kamu')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <h1 class="text-3xl md:text-4xl font-bold text-[#1E293B] mb-8 animate-fade-up">Titipan Kamu</h1>

    @if($cartItems->isEmpty())
        <div class="text-center py-16 animate-fade-up" style="animation-delay: 120ms">
            <div class="w-32 h-32 mx-auto bg-[#F0EDE7] rounded-full mb-6 flex items-center justify-center">
                <span class="text-5xl">🛍️</span>
            </div>
            <h2 class="text-xl font-semibold text-[#1E293B] mb-2">Titipan kamu masih kosong.</h2>
            <p class="text-[#94A3B8] mb-6">Belum ada produk yang ditambahkan ke titipan.</p>
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                Lihat Semua Produk
            </a>
        </div>
    @else
        <div class="space-y-6">

            {{-- Cart Table (Desktop) --}}
            <div class="hidden sm:block bg-white rounded-xl shadow-sm border border-[#E2E8F0] overflow-hidden animate-fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-[#F1F5F9]">
                            <tr>
                                <th class="text-left px-4 py-3 text-xs font-medium text-[#64748B] uppercase">Produk</th>
                                <th class="text-center px-4 py-3 text-xs font-medium text-[#64748B] uppercase">Harga</th>
                                <th class="text-center px-4 py-3 text-xs font-medium text-[#64748B] uppercase">Jumlah</th>
                                <th class="text-right px-4 py-3 text-xs font-medium text-[#64748B] uppercase">Subtotal</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-[#64748B] uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F0EDE7]">
                            @foreach($cartItems as $item)
                                <tr class="hover:bg-[#F8FAFC]">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center space-x-3">
                                            <img src="{{ $item['image'] ?? 'https://placehold.co/60x60/ffe8f0/999999?text=' . urlencode($item['name']) }}"
                                                 alt="{{ $item['name'] }}"
                                                 class="w-14 h-14 rounded-lg object-cover"
                                                 loading="lazy" decoding="async">
                                            <div>
                                                <p class="font-medium text-[#1E293B]">{{ $item['name'] }}</p>
                                                <p class="text-sm text-[#94A3B8]">{{ $item['unit'] ?? 'pcs' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center px-4 py-4">{{ format_price($item['price']) }}</td>
                                    <td class="text-center px-4 py-4">
                                        <form method="POST" action="{{ route('cart.update', ['product' => $item['product_id']]) }}"
                                              class="inline-flex items-center justify-center gap-1">
                                            @csrf
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                                   class="w-10 h-8 text-center text-sm border border-[#E2E8F0] rounded focus:outline-none focus:ring-1 focus:ring-orange-300">
                                            <button type="submit"
                                                    class="text-xs px-2 py-1 bg-orange-500 text-white rounded hover:bg-orange-600">
                                                OK
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-right px-4 py-4 font-medium text-[#1E293B]">{{ format_price($item['subtotal']) }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <form method="POST"
                                              action="{{ route('cart.remove', ['product' => $item['product_id']]) }}"
                                              data-confirm="Yakin ingin menghapus produk ini dari titipan?"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-500 hover:text-red-700 p-2 rounded hover:bg-red-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142a2 2 0 01-1.994 1.827H7.864a2 2 0 01-1.994-1.827L5 7M10 11V6a1 1 0 011-1h2a1 1 0 011 1v5m-4 0v9h6v-9m-6 0L8 21h8l-2-9h-4z"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mobile list --}}
            <div class="sm:hidden space-y-4">
                @foreach($cartItems as $item)
                    <div class="bg-white rounded-xl shadow-sm border border-[#E2E8F0] p-4 flex gap-3 items-start">
                        <img src="{{ $item['image'] ?? 'https://placehold.co/60x60/ffe8f0/999999?text=' . urlencode($item['name']) }}"
                             alt="{{ $item['name'] }}"
                             class="w-16 h-16 rounded-lg object-cover"
                             loading="lazy" decoding="async">
                        <div class="flex-1">
                            <p class="font-medium text-[#1E293B]">{{ $item['name'] }}</p>
                            <p class="text-xs text-[#94A3B8]">{{ $item['unit'] ?? 'pcs' }} · {{ format_price($item['price']) }}</p>
                            <form method="POST" action="{{ route('cart.update', ['product' => $item['product_id']]) }}"
                                  class="flex items-center gap-1 mt-2">
                                @csrf
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                       class="w-10 h-8 text-center text-sm border border-[#E2E8F0] rounded">
                                <button type="submit" class="text-xs px-2 py-1 bg-orange-500 text-white rounded">OK</button>
                            </form>
                            <p class="text-xs text-[#94A3B8] mt-1">Subtotal: {{ format_price($item['subtotal']) }}</p>
                        </div>
                        <form method="POST"
                              action="{{ route('cart.remove', ['product' => $item['product_id']]) }}"
                              data-confirm="Yakin ingin menghapus produk ini dari titipan?"
                              class="self-start">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            {{-- Cart Summary --}}
            <div class="mt-8 bg-white rounded-xl shadow-sm border border-[#E2E8F0] p-6 animate-fade-up">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-[#64748B]">Subtotal ({{ $cartItems->sum('quantity') }} item)</span>
                        <span class="font-bold text-[#1E293B]">{{ format_price($subtotal) }}</span>
                    </div>
                                                            <div class="flex justify-between items-center">
                        <span class="text-[#64748B]">Ongkir</span>
                        <span class="text-[#94A3B8]">{{ format_price(setting('shipping_cost', 0)) }}</span>
                    </div>
                    @if(($fee ?? 0) > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-[#64748B]">Biaya Fee</span>
                        <span class="text-[#94A3B8]">{{ format_price($fee) }}</span>
                    </div>
                    @endif
                    <div class="border-t border-[#E2E8F0] pt-3 mt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-[#1E293B]">Total</span>
                            <span class="text-2xl font-bold text-orange-600">{{ format_price($subtotal + setting('shipping_cost', 0) + ($fee ?? 0)) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-8 flex flex-col sm:flex-row gap-4 animate-fade-up">
                <a href="{{ route('products.index') }}"
                   class="flex-1 text-center px-6 py-3 border border-[#E2E8F0] text-[#64748B] font-medium rounded-full hover:bg-[#F1F5F9] transition-colors">
                    Lanjutkan Belanja
                </a>
                <a href="{{ route('checkout.index') }}"
                   class="flex-1 text-center px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-full shadow-md hover:shadow-lg transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Lanjut Nitip
                </a>
            </div>
        </div>
    @endif
</div>
@endsection