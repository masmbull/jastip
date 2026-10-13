<div class="flex items-start gap-3">
    <div class="w-16 h-16 rounded-lg overflow-hidden shrink-0">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
             class="w-full h-full object-cover" loading="lazy" decoding="async">
    </div>
    <div class="space-y-2 text-sm">
        <div>
            <p class="font-bold text-[#1E293B] dark:text-[#f1f5f9] text-lg">{{ $product->name }}</p>
            <p class="text-xs text-[#94A3B8] dark:text-[#64748B] font-mono">{{ $product->slug }}</p>
        </div>
        <p class="font-bold text-orange-600 dark:text-orange-400 text-lg num">{{ $product->formatted_price }}</p>
        @if($product->setbiaya_fee > 0)
            <p class="text-xs text-[#94A3B8] dark:text-[#64748B] num">Fee: {{ $product->formatted_setbiaya_fee }}</p>
        @endif
        <p class="text-[#64748B] dark:text-[#cbd5e1]">{{ $product->availability_badge['text'] }}</p>
        @if($product->is_featured)
            <p class="text-xs font-bold text-orange-600 dark:text-orange-400 inline-flex items-center gap-1">
                <x-icon name="star" class="w-4 h-4" /> Unggulan
            </p>
        @endif
        @if(!$product->is_active)
            <p class="text-xs text-[#94A3B8] dark:text-[#64748B]">(Non-aktif)</p>
        @endif
        @if($product->category)
            <p class="text-[#64748B] dark:text-[#cbd5e1]">Kategori: <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $product->category->name }}</span></p>
        @endif
        @if($product->sku)
            <p class="text-xs text-[#94A3B8] dark:text-[#64748B] font-mono">SKU: {{ $product->sku }}</p>
        @endif
        <p class="text-[#64748B] dark:text-[#cbd5e1]">{{ $product->description ?: 'Tidak ada deskripsi.' }}</p>
    </div>
</div>