@php
    $statusTones = [
        'awaiting_payment' => 'warning',
        'pending' => 'warning',
        'confirmed' => 'info',
        'processing' => 'brand',
        'ready' => 'neutral',
        'shipped' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger',
    ];
@endphp

@forelse($invoices as $invoice)
    <tr class="bg-white dark:bg-[#1E1E1E] hover:bg-[#FFFDF0] dark:hover:bg-[#262626] transition-colors">
        <td class="px-4 py-3">
            <span class="font-mono text-sm font-black text-[#0E7490] dark:text-[#22D3EE]">#{{ $invoice->no }}</span>
        </td>
        <td class="px-4 py-3 text-sm">
            <p class="font-extrabold text-[#141414] dark:text-[#FAF6EF]">{{ $invoice->customer_name }}</p>
            <p class="text-xs font-medium text-[#475569] num">{{ $invoice->customer_whatsapp }}</p>
        </td>
        <td class="px-4 py-3 text-sm font-medium text-[#475569] num">{{ $invoice->created_at->format('d M Y, H:i') }}</td>
        <td class="px-4 py-3 text-sm font-bold num">{{ $invoice->items_count }} item</td>
        <td class="px-4 py-3 text-sm text-right font-black num">{{ format_price($invoice->total) }}</td>
        <td class="px-4 py-3">
            @if($invoice->is_paid)
                <x-badge tone="success">Lunas</x-badge>
            @else
                <x-badge tone="warning">Belum bayar</x-badge>
            @endif
        </td>
        <td class="px-4 py-3">
            <x-badge :tone="$statusTones[$invoice->status] ?? 'neutral'">{{ $invoice->status_label }}</x-badge>
        </td>
        <td class="px-4 py-3">
            <div class="flex items-center gap-2">
                <button type="button"
                        data-inv-url="{{ route('admin.orders.invoice', $invoice) }}?embed=1"
                        data-inv-title="Invoice #{{ $invoice->no }}"
                        @click="$dispatch('jd-invoice', {url:$el.dataset.invUrl, title:$el.dataset.invTitle})"
                        class="inline-flex items-center gap-1 min-h-[44px] font-bold text-[#0E7490] dark:text-[#22D3EE] underline underline-offset-2 text-sm">
                    <x-icon name="clipboard" class="w-4 h-4" /> Cetak
                </button>
                <a href="{{ route('admin.orders.show', $invoice->id) }}"
                   class="inline-flex items-center min-h-[44px] text-sm font-bold text-[#475569] underline underline-offset-2">
                    Lihat
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="bg-white dark:bg-[#1E1E1E]">
            <x-empty-state icon="clipboard" :title="request()->filled('search') || (request('status') && request('status') !== 'all') ? 'Tidak ada invoice yang cocok.' : 'Belum ada invoice.'">
                @if(request()->filled('search') || (request('status') && request('status') !== 'all'))
                    Coba ubah kata kunci atau status.
                    <div class="mt-4">
                        <x-btn as="a" :href="route('admin.invoices.index')" variant="ghost">Reset filter</x-btn>
                    </div>
                @else
                    Buat invoice manual untuk pelanggan di luar checkout.
                    <div class="mt-4">
                        <x-btn type="button" icon="plus" @click="open = true">Buat Invoice</x-btn>
                    </div>
                @endif
            </x-empty-state>
        </td>
    </tr>
@endforelse
