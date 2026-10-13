@extends('layouts.app')

@section('title', 'Titipan Kamu')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

    <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-6 md:mb-8 animate-fade-up">Titipan Kamu</h1>

    @if($cartItems->isEmpty())
        <x-empty-state icon="bag" title="Titipan kamu masih kosong." class="animate-fade-up">
            Belum ada produk yang ditambahkan ke titipan.
            <div class="mt-4">
                <x-btn as="a" :href="route('products.index')" class="rounded-full px-6 py-3">Lihat Semua Produk</x-btn>
            </div>
        </x-empty-state>
    @else
        <div class="space-y-4 md:space-y-6">

            {{-- Cart Table (Desktop) --}}
            <x-card padded="false" class="hidden sm:block animate-fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <caption class="sr-only">Daftar produk dalam titipan</caption>
                        <thead class="bg-[#F1F5F9] dark:bg-[#2e323b]">
                            <tr>
                                <th scope="col" class="text-left px-3 md:px-4 py-3 text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase">Produk</th>
                                <th scope="col" class="text-right px-3 md:px-4 py-3 text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase">Harga</th>
                                <th scope="col" class="text-center px-3 md:px-4 py-3 text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase">Jumlah</th>
                                <th scope="col" class="text-right px-3 md:px-4 py-3 text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase">Subtotal</th>
                                <th scope="col" class="px-3 md:px-4 py-3 text-center text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                            @foreach($cartItems as $item)
                                <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#2e323b] transition-colors">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center space-x-3">
                                            <img src="{{ $item['image'] ?? 'https://placehold.co/60x60/ffe8f0/999999?text=' . urlencode($item['name']) }}"
                                                 alt="{{ $item['name'] }}"
                                                 class="w-14 h-14 rounded-lg object-cover bg-[#F1F5F9] dark:bg-[#2e323b]"
                                                 loading="lazy" decoding="async">
                                            <div class="min-w-0">
                                                <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $item['name'] }}</p>
                                                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">{{ $item['unit'] ?? 'pcs' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right px-4 py-4 text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($item['price']) }}</td>
                                    <td class="text-center px-4 py-4">
                                        <form method="POST" action="{{ route('cart.update', ['product' => $item['product_id']]) }}"
                                              data-async
                                              class="inline-flex items-center justify-center gap-1">
                                            @csrf
                                            <label class="sr-only" for="qty-{{ $item['product_id'] }}">Jumlah {{ $item['name'] }}</label>
                                            <input type="number" id="qty-{{ $item['product_id'] }}" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                                   class="w-14 h-10 text-center text-sm num border border-[#E2E8F0] dark:border-[#404854] rounded bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300">
                                            <button type="submit"
                                                    class="text-xs px-3 py-2 min-h-[44px] bg-orange-500 text-white rounded-lg hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-300">
                                                OK
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-right px-4 py-4 font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($item['subtotal']) }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <form method="POST"
                                              action="{{ route('cart.remove', ['product' => $item['product_id']]) }}"
                                              data-confirm="Yakin ingin menghapus produk ini dari titipan?"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-500 hover:text-red-700 p-2.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors focus:outline-none focus:ring-2 focus:ring-red-300"
                                                    aria-label="Hapus {{ $item['name'] }} dari titipan" title="Hapus">
                                                <x-icon name="trash" class="w-5 h-5" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            {{-- Mobile list --}}
            <div class="sm:hidden space-y-4">
                @foreach($cartItems as $item)
                    <x-card class="animate-fade-up">
                        <div class="flex gap-3 items-start">
                            <img src="{{ $item['image'] ?? 'https://placehold.co/60x60/ffe8f0/999999?text=' . urlencode($item['name']) }}"
                                 alt="{{ $item['name'] }}"
                                 class="w-16 h-16 rounded-lg object-cover bg-[#F1F5F9] dark:bg-[#2e323b]"
                                 loading="lazy" decoding="async">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $item['name'] }}</p>
                                <p class="text-xs text-[#64748B] dark:text-[#cbd5e1]">{{ $item['unit'] ?? 'pcs' }} · <span class="num">{{ format_price($item['price']) }}</span></p>
                                <form method="POST" action="{{ route('cart.update', ['product' => $item['product_id']]) }}"
                                      data-async
                                      class="flex items-center gap-1 mt-2">
                                    @csrf
                                    <label class="sr-only" for="qty-m-{{ $item['product_id'] }}">Jumlah {{ $item['name'] }}</label>
                                    <input type="number" id="qty-m-{{ $item['product_id'] }}" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                           class="w-14 h-10 text-center text-sm num border border-[#E2E8F0] dark:border-[#404854] rounded bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300">
                                    <button type="submit" class="text-xs px-3 py-2 min-h-[44px] bg-orange-500 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">OK</button>
                                </form>
                                <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Subtotal: <span class="num">{{ format_price($item['subtotal']) }}</span></p>
                            </div>
                            <form method="POST"
                                  action="{{ route('cart.remove', ['product' => $item['product_id']]) }}"
                                  data-confirm="Yakin ingin menghapus produk ini dari titipan?"
                                  class="self-start">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 p-2.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 focus:outline-none focus:ring-2 focus:ring-red-300"
                                        aria-label="Hapus {{ $item['name'] }} dari titipan" title="Hapus">
                                    <x-icon name="x" class="w-5 h-5" />
                                </button>
                            </form>
                        </div>
                    </x-card>
                @endforeach
            </div>

            {{-- Cart Summary --}}
            <x-card class="mt-8 animate-fade-up">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-[#64748B] dark:text-[#cbd5e1]">Subtotal (<span class="num">{{ $cartItems->sum('quantity') }}</span> item)</span>
                        <span class="font-bold text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($subtotal) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[#64748B] dark:text-[#cbd5e1]">Ongkir</span>
                        <span class="text-[#64748B] dark:text-[#cbd5e1] num">{{ format_price(setting('shipping_cost', 0)) }}</span>
                    </div>
                    @if(($fee ?? 0) > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-[#64748B] dark:text-[#cbd5e1]">Biaya Fee</span>
                        <span class="text-[#64748B] dark:text-[#cbd5e1] num">{{ format_price($fee) }}</span>
                    </div>
                    @endif
                    <div class="border-t border-[#E2E8F0] dark:border-[#404854] pt-3 mt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Total</span>
                            <span class="text-2xl font-bold text-orange-600 dark:text-orange-400 num">{{ format_price($subtotal + setting('shipping_cost', 0) + ($fee ?? 0)) }}</span>
                        </div>
                    </div>
                </div>
            </x-card>

            {{-- Actions --}}
            <div class="mt-8 flex flex-col sm:flex-row gap-4 animate-fade-up">
                <x-btn as="a" :href="route('products.index')" variant="outline" class="flex-1 py-3 rounded-full">
                    Lanjutkan Belanja
                </x-btn>
                @auth
                    <x-btn as="a" :href="route('checkout.index')" variant="brand" class="flex-1 py-3 rounded-full">
                        Lanjut Nitip
                    </x-btn>
                @else
                    <x-btn as="a" :href="route('login')" variant="brand" icon="lock" class="flex-1 py-3 rounded-full">
                        Masuk untuk Lanjut Nitip
                    </x-btn>
                @endauth
            </div>
        </div>
    @endif
</div>
@endsection