<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $doc['title'] }} {{ $doc['number'] }}</title>
    <style>
        /* Dipakai sebagai target render PNG (html2canvas) untuk tombol
           WhatsApp. Sengaja TANPA Tailwind: html2canvas tidak memahami
           color(oklch) milik Tailwind v4. Template yang dirender sama persis
           dengan pratinjau/PDF (invoices/_template). */
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px; background: #FFFFFF; font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
    </style>
</head>
<body>
    @include('invoices._template', ['doc' => $doc])
</body>
</html>