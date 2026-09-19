@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
        {{-- Product Image --}}
        <div class="relative">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                 class="w-full rounded-xl shadow-lg object-cover aspect-square">
            @if($product->is_featured)
                <span class="absolute top-4 left-4 bg-rose-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                    Produk Unggulan
                </span>
            @endif
        </div>

        {{-- Product Details --}}
        <div class="space-y-6">
            <div>
                <p class="text-sm text-rose-600 font-medium mb-2">{{ $product->category->name ?? 'Lainnya' }}</p>
                <h1 class="text-3xl font-bold text-[#333333]">{{ $product->name }}</h1>
                <p class="text-2xl font-bold text-rose-600 mt-3">{{ $product->formatted_price }}</p>
                <p class="text-sm text-[#999999] mt-1">* Harga belum termasuk ongkir.</p>

                @php
                    $badge = $product->availability_badge;
                @endphp
                <span class="inline-flex items-center px-3 py-1 text-sm rounded-full {{ $badge['class'] }}">
                    {{ $badge['text'] }}
                </span>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-[#333333] mb-2">Deskripsi</h3>
                <p class="text-[#666666] leading-relaxed">{{ $product->description }}</p>
            </div>

            @if($product->isInStock())
            <div class="pt-4 border-t border-[#E8E0D8]">
                <form id="addToCartForm" method="POST" action="{{ route('cart.add', ['product' => $product->id]) }}">
                    @csrf
                    <div class="flex items-center space-x-4 mb-4">
                        <label class="text-sm font-medium text-[#333333]">Jumlah:</label>
                        <div class="flex items-center space-x-2">
                            <button type="button" onclick="decrementQty()"
                                    class="w-10 h-10 flex items-center justify-center bg-[#F5F5F5] rounded-full text-[#333333] hover:bg-rose-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path>
                                </svg>
                            </button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}"
                                   class="w-12 h-10 text-center text-lg font-medium border border-[#E8E0D8] rounded focus:outline-none focus:ring-2 focus:ring-rose-300">
                            <button type="button" onclick="incrementQty({{ $product->stock }})"
                                    class="w-10 h-10 flex items-center justify-center bg-[#F5F5F5] rounded-full text-[#333333] hover:bg-rose-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m6-6H6"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" id="addToCartBtn"
                            class="w-full px-6 py-4 bg-rose-500 hover:bg-rose-600 text-white font-medium rounded-full shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-rose-300">
                        Tambah ke Titipan
                    </button>
                </form>

                <a href="{{ \App\Services\WhatsappService::contactUrl('Saya ingin nitip ' . $product->name) }}"
                   target="_blank"
                   class="mt-3 flex items-center justify-center w-full px-6 py-3 border-2 border-green-500 text-green-600 font-medium rounded-full hover:bg-green-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.52 3.48A11.94 11.94 0 0012 0a11.96 11.96 0 00-8.44 20.73l-2.53 7.55 7.67-2.08A11.88 11.88 0 0012 24c6.62 0 12-5.38 12-12 0-3.21-1.25-6.21-3.48-8.42z" />
                    </svg>
                    Nitip via WhatsApp
                </a>
            </div>
            @else
            <div class="pt-4">
                <button disabled class="w-full px-6 py-4 bg-gray-300 text-gray-500 font-medium rounded-full cursor-not-allowed">
                    Stok Habis
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- Related Products --}}
    @if($relatedProducts->isNotEmpty())
    <section class="mt-16">
        <h2 class="text-2xl font-bold text-[#333333] mb-6">Produk Serupa</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $related)
                <x-product-card :product="$related" />
            @endforeach
        </div>
    </section>
    @endif
</div>

<script>
function decrementQty() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) { input.value = parseInt(input.value) - 1; }
}
function incrementQty(max) {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) < max) { input.value = parseInt(input.value) + 1; }
}
</script>
@endsection