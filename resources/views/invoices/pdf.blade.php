<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $doc['title'] }} {{ $doc['number'] }}</title>
    <style>
        /* dompdf: tidak memuat CSS eksternal, jadi reset minimal di sini. */
        * { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
        body { margin: 0; color: #141414; font-size: 12px; }
        table { border-collapse: collapse; }
    </style>
</head>
<body>
    {{-- Template yang SAMA dengan pratinjau (admin/invoices/sample.blade.php). --}}
    @include('invoices._template', ['doc' => $doc])
</body>
</html>