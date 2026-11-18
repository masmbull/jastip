@extends('layouts.app')

@section('title', 'Titipan Kamu')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

    <h1 class="font-display text-2xl md:text-3xl lg:text-4xl font-extrabold text-[#141414] dark:text-[#FAF6EF] mb-6 md:mb-8 animate-fade-up">
        Titipan Kamu
    </h1>

    @if($cartItems->isEmpty())
        <x-empty-state icon="bag" title="Titipan kamu masih kosong." class="animate-fade-up nb-card">
            Belum ada produk di titipan. Pilih dulu dari katalog.
            <div class="mt-4">
                <x-btn as="a" :href="route('products.index')" icon="bag" class="px-6 py-3">Lihat Katalog</x-btn>
            </div>
        </x-empty-state>
    @else
        <div class="space-y-4 md:space-y-6">

            <div class="hidden sm:block nb-table-shell animate-fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <caption class="sr-only">Daftar produk dalam titipan</caption>
                        <thead class="nb-table-head">
                            <tr>
                                <th scope="col" class="text-left px-3 md:px-4 py-3 text-xs font-black uppercase tracking-wide">Produk</th>
                                <th scope="col" class="text-right px-3 md:px-4 py-3 text-xs font-black uppercase tracking-wide">Harga</th>
                                <th scope="col" class="text-center px-3 md:px-4 py-3 text-xs font-black uppercase tracking-wide">Jumlah</th>
                                <th scope="col" class="text-right px-3 md:px-4 py-3 text-xs font-black uppercase tracking-wide">Subtotal</th>
                                <th scope="col" class="px-3 md:px-4 py-3 text-center text-xs font-black uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-[#141414]/10 dark:divide-[#FAF6EF]/15">
                            @foreach($cartItems as $item)
                                <tr class="hover:bg-[#FFF7D6] dark:hover:bg-[#262626] transition-colors">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $item['image'] ?? asset('images/icons/favicon-32.png') }}"
                                                 alt="{{ $item['name'] }}"
                                                 class="w-14 h-14 rounded-[8px] object-cover bg-[#FAF6EF] dark:bg-[#262626] border-2 border-[#141414] dark:border-[#FAF6EF]"
                                                 loading="lazy" decoding="async">
                                            <div class="min-w-0">
                                                <p class="font-extrabold text-[#141414] dark:text-[#FAF6EF]">{{ $item['name'] }}</p>
                                                <p class="text-sm font-medium text-[#475569] dark:text-[#A8A29E]">{{ $item['unit'] ?? 'pcs' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right px-4 py-4 font-bold text-[#141414] dark:text-[#FAF6EF] num">{{ format_price($item['price']) }}</td>
                                    <td class="text-center px-4 py-4">
                                        <form method="POST" action="{{ route('cart.update', ['product' => $item['product_id']]) }}"
                                              data-async
                                              class="inline-flex items-center justify-center gap-1">
                                            @csrf
                                            <label class="sr-only" for="qty-{{ $item['product_id'] }}">Jumlah {{ $item['name'] }}</label>
                                            <input type="number" id="qty-{{ $item['product_id'] }}" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                                   class="nb-input w-14 h-10 text-center text-sm num px-1">
                                            <button type="submit"
                                                    class="nb-btn px-3 py-2 text-xs bg-[#FFC800] text-[#141414]">
                                                OK
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-right px-4 py-4 font-extrabold text-[#141414] dark:text-[#FAF6EF] num">{{ format_price($item['subtotal']) }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <form method="POST"
                                              action="{{ route('cart.remove', ['product' => $item['product_id']]) }}"
                                              data-confirm="Yakin ingin menghapus produk ini dari titipan?"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="nb-btn w-11 h-11 p-0! bg-white dark:bg-[#1E1E1E] text-[#991B1B] dark:text-[#FECACA]"
                                                    aria-label="Hapus {{ $item['name'] }} dari titipan" title="Hapus">
                                                <x-icon name="trash" class="w-5 h-5" stroke="2" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="sm:hidden space-y-4">
                @foreach($cartItems as $item)
                    <article class="nb-card p-4 animate-fade-up">
                        <div class="flex gap-3 items-start">
                            <img src="{{ $item['image'] ?? asset('images/icons/favicon-32.png') }}"
                                 alt="{{ $item['name'] }}"
                                 class="w-16 h-16 rounded-[8px] object-cover bg-[#FAF6EF] dark:bg-[#262626] border-2 border-[#141414] dark:border-[#FAF6EF]"
                                 loading="lazy" decoding="async">
                            <div class="flex-1 min-w-0">
                                <p class="font-extrabold text-[#141414] dark:text-[#FAF6EF]">{{ $item['name'] }}</p>
                                <p class="text-xs font-medium text-[#475569] dark:text-[#A8A29E]">{{ $item['unit'] ?? 'pcs' }} · <span class="num">{{ format_price($item['price']) }}</span></p>
                                <form method="POST" action="{{ route('cart.update', ['product' => $item['product_id']]) }}"
                                      data-async
                                      class="flex items-center gap-1 mt-2">
                                    @csrf
                                    <label class="sr-only" for="qty-m-{{ $item['product_id'] }}">Jumlah {{ $item['name'] }}</label>
                                    <input type="number" id="qty-m-{{ $item['product_id'] }}" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                           class="nb-input w-14 h-10 text-center text-sm num px-1">
                                    <button type="submit" class="nb-btn px-3 py-2 text-xs bg-[#FFC800] text-[#141414]">OK</button>
                                </form>
                                <p class="text-xs font-bold text-[#475569] dark:text-[#A8A29E] mt-2">Subtotal: <span class="num text-[#141414] dark:text-[#FAF6EF]">{{ format_price($item['subtotal']) }}</span></p>
                            </div>
                            <form method="POST"
                                  action="{{ route('cart.remove', ['product' => $item['product_id']]) }}"
                                  data-confirm="Yakin ingin menghapus produk ini dari titipan?"
                                  class="self-start">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="nb-btn w-11 h-11 p-0! bg-white dark:bg-[#1E1E1E] text-[#991B1B]"
                                        aria-label="Hapus {{ $item['name'] }} dari titipan" title="Hapus">
                                    <x-icon name="x" class="w-5 h-5" stroke="2" />
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="nb-card overflow-hidden animate-fade-up">
                <div class="p-5 sm:p-6 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-[#475569] dark:text-[#A8A29E]">Subtotal (<span class="num">{{ $cartItems->sum('quantity') }}</span> item)</span>
                        <span class="font-extrabold text-[#141414] dark:text-[#FAF6EF] num">{{ format_price($subtotal) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-[#475569] dark:text-[#A8A29E]">Ongkir</span>
                        <span class="font-bold text-[#475569] dark:text-[#A8A29E] num">{{ format_price(setting('shipping_cost', 0)) }}</span>
                    </div>
                    @if(($fee ?? 0) > 0)
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-[#475569] dark:text-[#A8A29E]">Biaya Fee</span>
                        <span class="font-bold text-[#475569] dark:text-[#A8A29E] num">{{ format_price($fee) }}</span>
                    </div>
                    @endif
                </div>
                <div class="nb-money-strip px-5 sm:px-6 py-4 flex justify-between items-center">
                    <span class="text-lg font-extrabold">Total</span>
                    <span class="text-2xl font-black num">{{ format_price($subtotal + setting('shipping_cost', 0) + ($fee ?? 0)) }}</span>
                </div>
            </div>

            <div class="mt-2 flex flex-col sm:flex-row gap-3 animate-fade-up">
                <x-btn as="a" :href="route('products.index')" variant="ghost" class="flex-1 py-3">
                    Lanjut Belanja
                </x-btn>
                @auth
                    <x-btn as="a" :href="route('checkout.index')" variant="brand" icon="bag" class="flex-1 py-3">
                        Lanjut Nitip
                    </x-btn>
                @else
                    <x-btn as="a" :href="route('login')" variant="brand" icon="lock" class="flex-1 py-3">
                        Masuk untuk Lanjut Nitip
                    </x-btn>
                @endauth
            </div>
        </div>
    @endif
</div>
@endsection
