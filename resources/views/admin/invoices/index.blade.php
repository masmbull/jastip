@extends('layouts.admin')
@section('title', 'Rekapan Pesanan | ' . setting('brand_name'))

@section('content')
@php
    $activeStatus = request('status', 'all');
    $activePay = request('pay_status', 'all');
    $payTones = ['belum' => 'neutral', 'dp' => 'warning', 'lunas' => 'success'];
    $payLabels = ['belum' => 'Belum Bayar', 'dp' => 'Deposit', 'lunas' => 'Lunas'];
@endphp
<div class="space-y-5" x-data="{ open: false, sampleOpen: false, sampleLoaded: false, sampleError: false }"
     @close-manual-invoice.window="open = false"
     @keydown.escape.window="open = false; sampleOpen = false">
    <x-page-header title="Rekapan Pesanan" subtitle="Ledger harian: Deposit, pelunasan, dan sisa dihitung otomatis dari data asli.">
        <x-btn as="a" :href="route('admin.orders.index')" variant="ghost">Semua Pesanan</x-btn>
        <x-btn type="button" variant="outline" icon="printer"
               @click="sampleOpen = true; sampleLoaded = false; sampleError = false">Pratinjau Sampel</x-btn>
        <x-btn type="button" variant="outline" icon="plus"
               @click="$dispatch('set-invoice-mode', 'lunas'); open = true">Buat Invoice Lunas</x-btn>
        <x-btn type="button" icon="plus" @click="$dispatch('set-invoice-mode', 'deposit'); open = true">Buat Invoice Deposit</x-btn>
    </x-page-header>

    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 nb-rainbow">
        <div class="nb-card p-3.5">
            <p class="text-[11px] font-black uppercase tracking-wider text-[#334155]">Customer</p>
            <p class="text-xl font-black num mt-0.5">{{ number_format($summary['customers'], 0, ',', '.') }}</p>
        </div>
        <div class="nb-card p-3.5">
            <p class="text-[11px] font-black uppercase tracking-wider text-[#334155]">Pesanan</p>
            <p class="text-xl font-black num mt-0.5">{{ number_format($summary['orders'], 0, ',', '.') }}</p>
        </div>
        <div class="nb-card p-3.5">
            <p class="text-[11px] font-black uppercase tracking-wider text-[#334155]">Qty</p>
            <p class="text-xl font-black num mt-0.5">{{ number_format($summary['qty'], 0, ',', '.') }}</p>
        </div>
        <div class="nb-card p-3.5">
            <p class="text-[11px] font-black uppercase tracking-wider text-[#334155]">Grand Total</p>
            <p class="text-[15px] font-black num mt-1">{{ format_price($summary['grand']) }}</p>
        </div>
        <div class="nb-card p-3.5">
            <p class="text-[11px] font-black uppercase tracking-wider text-[#334155]">Total Deposit</p>
            <p class="text-[15px] font-black num mt-1">{{ format_price($summary['dp']) }}</p>
        </div>
        <div class="nb-card p-3.5">
            <p class="text-[11px] font-black uppercase tracking-wider text-[#334155]">Masuk</p>
            <p class="text-[15px] font-black text-[#166534] num mt-1">{{ format_price($summary['paid']) }}</p>
        </div>
        <div class="nb-card p-3.5 bg-[#FFC800]!">
            <p class="text-[11px] font-black uppercase tracking-wider text-[#141414]">Sisa</p>
            <p class="text-[15px] font-black text-[#141414] num mt-1">{{ format_price($summary['remaining']) }}</p>
        </div>
    </div>

    <x-card>
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="grid grid-cols-2 lg:grid-cols-12 gap-2.5" role="search">
            <div class="relative col-span-2 lg:col-span-4">
                <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#334155] pointer-events-none" />
                <label class="sr-only" for="inv-search">Cari rekapan</label>
                <input id="inv-search" type="search" name="search" value="{{ request('search') }}"
                       placeholder="Cari no. pesanan, nama, atau WA"
                       class="nb-input w-full pl-9 pr-3 py-2 text-sm">
            </div>
            <div class="col-span-1 lg:col-span-2">
                <label class="sr-only" for="inv-status">Filter status pesanan</label>
                <select id="inv-status" name="status" class="nb-input w-full px-3 py-2 text-sm font-bold">
                    <option value="all">Semua status</option>
                    @foreach(\App\Models\Order::statuses() as $value => $label)
                        <option value="{{ $value }}" @selected($activeStatus === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1 lg:col-span-2">
                <label class="sr-only" for="inv-pay">Filter status bayar</label>
                <select id="inv-pay" name="pay_status" class="nb-input w-full px-3 py-2 text-sm font-bold">
                    <option value="all">Semua bayar</option>
                    <option value="belum" @selected($activePay === 'belum')>Belum Bayar</option>
                    <option value="dp" @selected($activePay === 'dp')>Deposit</option>
                    <option value="lunas" @selected($activePay === 'lunas')>Lunas</option>
                </select>
            </div>
            <div class="col-span-1 lg:col-span-2">
                <label class="sr-only" for="inv-from">Dari tanggal</label>
                <input id="inv-from" type="date" name="date_from" value="{{ request('date_from') }}" class="nb-input w-full px-3 py-2 text-sm num">
            </div>
            <div class="col-span-1 lg:col-span-2">
                <label class="sr-only" for="inv-to">Sampai tanggal</label>
                <input id="inv-to" type="date" name="date_to" value="{{ request('date_to') }}" class="nb-input w-full px-3 py-2 text-sm num">
            </div>
            <div class="col-span-2 lg:col-span-12 flex flex-wrap gap-2">
                <button type="submit" class="nb-btn px-4 py-2 text-sm bg-[#0E7490] text-white">Terapkan</button>
                @if(request()->filled('search') || $activeStatus !== 'all' || $activePay !== 'all' || request()->filled('date_from') || request()->filled('date_to'))
                    <a href="{{ route('admin.invoices.index') }}" class="nb-btn px-4 py-2 text-sm bg-white dark:bg-[#1E1E1E]">Reset</a>
                @endif
                <a href="{{ route('admin.invoices.export', request()->query()) }}" class="nb-btn px-4 py-2 text-sm bg-white dark:bg-[#1E1E1E] ml-auto">
                    <x-icon name="download" class="w-4 h-4" /> Export CSV
                </a>
            </div>
        </form>
    </x-card>

    <div class="nb-table-shell">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1020px]">
                <thead>
                    <tr class="nb-table-head text-left text-xs uppercase">
                        <th scope="col" class="px-4 py-3 font-black">No. Pesanan</th>
                        <th scope="col" class="px-4 py-3 font-black">Tanggal</th>
                        <th scope="col" class="px-4 py-3 font-black">Customer</th>
                        <th scope="col" class="px-4 py-3 font-black">Barang</th>
                        <th scope="col" class="px-4 py-3 text-right font-black">Deposit</th>
                        <th scope="col" class="px-4 py-3 text-right font-black">Grand Total</th>
                        <th scope="col" class="px-4 py-3 text-right font-black">Sisa</th>
                        <th scope="col" class="px-4 py-3 font-black">Bayar</th>
                        <th scope="col" class="px-4 py-3 font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#141414]/15 dark:divide-[#FAF6EF]/15">
                    @forelse($invoices as $invoice)
                        @php
                            $waPhone = wa_phone($invoice->customer_whatsapp);
                            $paid = (int) $invoice->down_payment + (int) $invoice->payments_sum;
                            $left = max(0, (int) $invoice->total - $paid);
                            $ps = $invoice->paid_at ? 'lunas' : ($paid > 0 ? 'dp' : 'belum');
                            $waLink = whatsapp_url($invoice->customer_whatsapp, 'Halo kak '.$invoice->customer_name.', untuk pesanan #'.$invoice->no.' ('.format_price($invoice->total).'), sisa pembayaran '.format_price($left).'. Terima kasih.');
                        @endphp
                        <tr class="bg-white dark:bg-[#1E1E1E] hover:bg-[#FFFDF0] dark:hover:bg-[#262626] transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm font-black text-[#0E7490] dark:text-[#22D3EE]">#{{ $invoice->no }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs font-bold text-[#334155] num whitespace-nowrap">{{ $invoice->created_at->format('d M Y') }}<br>{{ $invoice->created_at->format('H:i') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <p class="font-extrabold">{{ $invoice->customer_name }}</p>
                                <p class="text-xs font-medium text-[#334155] num">{{ $invoice->customer_whatsapp }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs font-medium max-w-[220px]">
                                @foreach($invoice->items->take(2) as $it)
                                    <span class="block truncate">{{ $it->product_name }} <span class="num">x{{ $it->quantity }}</span></span>
                                @endforeach
                                @if($invoice->items->count() > 2)
                                    <span class="font-bold text-[#334155]">+{{ $invoice->items->count() - 2 }} lagi</span>
                                @endif
                                <span class="block font-bold text-[#334155] num mt-0.5">{{ $invoice->items_qty }} pcs</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-bold num">{{ $invoice->down_payment > 0 ? format_price($invoice->down_payment) : '-' }}</td>
                            <td class="px-4 py-3 text-sm text-right font-black num">{{ format_price($invoice->total) }}</td>
                            <td class="px-4 py-3 text-sm text-right font-black num {{ $left > 0 ? 'text-[#92400E]' : 'text-[#166534]' }}">{{ format_price($left) }}</td>
                            <td class="px-4 py-3">
                                <x-badge :tone="$payTones[$ps]">{{ $payLabels[$ps] }}</x-badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    @if($waPhone)
                                        <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center justify-center w-11 h-11 border-2 border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF] rounded-[8px] text-[#15803D]"
                                           title="Kirim teks lewat WhatsApp" aria-label="WhatsApp {{ $invoice->customer_name }}">
                                            <x-icon name="chat" class="w-5 h-5" />
                                        </a>
                                        <button type="button"
                                                data-state="idle"
                                                @click="jdShareInvoice($el, {
                                                    url: '{{ route('admin.orders.invoice-image', $invoice) }}',
                                                    filename: 'invoice-{{ $invoice->no }}.png',
                                                    text: 'Invoice #{{ $invoice->no }} - {{ $invoice->customer_name }}',
                                                    waUrl: '{{ $waLink }}'
                                                })"
                                                class="inline-flex items-center justify-center w-11 h-11 border-2 border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF] rounded-[8px] text-[#15803D] disabled:opacity-60"
                                                title="Kirim gambar invoice lewat WhatsApp" aria-label="Kirim gambar invoice {{ $invoice->no }}">
                                            <x-icon name="photo" class="w-5 h-5" />
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.orders.edit', $invoice->id) }}"
                                       class="inline-flex items-center justify-center w-11 h-11 border-2 border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF] rounded-[8px] text-[#B45309]"
                                       title="Edit invoice" aria-label="Edit invoice {{ $invoice->no }}">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </a>
                                    <button type="button"
                                            data-inv-url="{{ route('admin.orders.invoice', $invoice) }}?embed=1"
                                            data-inv-title="Invoice #{{ $invoice->no }}"
                                            @click="$dispatch('jd-invoice', {url:$el.dataset.invUrl, title:$el.dataset.invTitle})"
                                            class="inline-flex items-center justify-center w-11 h-11 border-2 border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF] rounded-[8px] text-[#0E7490]"
                                            title="Cetak" aria-label="Cetak invoice {{ $invoice->no }}">
                                        <x-icon name="clipboard" class="w-4 h-4" />
                                    </button>
                                    <a href="{{ route('admin.orders.show', $invoice->id) }}#pelunasan"
                                       class="inline-flex items-center min-h-[44px] px-1 text-sm font-bold underline underline-offset-2">
                                        Bayar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="bg-white dark:bg-[#1E1E1E]">
                                <x-empty-state icon="clipboard" :title="request()->filled('search') || $activeStatus !== 'all' || $activePay !== 'all' ? 'Tidak ada pesanan yang cocok.' : 'Belum ada pesanan.'">
                                    @if(request()->filled('search') || $activeStatus !== 'all' || $activePay !== 'all')
                                        Coba ubah kata kunci atau filter.
                                        <div class="mt-4">
                                            <x-btn as="a" :href="route('admin.invoices.index')" variant="ghost">Reset filter</x-btn>
                                        </div>
                                    @else
                                        Buat invoice manual untuk mencatat pesanan harian.
                                        <div class="mt-4">
                                            <x-btn type="button" icon="plus" @click="open = true">Buat Invoice</x-btn>
                                        </div>
                                    @endif
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($invoices, 'hasPages') && $invoices->hasPages())
        <div>{{ $invoices->links() }}</div>
    @endif

    @include('admin.invoices._modal')
    @include('admin.invoices._sample-modal')
</div>
@endsection

@include('admin.orders._manual-form-script')
