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
        .invoice-doc[data-layout="compact"] { font-size: 11px !important; }
        .invoice-doc[data-layout="compact"] .invoice-items td {
            padding-top: 3px !important;
            padding-bottom: 3px !important;
        }
        .invoice-doc[data-layout="compact"] .invoice-items th {
            padding-top: 6px !important;
            padding-bottom: 6px !important;
        }
        .invoice-doc[data-layout="compact"] .invoice-totals-box td {
            padding-top: 4px !important;
            padding-bottom: 4px !important;
        }
    </style>
</head>
<body>
    {{-- Template yang SAMA dengan pratinjau (admin/invoices/sample.blade.php). --}}
    @include('invoices._template', ['doc' => $doc])
</body>
</html>