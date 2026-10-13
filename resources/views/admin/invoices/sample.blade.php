<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pratinjau Sampel Invoice | {{ $invoice['brand_line2'] }}</title>
    @vite(['resources/css/app.css'])
    <style>
        :root { --ink: #1E293B; --muted: #64748B; --line: #CBD5E1; --brand: #F97316; }
        .receipt { -webkit-print-color-adjust: exact; print-color-adjust: exact;
                   font-family: ui-monospace, "SFMono-Regular", "JetBrains Mono", "Fira Code", Menlo, Consolas, monospace; }
        .dash { border: 0; border-top: 1px dashed var(--line); margin: 14px 0; }
        .dash-tight { border-top-style: dashed; }
        .avoid-break { break-inside: avoid; }
        .num { font-variant-numeric: tabular-nums; }
        @media print {
            .no-print { display: none !important; }
            html, body { background: #fff !important; margin: 0 !important; }
            .paper { box-shadow: none !important; border: none !important; margin: 0 auto !important; border-radius: 0 !important; max-width: 80mm !important; padding: 4mm !important; }
            .avoid-break { break-inside: avoid; }
            @page { size: 80mm auto; margin: 4mm; }
        }
    </style>
</head>
<body class="receipt bg-[#EEF2F6] text-[#1E293B] antialiased py-6">

    {{-- Kertas struk --}}
    <div class="paper bg-white mx-auto w-full max-w-[620px] rounded-[20px] border border-dashed border-[#CBD5E1] p-8 shadow-sm">

        {{-- Logo --}}
        <div class="flex justify-center">
            <div class="w-[88px] h-[88px] rounded-2xl bg-[#F97316] flex items-center justify-center text-white font-bold text-3xl tracking-widest">
                {{ $invoice['logo_text'] }}
            </div>
        </div>

        {{-- Nama toko --}}
        <div class="text-center mt-4">
            <div class="font-bold uppercase tracking-[0.15em] text-sm leading-snug">{{ $invoice['brand_line1'] }}</div>
            <div class="font-bold uppercase tracking-[0.3em] text-base leading-snug">{{ $invoice['brand_line2'] }}</div>
            <div class="text-[11px] text-[#64748B] mt-1 tracking-wide">{{ $invoice['subtitle'] }}</div>
            <div class="text-xs mt-2 num">#{{ $invoice['number'] }}</div>
            <div class="text-xs text-[#64748B] num">{{ $invoice['date'] }}</div>
        </div>

        <hr class="dash">

        {{-- Pelanggan --}}
        <div class="text-xs space-y-1">
            <div class="flex gap-3">
                <span class="text-[#64748B] shrink-0">PELANGGAN</span>
                <span class="ml-auto text-right break-words">{{ $invoice['customer']['name'] }}</span>
            </div>
            <div class="flex gap-3">
                <span class="text-[#64748B] shrink-0">NO. WA</span>
                <span class="ml-auto text-right num">{{ $invoice['customer']['wa'] }}</span>
            </div>
            <div class="flex gap-3">
                <span class="text-[#64748B] shrink-0">KURIR</span>
                <span class="ml-auto text-right break-words">{{ $invoice['customer']['courier'] }}</span>
            </div>
        </div>

        <hr class="dash">

        {{-- Produk --}}
        @foreach($invoice['items'] as $i => $item)
            <div class="text-xs avoid-break {{ $i > 0 ? 'mt-3 pt-3 border-t border-dashed border-[#E2E8F0]' : '' }}">
                <div class="flex gap-3 items-start">
                    <span class="font-semibold break-words">{{ $i + 1 }}. {{ $item['name'] }}</span>
                    <span class="ml-auto shrink-0 font-semibold num">{{ format_price($item['subtotal']) }}</span>
                </div>
                <div class="text-[11px] text-[#64748B] mt-0.5 num">
                    {{ $item['qty'] }}x @ {{ format_price($item['price']) }} (Jastip: {{ format_price($item['jastip']) }}/pc)
                </div>
                <div class="text-[11px] text-[#64748B]">({{ $item['variant'] }})</div>
            </div>
        @endforeach

        <hr class="dash">

        {{-- Ringkasan pembayaran --}}
        <div class="text-xs space-y-1.5">
            <div class="flex gap-3">
                <span class="text-[#64748B]">TOTAL ITEM (QTY)</span>
                <span class="ml-auto num">{{ $invoice['qty'] }} pcs</span>
            </div>
            <div class="flex gap-3">
                <span class="text-[#64748B]">SUBTOTAL (HARGA BARANG)</span>
                <span class="ml-auto num">{{ format_price($invoice['subtotal']) }}</span>
            </div>
            <div class="flex gap-3">
                <span class="text-[#64748B]">TOTAL FEE JASTIP</span>
                <span class="ml-auto num">{{ format_price($invoice['fee_jastip']) }}</span>
            </div>
            <div class="flex gap-3">
                <span class="text-[#64748B]">FEE PACKING</span>
                <span class="ml-auto num">{{ format_price($invoice['packing']) }}</span>
            </div>

            <div class="flex gap-3 items-baseline pt-2 mt-1 border-t-2 border-solid border-[#1E293B]">
                <span class="font-bold tracking-wide">TOTAL BAYAR</span>
                <span class="ml-auto font-bold text-base num">{{ format_price($invoice['total']) }}</span>
            </div>
        </div>

        <hr class="dash">

        {{-- Info pembayaran --}}
        <div class="text-[11px] text-[#64748B] space-y-0.5">
            <div class="font-semibold text-[#64748B]">{{ $invoice['payment']['title'] }}</div>
            @foreach($invoice['payment']['lines'] as $line)
                <div>{{ $line }}</div>
            @endforeach
        </div>

        <hr class="dash">

        {{-- Footer --}}
        <div class="text-center text-[11px] text-[#64748B] space-y-2 mt-1 mb-2">
            <p class="leading-relaxed">{{ $invoice['thanks'] }}</p>
            <p class="tracking-wide">{{ $invoice['footer_note'] }}</p>
        </div>
    </div>

</body>
</html>