<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }} | {{ setting('brand_name', 'NITIP DI END') }}</title>
    @vite(['resources/css/app.css'])
    <style>
        .sheet { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .num { font-variant-numeric: tabular-nums; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .sheet { box-shadow: none !important; border: 2px solid #141414 !important; margin: 0 !important; max-width: none !important; }
            tr, .avoid-break { break-inside: avoid; }
            @page { margin: 12mm; }
        }
    </style>
</head>
<body class="bg-[#FAF6EF] text-[#141414] antialiased">

@php
    $brand    = setting('brand_name', 'NITIP DI END');
    $owner    = setting('brand_owner');
    $address  = setting('address');
    $whatsapp = setting('whatsapp');
    $waDigits = preg_replace('/[^0-9]/', '', (string) ($whatsapp ?: $owner));
@endphp

@if(!($embed ?? false))
<div class="no-print sticky top-0 z-10 bg-white border-b-2 border-[#141414]">
    <div class="max-w-[880px] mx-auto px-4 py-3 flex items-center justify-between gap-3">
        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-[#141414] underline underline-offset-4 min-h-[44px]">
            <x-icon name="arrow-left" class="w-4 h-4" /> Kembali ke pesanan
        </a>
        <button type="button" onclick="window.print()"
                class="nb-btn px-4 py-2 bg-[#0E7490] text-white text-sm">
            <x-icon name="printer" class="w-4 h-4" /> Cetak / Simpan PDF
        </button>
    </div>
</div>
@endif

<main class="sheet nb-card max-w-[880px] mx-auto sm:my-6 md:my-8 p-6 sm:p-9 md:p-11 rounded-[14px]!">

    <header class="flex flex-wrap items-start justify-between gap-6 pb-6 border-b-[3px] border-[#141414]">
        <div class="flex items-start gap-4 min-w-0">
            <div class="h-13 w-13 p-3 bg-[#FFC800] border-2 border-[#141414] rounded-[10px] flex items-center justify-center text-[#141414] font-black text-2xl shrink-0" style="box-shadow: 3px 3px 0 #141414; width:52px; height:52px;">
                {{ strtoupper(substr($brand, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <div class="text-xl font-black leading-tight tracking-tight">{{ $brand }}</div>
                @if($owner)<div class="text-xs font-bold text-[#475569] mt-0.5">{{ $owner }}</div>@endif
                <div class="mt-1.5 text-xs text-[#475569] leading-relaxed max-w-[42ch]">
                    @if($address)<div>{{ $address }}</div>@endif
                    @if($whatsapp)<div>WhatsApp: <span class="font-bold text-[#141414] num">{{ $whatsapp }}</span></div>@endif
                </div>
            </div>
        </div>
        <div class="text-left sm:text-right shrink-0">
            <div class="inline-block bg-[#141414] text-white font-black tracking-tight text-2xl sm:text-3xl leading-none px-3 py-2 rounded-[8px]">INVOICE</div>
            <div class="mt-2.5 text-sm font-black num">#{{ $order->order_number }}</div>
            <div class="mt-0.5 text-xs text-[#475569] num">{{ $order->created_at->format('d M Y, H:i') }}</div>
            <div class="mt-2.5">
                @if($order->is_paid)
                    <span class="nb-badge bg-[#DCFCE7] text-[#166534]">
                        <x-icon name="check" class="w-3.5 h-3.5" /> Lunas
                    </span>
                @else
                    <span class="nb-badge bg-[#FEF3C7] text-[#92400E]">
                        <x-icon name="clock" class="w-3.5 h-3.5" /> Belum bayar
                    </span>
                @endif
            </div>
        </div>
    </header>

    <section class="grid grid-cols-1 sm:grid-cols-5 gap-4 py-6" aria-label="Detail penagihan">
        <div class="sm:col-span-3 border-2 border-[#141414] rounded-[10px] p-4 bg-white">
            <div class="text-[11px] font-black uppercase tracking-[0.12em] text-[#475569] mb-2">Ditagihkan kepada</div>
            <div class="text-[15px] font-extrabold leading-snug">{{ $order->customer_name }}</div>
            @if($order->customer_whatsapp)<div class="text-sm text-[#475569] num mt-1">WhatsApp: <span class="font-bold text-[#141414]">{{ $order->customer_whatsapp }}</span></div>@endif
            @if($order->customer_address)<div class="text-sm text-[#475569] mt-1 leading-relaxed">{{ $order->customer_address }}</div>@endif
        </div>
        <div class="sm:col-span-2 border-2 border-[#141414] rounded-[10px] p-4 bg-[#FAF6EF] text-sm">
            <div class="text-[11px] font-black uppercase tracking-[0.12em] text-[#475569] mb-2">Ringkasan</div>
            <dl class="space-y-1.5">
                <div class="flex justify-between gap-3"><dt class="text-[#475569]">Ekspedisi</dt><dd class="font-bold text-right">{{ $order->shipping_method ?? '-' }}</dd></div>
                @if($order->resi)<div class="flex justify-between gap-3"><dt class="text-[#475569]">No. resi</dt><dd class="font-bold text-right num break-all">{{ $order->resi }}</dd></div>@endif
                <div class="flex justify-between gap-3"><dt class="text-[#475569]">Ongkir</dt><dd class="font-bold text-right num">{{ format_price($order->shipping_cost) }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-[#475569]">Jumlah item</dt><dd class="font-bold text-right num">{{ $order->items->sum('quantity') }} pcs</dd></div>
            </dl>
        </div>
    </section>

    <section aria-label="Rincian produk">
        <div class="border-2 border-[#141414] rounded-[10px] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse min-w-[560px]">
                    <thead>
                        <tr class="bg-[#141414] text-white text-left">
                            <th scope="col" class="py-3 px-3 font-extrabold">Produk</th>
                            <th scope="col" class="py-3 px-3 font-extrabold">Satuan</th>
                            <th scope="col" class="py-3 px-3 font-extrabold text-center">Jumlah</th>
                            <th scope="col" class="py-3 px-3 font-extrabold text-right">Harga</th>
                            <th scope="col" class="py-3 px-3 font-extrabold text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-[#141414]">
                        @forelse($order->items as $item)
                            <tr class="bg-white">
                                <td class="py-2.5 px-3 font-bold leading-snug">
                                    {{ $item->product_name }}
                                    @if(($item->status ?? 'ready') === 'po')
                                        <span class="nb-badge bg-[#FEF3C7] text-[#92400E] ml-1.5">PO</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-[#475569]">{{ $item->unit }}</td>
                                <td class="py-2.5 px-3 text-center font-bold num">{{ $item->quantity }}</td>
                                <td class="py-2.5 px-3 text-right num">{{ format_price($item->product_price) }}</td>
                                <td class="py-2.5 px-3 text-right font-extrabold num">{{ format_price($item->subtotal) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-8 px-3 text-center text-[#475569] font-bold">Tidak ada item di pesanan ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="flex justify-end mt-5">
        <div class="w-full sm:w-[340px] text-sm border-2 border-[#141414] rounded-[10px] overflow-hidden bg-white">
            <div class="p-4 space-y-1.5">
                <div class="flex justify-between py-0.5"><span class="text-[#475569]">Subtotal</span><span class="font-bold num">{{ format_price($order->subtotal) }}</span></div>
                <div class="flex justify-between py-0.5"><span class="text-[#475569]">Ongkir</span><span class="font-bold num">{{ format_price($order->shipping_cost) }}</span></div>
                @if(($order->fee ?? 0) > 0)
                    <div class="flex justify-between py-0.5"><span class="text-[#475569]">Fee Jastip</span><span class="font-bold num">{{ format_price($order->fee) }}</span></div>
                @endif
                @if(($order->discount ?? 0) > 0)
                    <div class="flex justify-between py-0.5"><span class="text-[#475569]">Diskon{{ $order->coupon ? ' (' . $order->coupon->code . ')' : '' }}</span><span class="font-bold text-[#166534] num">-{{ format_price($order->discount) }}</span></div>
                @endif
                @if(($order->down_payment ?? 0) > 0)
                    <div class="flex justify-between py-0.5"><span class="text-[#475569]">DP Masuk</span><span class="font-bold text-[#166534] num">-{{ format_price($order->down_payment) }}</span></div>
                @endif
            </div>
            @if(($order->down_payment ?? 0) > 0 && ! $order->is_paid)
                <div class="nb-money-strip px-4 py-3.5 space-y-1">
                    <div class="flex items-center justify-between text-[13px]">
                        <span class="font-bold">Total</span>
                        <span class="font-bold num">{{ format_price($order->total) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-black uppercase tracking-wide">Sisa Bayar</span>
                        <span class="text-xl font-black num">{{ format_price($order->remaining) }}</span>
                    </div>
                </div>
            @else
                <div class="nb-money-strip flex items-center justify-between px-4 py-3.5">
                    <span class="text-sm font-black uppercase tracking-wide">Total bayar</span>
                    <span class="text-xl font-black num">{{ format_price($order->total) }}</span>
                </div>
            @endif
        </div>
    </div>

    <section class="mt-8 avoid-break" aria-label="Pembayaran">
        @if($order->is_paid)
            <div class="rounded-[10px] border-2 border-[#141414] bg-[#DCFCE7] p-5 flex flex-col sm:flex-row items-start sm:items-center gap-5">
                <div class="flex-1">
                    <div class="flex items-center gap-2 text-[#166534] font-black">
                        <x-icon name="check-circle" class="w-5 h-5" /> Pembayaran diterima
                    </div>
                    <p class="text-sm text-[#14532D] mt-1.5 leading-relaxed">
                        Terima kasih, pembayaran pesanan ini sudah kami terima
                        @if($order->paid_at) pada <span class="font-bold num">{{ $order->paid_at->format('d M Y, H:i') }}</span>@endif.
                    </p>
                </div>
                @if($paidImage)
                    <img src="{{ $paidImage }}" alt="Bukti pembayaran diterima" class="w-52 h-auto rounded-[8px] border-2 border-[#141414] bg-white">
                @endif
            </div>
        @else
            <h2 class="text-[11px] font-black uppercase tracking-[0.12em] text-[#475569] mb-3">Cara pembayaran</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="rounded-[10px] border-2 border-[#141414] bg-white p-4 text-center">
                    <div class="inline-block text-xs font-black uppercase tracking-wide bg-[#141414] text-white px-2.5 py-1 rounded-md mb-3">Bayar via QRIS</div>
                    @if($qrisImage)
                        <img src="{{ $qrisImage }}" alt="Kode QRIS {{ $qrisMerchant }}" class="mx-auto w-48 h-auto rounded-[8px] border-2 border-[#141414]">
                        <div class="text-xs font-bold text-[#475569] mt-2.5">{{ $qrisMerchant }}</div>
                    @else
                        <p class="text-xs text-[#475569] font-bold py-8 leading-relaxed">QRIS belum diunggah.<br>Atur di Pengaturan, bagian QRIS.</p>
                    @endif
                </div>
                <div class="rounded-[10px] border-2 border-[#141414] bg-white p-4 text-center">
                    <div class="inline-block text-xs font-black uppercase tracking-wide bg-white px-2.5 py-1 rounded-md mb-3 border-2 border-[#141414]">atau Transfer Rekening</div>
                    @if($rekeningImage)
                        <img src="{{ $rekeningImage }}" alt="Informasi rekening pembayaran" class="mx-auto w-48 h-auto rounded-[8px] border-2 border-[#141414]">
                    @else
                        <p class="text-xs text-[#475569] font-bold py-8 leading-relaxed">Rekening belum diunggah.<br>Atur di Pengaturan, bagian Rekening.</p>
                    @endif
                </div>
            </div>
            <p class="text-xs text-[#475569] mt-3 leading-relaxed border-l-4 border-[#FFC800] pl-3">
                Setelah transfer, kirim bukti pembayaran lewat WhatsApp
                @if($whatsapp)<a href="https://wa.me/{{ $waDigits }}" class="font-bold text-[#0E7490] underline underline-offset-2" target="_blank" rel="noopener noreferrer">{{ $whatsapp }}</a>@else<span class="font-bold">{{ $owner }}</span>@endif
                dengan menyebut nomor invoice <span class="font-bold text-[#141414] num">#{{ $order->order_number }}</span>.
            </p>
        @endif
    </section>

    <section class="mt-10 grid grid-cols-2 gap-8 text-center text-xs text-[#475569] avoid-break" aria-label="Tanda terima">
        <div>
            <div class="h-16"></div>
            <div class="border-t-[2.5px] border-[#141414] pt-2 font-bold text-[#141414]">Penerima</div>
        </div>
        <div>
            <div class="h-16"></div>
            <div class="border-t-[2.5px] border-[#141414] pt-2 font-bold text-[#141414]">{{ $brand }}</div>
        </div>
    </section>

    <footer class="mt-8 pt-4 border-t-2 border-[#141414] text-center text-xs text-[#475569] leading-relaxed">
        Terima kasih telah menitip di {{ $brand }}. Invoice ini dibuat otomatis dan sah tanpa tanda tangan basah.
    </footer>
</main>

@if($autoPrint)
    <script>window.addEventListener('load', () => window.print());</script>
@endif
</body>
</html>
