<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $doc['title'] }} {{ $doc['number'] }}</title>
    <style>
        /* Dipakai sebagai target render PNG (html2canvas) untuk tombol
           berbagi invoice. Sengaja TANPA Tailwind: html2canvas tidak memahami
           color(oklch) milik Tailwind v4. Template yang dirender sama persis
           dengan pratinjau/PDF (invoices/_template). */
        * { box-sizing: border-box; }
        body { margin: 0; padding: 16px; background: #FFFFFF; font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
        .invoice-doc.invoice-doc-compact {
            font-size: 11px !important;
            line-height: 1.18 !important;
        }
        .invoice-doc.invoice-doc-compact td {
            padding-top: 3px !important;
            padding-bottom: 3px !important;
        }
    </style>
</head>
<body>
    @include('invoices._template', ['doc' => $doc])
</body>
</html>