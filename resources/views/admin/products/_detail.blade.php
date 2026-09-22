<div class="flex items-start gap-3">
    <div class="w-16 h-16 rounded-lg overflow-hidden">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
             class="w-full h-full object-cover" loading="lazy" decoding="async">
    </div>
    <div class="space-y-2 text-sm">
        <div>
            <p class="font-bold text-[#1E293B] text-lg">{{ $product->name }}</p>
            <p class="text-xs text-[#94A3B8] font-mono">{{ $product->slug }}</p>
        </div>
        <p class="font-bold text-orange-600 text-lg">{{ $product->formatted_price }}</p>
        @if($product->setbiaya_fee > 0)
            <p class="text-xs text-[#94A3B8]">Fee: {{ $product->formatted_setbiaya_fee }}</p>
        @endif
        <p class="text-[#94A3B8]">{{ $product->availability_badge['text'] }}</p>
        @if($product->is_featured)
            <p class="text-xs font-bold text-orange-600">★ Unggulan</p>
        @endif
        @if(!$product->is_active)
            <p class="text-xs text-[#94A3B8]">(Non-aktif)</p>
        @endif
        @if($product->category)
            <p class="text-[#64748B]">Kategori: <span class="font-medium">{{ $product->category->name }}</span></p>
        @endif
        @if($product->sku)
            <p class="text-xs text-[#94A3B8]">SKU: {{ $product->sku }}</p>
        @endif
        <p class="text-[#64748B]">{{ $product->description ?: 'Tidak ada deskripsi.' }}</p>
    </div>
</div>
