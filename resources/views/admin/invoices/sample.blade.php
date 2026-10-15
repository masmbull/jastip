<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pratinjau Cetak Sampel Invoice</title>
    <style>
        /* Pratinjau memakai TEMPLATE YANG SAMA dengan PDF (invoices/_template),
           dibungkus kanvas abu-abu + kertas putih. Jadi apa yang terlihat di
           sini identik dengan hasil PDF. Tanpa Tailwind supaya sama dengan
           renderer dompdf. */
        :root { --ink: #141414; --paper: #FFFFFF; }
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 24px 12px;
            background: #E7E5E4;
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
        }
        .paper {
            width: 100%; max-width: 720px; margin: 0 auto;
            background: var(--paper); padding: 36px;
            border: 2px solid var(--ink);
            box-shadow: 4px 4px 0 var(--ink);
        }
        @media (max-width: 520px) { .paper { padding: 18px; box-shadow: 3px 3px 0 var(--ink); } }
        @media print {
            body { background: #fff; padding: 0; }
            .paper { border: none; box-shadow: none; max-width: none; padding: 0; }
            @page { size: A4; margin: 14mm; }
        }
    </style>
</head>
<body>

    <div class="paper">
        {{-- Template yang SAMA dengan PDF. --}}
        @include('invoices._template', ['doc' => $doc])
    </div>

</body>
</html>