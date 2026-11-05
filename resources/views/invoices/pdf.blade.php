<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $doc['title'] }} {{ $doc['number'] }}</title>

    {{--
        Browsershot renders with real Chrome — we can use:
        - Google Fonts (Chrome waits for them via waitUntilNetworkIdle)
        - Emoji
        - Flexbox / CSS Grid
        - box-shadow, border-radius, gradients
        - External images (storage URLs)
    --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #141414;
            background: #FFFFFF;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 11mm 14mm;
            }
            body { margin: 0; }
        }

        /* ── QRIS image ──────────────────────────────────────── */
        .qris-invoice-image {
            width: 75mm !important;
            height: 75mm !important;
            object-fit: contain;
            display: block;
            background: #FFFFFF;
            image-rendering: -webkit-optimize-contrast;
        }

        /* ── Compact mode (13+ items): scale everything down ─── */
        .invoice-doc[data-layout="compact"] {
            font-size: 9px !important;
        }
        .invoice-doc[data-layout="compact"] .section-header,
        .invoice-doc[data-layout="compact"] .section-header * {
            font-size: 9px !important;
        }
        .invoice-doc[data-layout="compact"] .invoice-title {
            font-size: 26px !important;
        }
        .invoice-doc[data-layout="compact"] .brand-name {
            font-size: 14px !important;
        }
        .invoice-doc[data-layout="compact"] .info-card {
            padding: 6px 8px !important;
        }
        .invoice-doc[data-layout="compact"] .items-th,
        .invoice-doc[data-layout="compact"] .items-td {
            padding: 3px 5px !important;
            font-size: 8.5px !important;
        }
        .invoice-doc[data-layout="compact"] .totals-td {
            padding: 3px 8px !important;
            font-size: 8.5px !important;
        }
        .invoice-doc[data-layout="compact"] .pay-card-body {
            padding: 6px 8px !important;
        }
        .invoice-doc[data-layout="compact"] .qris-invoice-image {
            width: 40mm !important;
            height: 40mm !important;
        }
        .invoice-doc[data-layout="compact"] .bank-logo {
            width: 72px !important;
            height: 28px !important;
        }
        .invoice-doc[data-layout="compact"] .invoice-logo {
            width: 48px !important;
            height: 48px !important;
        }
    </style>
</head>
<body>
    @include('invoices._template', ['doc' => $doc])
</body>
</html>
