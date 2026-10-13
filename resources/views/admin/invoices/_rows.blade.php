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
    <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
        <td class="px-4 py-3">
            <span class="font-mono text-sm text-orange-600 dark:text-orange-400">#{{ $invoice->no }}</span>
        </td>
        <td class="px-4 py-3 text-sm">
            <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $invoice->customer_name }}</p>
            <p class="text-xs text-[#94A3B8] dark:text-[#64748B]">{{ $invoice->customer_whatsapp }}</p>
        </td>
        <td class="px-4 py-3 text-sm text-[#64748B] dark:text-[#cbd5e1]">{{ $invoice->created_at->format('d M Y, H:i') }}</td>
        <td class="px-4 py-3 text-sm text-[#1E293B] dark:text-[#f1f5f9] num">{{ $invoice->items_count }} item</td>
        <td class="px-4 py-3 text-sm text-right font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($invoice->total) }}</td>
        <td class="px-4 py-3">
            @if($invoice->is_paid)
                <x-badge tone="success">LUNAS</x-badge>
            @else
                <x-badge tone="warning">BELUM BAYAR</x-badge>
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
                        class="inline-flex items-center gap-1 text-orange-600 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300 text-sm font-medium transition-colors">
                    <x-icon name="clipboard" class="w-4 h-4" /> Cetak
                </button>
                <a href="{{ route('admin.orders.show', $invoice->id) }}"
                   class="text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 text-sm font-medium transition-colors">
                    Lihat
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8">
            <x-empty-state icon="clipboard" :title="request()->filled('search') || (request('status') && request('status') !== 'all') ? 'Tidak ada invoice yang cocok.' : 'Belum ada invoice.'">
                @if(request()->filled('search') || (request('status') && request('status') !== 'all'))
                    Coba ubah kata kunci atau status.
                    <div class="mt-4">
                        <a href="{{ route('admin.invoices.index') }}"
                           class="inline-flex items-center justify-center px-4 py-2 text-sm bg-[#E2E8F0] dark:bg-[#2e323b] text-[#64748B] dark:text-[#cbd5e1] rounded-lg font-medium transition-colors hover:bg-[#CBD5E1] dark:hover:bg-[#404854]">
                            Reset filter
                        </a>
                    </div>
                @else
                    Buat invoice manual untuk pelanggan di luar checkout.
                    <div class="mt-4">
                        <button type="button" @click="open = true"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                            <x-icon name="plus" class="w-4 h-4" /> Buat Invoice
                        </button>
                    </div>
                @endif
            </x-empty-state>
        </td>
    </tr>
@endforelse