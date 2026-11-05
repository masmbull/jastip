<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $doc['title'] }} {{ $doc['number'] }}</title>
    <style>
        @page {
            size: A4 portrait;
            /* Extra bottom margin prevents footer from being clipped */
            margin: 10mm 11mm 14mm;
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
        table { border-collapse: collapse; }
        thead { display: table-header-group; }
        /* Prevent rows from splitting across pages */
        tr, td, th { page-break-inside: avoid; }
        .invoice-items thead { display: table-header-group; }

        /* ── QRIS image: crisp rendering ─────────────────────────── */
        .qris-invoice-image {
            width: 60mm !important;
            height: 60mm !important;
            max-width: 60mm !important;
            max-height: 60mm !important;
            object-fit: contain;
            image-rendering: -webkit-optimize-contrast;
            background: #FFFFFF;
        }

        /* ── Compact mode: 13+ items ──────────────────────────────── */
        /* Shrinks the ENTIRE invoice — all sections, not just items table */
        .invoice-doc[data-layout="compact"] {
            font-size: 8.5px !important;
            line-height: 1.05 !important;
        }
        .invoice-doc[data-layout="compact"] td,
        .invoice-doc[data-layout="compact"] th,
        .invoice-doc[data-layout="compact"] div,
        .invoice-doc[data-layout="compact"] span {
            font-size: 8.5px !important;
            line-height: 1.05 !important;
        }
        /* Items table — tightest padding */
        .invoice-doc[data-layout="compact"] .invoice-items td {
            padding-top: 2px !important;
            padding-bottom: 2px !important;
            padding-left: 4px !important;
            padding-right: 4px !important;
        }
        .invoice-doc[data-layout="compact"] .invoice-items th {
            padding-top: 3px !important;
            padding-bottom: 3px !important;
            padding-left: 4px !important;
            padding-right: 4px !important;
        }
        /* Totals box */
        .invoice-doc[data-layout="compact"] .invoice-totals-box td {
            padding-top: 2px !important;
            padding-bottom: 2px !important;
            padding-left: 6px !important;
            padding-right: 6px !important;
        }
        /* Logo */
        .invoice-doc[data-layout="compact"] .invoice-logo {
            width: 44px !important;
            height: 44px !important;
        }
        /* Header section — tighter */
        .invoice-doc[data-layout="compact"] .invoice-header-title {
            font-size: 22px !important;
        }
        /* Customer + ringkasan cards — reduce padding */
        .invoice-doc[data-layout="compact"] .invoice-info-card td {
            padding: 6px 8px !important;
        }
        /* Payment section cards — reduce padding */
        .invoice-doc[data-layout="compact"] .invoice-pay-card td,
        .invoice-doc[data-layout="compact"] .invoice-pay-card div {
            padding-top: 3px !important;
            padding-bottom: 3px !important;
        }
        /* QRIS image — smaller in compact */
        .invoice-doc[data-layout="compact"] .qris-invoice-image {
            width: 40mm !important;
            height: 40mm !important;
            max-width: 40mm !important;
            max-height: 40mm !important;
        }
    </style>
</head>
<body>
    @include('invoices._template', ['doc' => $doc])
</body>
</html>
