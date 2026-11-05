<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $doc['title'] }} {{ $doc['number'] }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 10mm;
        }
        * {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            color: #141414;
            font-size: 12px;
            background: #FFFFFF;
        }
        table {
            border-collapse: collapse;
        }
        thead {
            display: table-header-group;
        }
        tr, td, th {
            page-break-inside: avoid;
        }
        .invoice-items thead {
            display: table-header-group;
        }

        /* ── Compact mode: 13+ items ──────────────────────────────────── */
        .invoice-doc[data-layout="compact"] {
            font-size: 9px !important;
            line-height: 1.1 !important;
        }
        .invoice-doc[data-layout="compact"] td,
        .invoice-doc[data-layout="compact"] th,
        .invoice-doc[data-layout="compact"] div,
        .invoice-doc[data-layout="compact"] span {
            font-size: 9px !important;
            line-height: 1.1 !important;
        }
        .invoice-doc[data-layout="compact"] .invoice-items td {
            padding-top: 2px !important;
            padding-bottom: 2px !important;
            padding-left: 5px !important;
            padding-right: 5px !important;
        }
        .invoice-doc[data-layout="compact"] .invoice-items th {
            padding-top: 4px !important;
            padding-bottom: 4px !important;
        }
        .invoice-doc[data-layout="compact"] .invoice-totals-box td {
            padding-top: 3px !important;
            padding-bottom: 3px !important;
        }
        .invoice-doc[data-layout="compact"] .invoice-logo {
            width: 48px !important;
            height: 48px !important;
        }

        /* ── QRIS image: crisp rendering ──────────────────────────────── */
        .qris-invoice-image {
            width: 52mm !important;
            height: 52mm !important;
            max-width: 52mm !important;
            max-height: 52mm !important;
            object-fit: contain;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: pixelated;
            background: #FFFFFF;
        }

        /* ── Bank logo images ─────────────────────────────────────────── */
        .bank-logo-img {
            display: block;
            width: 48px;
            height: auto;
            object-fit: contain;
            object-position: left center;
        }
    </style>
</head>
<body>
    @include('invoices._template', ['doc' => $doc])
</body>
</html>
