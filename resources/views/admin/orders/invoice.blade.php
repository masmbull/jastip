<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }} | {{ setting('brand_name', 'NITIP DI END') }}</title>
    @vite(['resources/css/app.css'])
    <style>
        :root { --brand: #F97316; --ink: #0F172A; --muted: #64748B; --line: #E2E8F0; }
        .sheet { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .num { font-variant-numeric: tabular-nums; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .sheet { box-shadow: none !important; border: none !important; margin: 0 !important; max-width: none !important; padding: 0 !important; }
            tr, .avoid-break { break-inside: avoid; }
            @page { margin: 14mm; }
        }
    </style>
</head>
<body class="bg-[#EEF2F6] text-[#0F172A] antialiased">

@php
    $brand    = setting('brand_name', 'NITIP DI END');
    $owner    = setting('brand_owner');
    $address  = setting('address');
    $whatsapp = setting('whatsapp');
    $waDigits = preg_replace('/[^0-9]/', '', (string) ($whatsapp ?: $owner));
@endphp

{{-- Toolbar (tidak tercetak). Saat dibuka dalam modal (embed), disembunyikan
     karena tombol cetak sudah ada di modal. --}}
@if(!($embed ?? false))
<div class="no-print sticky top-0 z-10 bg-white/95 backdrop-blur border-b border-[#E2E8F0]">
    <div class="max-w-[860px] mx-auto px-4 py-3 flex items-center justify-between gap-3">
        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#64748B] hover:text-orange-600 transition-colors">
            <x-icon name="arrow-left" class="w-4 h-4" /> Kembali ke pesanan
        </a>
        <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
            <x-icon name="printer" class="w-4 h-4" /> Cetak / Simpan PDF
        </button>
    </div>
</div>
@endif

{{-- Lembar invoice --}}
<div class="sheet max-w-[860px] mx-auto bg-white sm:my-6 md:my-8 p-7 sm:p-10 md:p-12 shadow-sm sm:border sm:border-[#E2E8F0] sm:rounded-lg">

    {{-- Kop: brand kiri, judul kanan --}}
    <header class="flex flex-wrap items-start justify-between gap-6 pb-6 border-b-2 border-[#0F172A]">
        <div class="flex items-start gap-3.5">
            <div class="h-12 w-12 rounded-xl bg-orange-500 flex items-center justify-center text-white font-extrabold text-xl shrink-0">
                {{ strtoupper(substr($brand, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <div class="text-lg font-extrabold leading-tight text-[#0F172A]">{{ $brand }}</div>
                @if($owner)<div class="text-xs text-[#64748B]">{{ $owner }}</div>@endif
                <div class="mt-1.5 text-xs text-[#64748B] leading-relaxed">
                    @if($address)<div>{{ $address }}</div>@endif
                    @if($whatsapp)<div>WhatsApp: {{ $whatsapp }}</div>@endif
                </div>
            </div>
        </div>
        <div class="text-right shrink-0">
            <div class="text-3xl font-black tracking-tight text-[#0F172A] leading-none">INVOICE</div>
            <div class="mt-2 text-sm font-semibold text-[#0F172A] num">#{{ $order->order_number }}</div>
            <div class="mt-0.5 text-xs text-[#64748B] num">{{ $order->created_at->format('d M Y, H:i') }}</div>
            <div class="mt-2.5">
                @if($order->is_paid)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold tracking-wide rounded-full bg-emerald-100 text-emerald-700">
                        <x-icon name="check" class="w-3.5 h-3.5" /> LUNAS
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold tracking-wide rounded-full bg-amber-100 text-amber-800">
                        <x-icon name="clock" class="w-3.5 h-3.5" /> BELUM BAYAR
                    </span>
                @endif
            </div>
        </div>
    </header>

    {{-- Meta: ditagihkan ke + ringkasan pesanan --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 gap-5 py-6">
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-[#64748B] mb-2">Ditagihkan kepada</div>
            <div class="text-sm font-semibold text-[#0F172A]">{{ $order->customer_name }}</div>
            @if($order->customer_whatsapp)<div class="text-sm text-[#64748B] num">WhatsApp: {{ $order->customer_whatsapp }}</div>@endif
            @if($order->customer_address)<div class="text-sm text-[#64748B] mt-0.5">{{ $order->customer_address }}</div>@endif
        </div>
        <div class="rounded-lg border border-[#E2E8F0] bg-[#F8FAFC] p-4 text-sm">
            <div class="text-[11px] font-bold uppercase tracking-wider text-[#64748B] mb-2">Ringkasan</div>
            <div class="flex justify-between py-0.5"><span class="text-[#64748B]">Ekspedisi</span><span class="font-medium text-[#0F172A]">{{ $order->shipping_method ?? '-' }}</span></div>
            @if($order->resi)<div class="flex justify-between py-0.5"><span class="text-[#64748B]">No. resi</span><span class="font-medium text-[#0F172A] num">{{ $order->resi }}</span></div>@endif
            <div class="flex justify-between py-0.5"><span class="text-[#64748B]">Ongkir</span><span class="font-medium text-[#0F172A] num">{{ format_price($order->shipping_cost) }}</span></div>
            <div class="flex justify-between py-0.5"><span class="text-[#64748B]">Jumlah item</span><span class="font-medium text-[#0F172A] num">{{ $order->items->sum('quantity') }} pcs</span></div>
        </div>
    </section>

    {{-- Rincian produk --}}
    <table class="w-full text-sm border-collapse">
        <thead>
            <tr class="bg-[#0F172A] text-white text-left">
                <th class="py-2.5 px-3 font-semibold rounded-tl-md">Produk</th>
                <th class="py-2.5 px-3 font-semibold">Satuan</th>
                <th class="py-2.5 px-3 font-semibold text-center">Jumlah</th>
                <th class="py-2.5 px-3 font-semibold text-right">Harga</th>
                <th class="py-2.5 px-3 font-semibold text-right rounded-tr-md">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->items as $item)
                <tr class="border-b border-[#E2E8F0]">
                    <td class="py-2.5 px-3 text-[#0F172A]">{{ $item->product_name }}</td>
                    <td class="py-2.5 px-3 text-[#64748B]">{{ $item->unit }}</td>
                    <td class="py-2.5 px-3 text-center text-[#0F172A] num">{{ $item->quantity }}</td>
                    <td class="py-2.5 px-3 text-right text-[#0F172A] num">{{ format_price($item->product_price) }}</td>
                    <td class="py-2.5 px-3 text-right font-semibold text-[#0F172A] num">{{ format_price($item->subtotal) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-4 px-3 text-center text-[#64748B]">Tidak ada item.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Ringkasan tagihan --}}
    <div class="flex justify-end mt-5">
        <div class="w-full sm:w-80 text-sm">
            <div class="flex justify-between py-1"><span class="text-[#64748B]">Subtotal</span><span class="text-[#0F172A] num">{{ format_price($order->subtotal) }}</span></div>
            <div class="flex justify-between py-1"><span class="text-[#64748B]">Ongkir</span><span class="text-[#0F172A] num">{{ format_price($order->shipping_cost) }}</span></div>
            @if(($order->fee ?? 0) > 0)
                <div class="flex justify-between py-1"><span class="text-[#64748B]">Biaya layanan</span><span class="text-[#0F172A] num">{{ format_price($order->fee) }}</span></div>
            @endif
            @if(($order->discount ?? 0) > 0)
                <div class="flex justify-between py-1"><span class="text-[#64748B]">Diskon{{ $order->coupon ? ' (' . $order->coupon->code . ')' : '' }}</span><span class="text-emerald-600 num">-{{ format_price($order->discount) }}</span></div>
            @endif
            <div class="mt-2 flex items-center justify-between rounded-lg bg-[#0F172A] text-white px-4 py-3">
                <span class="text-sm font-semibold uppercase tracking-wide">Total Bayar</span>
                <span class="text-lg font-black num">{{ format_price($order->total) }}</span>
            </div>
        </div>
    </div>

    {{-- Pembayaran --}}
    <section class="mt-8 pt-6 border-t border-[#E2E8F0] avoid-break">
        @if($order->is_paid)
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 flex flex-col sm:flex-row items-center gap-5">
                <div class="flex-1">
                    <div class="flex items-center gap-2 text-emerald-800 font-semibold">
                        <x-icon name="check-circle" class="w-5 h-5" /> Pembayaran diterima
                    </div>
                    <p class="text-sm text-emerald-700 mt-1">
                        Terima kasih, pembayaran pesanan ini sudah kami terima
                        @if($order->paid_at) pada {{ $order->paid_at->format('d M Y, H:i') }}@endif.
                    </p>
                </div>
                @if($paidImage)
                    <img src="{{ $paidImage }}" alt="Bukti pembayaran diterima" class="w-52 h-auto rounded-lg border border-emerald-200 bg-white">
                @endif
            </div>
        @else
            <div class="text-[11px] font-bold uppercase tracking-wider text-[#64748B] mb-3">Cara pembayaran</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="rounded-xl border border-[#E2E8F0] p-4 text-center">
                    <div class="text-xs font-bold uppercase tracking-wide text-[#0F172A] mb-3">Bayar via QRIS</div>
                    @if($qrisImage)
                        <img src="{{ $qrisImage }}" alt="Kode QRIS {{ $qrisMerchant }}" class="mx-auto w-48 h-auto rounded-lg border border-[#E2E8F0]">
                        <div class="text-xs text-[#64748B] mt-2.5">{{ $qrisMerchant }}</div>
                    @else
                        <p class="text-xs text-[#94A3B8] italic py-10">QRIS belum diunggah. Atur di Pengaturan, bagian QRIS.</p>
                    @endif
                </div>
                <div class="rounded-xl border border-[#E2E8F0] p-4 text-center">
                    <div class="text-xs font-bold uppercase tracking-wide text-[#0F172A] mb-3">atau Transfer Rekening</div>
                    @if($rekeningImage)
                        <img src="{{ $rekeningImage }}" alt="Informasi rekening pembayaran" class="mx-auto w-48 h-auto rounded-lg border border-[#E2E8F0]">
                    @else
                        <p class="text-xs text-[#94A3B8] italic py-10">Rekening belum diunggah.</p>
                    @endif
                </div>
            </div>
            <p class="text-xs text-[#64748B] mt-3 leading-relaxed">
                Setelah transfer, kirim bukti pembayaran lewat WhatsApp
                @if($whatsapp)<a href="https://wa.me/{{ $waDigits }}" class="font-semibold text-orange-600" target="_blank" rel="noopener noreferrer">{{ $whatsapp }}</a>@else<span class="font-medium">{{ $owner }}</span>@endif
                dengan menyebut nomor invoice <span class="font-semibold text-[#0F172A]">#{{ $order->order_number }}</span>.
            </p>
        @endif
    </section>

    {{-- Tanda terima --}}
    <section class="mt-10 grid grid-cols-2 gap-8 text-center text-xs text-[#64748B] avoid-break">
        <div>
            <div class="h-16"></div>
            <div class="border-t border-[#0F172A] pt-2">Penerima</div>
        </div>
        <div>
            <div class="h-16"></div>
            <div class="border-t border-[#0F172A] pt-2">{{ $brand }}</div>
        </div>
    </section>

    <footer class="mt-8 pt-4 border-t border-[#E2E8F0] text-center text-xs text-[#64748B]">
        Terima kasih telah menitip di {{ $brand }}. Invoice ini dibuat otomatis dan sah tanpa tanda tangan basah.
    </footer>
</div>

@if($autoPrint)
    <script>window.addEventListener('load', () => window.print());</script>
@endif
</body>
</html>