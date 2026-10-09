@forelse($invoices as $invoice)
    <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
        <td class="px-4 py-3">
            <span class="font-mono text-sm text-orange-600 dark:text-orange-400">#{{ $invoice->no }}</span>
        </td>
        <td class="px-4 py-3 text-sm">
            <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $invoice->customer_name }}</p>
            <p class="text-xs text-[#94A3B8] dark:text-[#64748B]">{{ $invoice->customer_whatsapp }}</p>
        </td>
        <td class="px-4 py-3 text-sm text-[#94A3B8] dark:text-[#cbd5e1]">{{ $invoice->created_at->format('d M Y, H:i') }}</td>
        <td class="px-4 py-3 text-sm text-[#1E293B] dark:text-[#f1f5f9]">{{ $invoice->items_count }} item</td>
        <td class="px-4 py-3 text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ format_price($invoice->total) }}</td>
        <td class="px-4 py-3">
            @if($invoice->is_paid)
                <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400">LUNAS</span>
            @else
                <span class="px-2 py-1 text-xs rounded-full bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400">BELUM BAYAR</span>
            @endif
        </td>
        <td class="px-4 py-3">
            <span class="px-2 py-1 text-xs rounded-full {{ $invoice->status_badge_class }}">{{ $invoice->status_label }}</span>
        </td>
        <td class="px-4 py-3">
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.orders.invoice', $invoice) }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1 text-orange-600 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300 text-sm font-medium transition-colors">
                    <x-icon name="clipboard" class="w-4 h-4" /> Cetak
                </a>
                <a href="{{ route('admin.orders.show', $invoice->id) }}"
                   class="text-[#94A3B8] hover:text-orange-600 text-sm font-medium transition-colors">
                    Lihat
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-4 py-12 text-center">
            <div class="w-16 h-16 mx-auto bg-[#F1F5F9] dark:bg-[#2e323b] rounded-full mb-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2Z"></path>
                </svg>
            </div>
            <p class="text-[#94A3B8] font-medium">Belum ada invoice.</p>
            <p class="text-sm text-[#CBD5E1] mt-1">Buat invoice manual untuk pelanggan di luar checkout.</p>
            <button type="button" @click="open = true"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium">
                <x-icon name="plus" class="w-4 h-4" /> Buat Invoice
            </button>
        </td>
    </tr>
@endforelse