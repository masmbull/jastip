<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $doc['title'] }} {{ $doc['number'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /*
         * HTML/PNG version — shared with html2canvas screenshot.
         * Sengaja TANPA Tailwind: html2canvas tidak memahami color(oklch).
         */
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 20px;
            background: #FFFFFF;
            /* Override DejaVu with a modern font for browser rendering */
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif !important;
        }
        /* Apply modern font to all invoice elements */
        .invoice-doc, .invoice-doc * {
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif !important;
        }
        .qris-invoice-image {
            width: 60mm !important;
            height: 60mm !important;
            max-width: 60mm !important;
            max-height: 60mm !important;
            object-fit: contain;
            image-rendering: -webkit-optimize-contrast;
            background: #FFFFFF;
        }
    </style>
</head>
<body>
    @include('invoices._template', ['doc' => $doc])
</body>
</html>
