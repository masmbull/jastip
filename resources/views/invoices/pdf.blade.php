<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $doc['title'] }} {{ $doc['number'] }}</title>
    <style>
        @page { size: A4 portrait; margin: 5mm; }
        * { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
        body { margin: 0; color: #141414; font-size: 12px; }
        table { border-collapse: collapse; }
        .invoice-doc[data-layout="compact"] {
            font-size: 10px !important;
            line-height: 1.08 !important;
        }
        .invoice-doc[data-layout="compact"] td,
        .invoice-doc[data-layout="compact"] div,
        .invoice-doc[data-layout="compact"] span {
            font-size: 10px !important;
            line-height: 1.08 !important;
        }
        .invoice-doc[data-layout="compact"] td {
            padding-top: 2px !important;
            padding-bottom: 2px !important;
        }
        .invoice-doc[data-layout="compact"] img {
            max-height: 86px !important;
            max-width: 86px !important;
            width: 86px !important;
            height: 86px !important;
        }
        .invoice-doc[data-layout="compact"] > div {
            margin-top: 4px !important;
            margin-bottom: 4px !important;
        }
    </style>
</head>
<body>
    {{-- Template yang SAMA dengan pratinjau (admin/invoices/sample.blade.php). --}}
    @include('invoices._template', ['doc' => $doc])
</body>
</html>