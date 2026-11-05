<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $doc['title'] }} {{ $doc['number'] }} | {{ $doc['brand'] }}</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* Halaman cetak memakai TEMPLATE yang sama dengan pratinjau
           (invoices/_template), jadi tidak ada lagi desain kedua. */
        body { background: #E9EEF3; }
        .sheet-paper { background: #fff; }
        /* Modern font override for browser preview */
        .invoice-doc, .invoice-doc * {
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif !important;
        }
        @media (max-width: 640px) { .sheet-paper { padding: 18px !important; } }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .sheet-paper { box-shadow: none !important; border: none !important; margin: 0 auto !important; max-width: none !important; padding: 0 !important; }
            @page { size: A4; margin: 5mm; }
        }
    </style>
</head>
<body class="font-sans text-[#141414]">

@if(!($embed ?? false))
<div class="no-print sticky top-0 z-10 bg-white/95 backdrop-blur border-b-2 border-[#141414]">
    <div class="max-w-[860px] mx-auto px-4 py-3 flex items-center justify-between gap-3">
        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-[#475569] hover:text-[#141414] transition-colors">
            <x-icon name="arrow-left" class="w-4 h-4" /> Kembali ke pesanan
        </a>
        <button type="button" onclick="window.print()"
                class="nb-btn inline-flex items-center gap-2 px-4 py-2 bg-[#FFC800] text-[#141414] text-sm font-black">
            <x-icon name="printer" class="w-4 h-4" /> Cetak / Simpan PDF
        </button>
    </div>
</div>
@endif

<div class="sheet-paper max-w-[860px] mx-auto my-6 p-10 shadow-sm border-2 border-[#141414] rounded-[10px]">
    @include('invoices._template', ['doc' => $doc])
</div>

@if($autoPrint)
    <script>window.addEventListener('load', () => window.print());</script>
@endif
</body>
</html>