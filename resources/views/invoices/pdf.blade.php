<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $doc['title'] }} {{ $doc['number'] }}</title>
    <style>
        @page { size: A4 portrait; margin: 9mm 10mm 9mm; }
        * { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
        body { margin: 0; color: #141414; font-size: 12px; }
        table { border-collapse: collapse; }
        thead { display: table-header-group; }
        tr, td, th { page-break-inside: avoid; }
        .invoice-items thead { display: table-header-group; }
        .invoice-doc[data-layout="compact"] { font-size: 9px !important; line-height: 1.05 !important; }
        .invoice-doc[data-layout="compact"] td,
        .invoice-doc[data-layout="compact"] th,
        .invoice-doc[data-layout="compact"] div,
        .invoice-doc[data-layout="compact"] span {
            font-size: 9px !important;
            line-height: 1.05 !important;
        }
        .invoice-doc[data-layout="compact"] .invoice-items td {
            padding-top: 1px !important;
            padding-bottom: 1px !important;
            padding-left: 5px !important;
            padding-right: 5px !important;
        }
        .invoice-doc[data-layout="compact"] .invoice-items th {
            padding-top: 4px !important;
            padding-bottom: 4px !important;
        }
        .invoice-doc[data-layout="compact"] .invoice-totals-box td {
            padding-top: 2px !important;
            padding-bottom: 2px !important;
        }
        .invoice-doc[data-layout="compact"] .invoice-logo {
            max-height: 84px !important;
            max-width: 84px !important;
        }
        .qris-invoice-image {
            width: 60mm !important;
            height: 60mm !important;
            max-width: 60mm !important;
            max-height: 60mm !important;
            object-fit: contain;
            image-rendering: -webkit-optimize-contrast;
        }
    </style>
</head>
<body>
    {{-- Template yang SAMA dengan pratinjau (admin/invoices/sample.blade.php). --}}
    @include('invoices._template', ['doc' => $doc])
</body>
</html>