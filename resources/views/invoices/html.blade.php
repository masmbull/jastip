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
         * HTML/PNG version for html2canvas screenshot sharing.
         * html2canvas cannot use color(oklch) from Tailwind v4,
         * so we explicitly load Plus Jakarta Sans here.
         */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            padding: 20px;
            background: #FFFFFF;
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
        }
        .invoice-doc, .invoice-doc * {
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
        }
        .qris-invoice-image {
            width: 60mm !important;
            height: 60mm !important;
            object-fit: contain;
            background: #FFFFFF;
        }
    </style>
</head>
<body>
    @include('invoices._template', ['doc' => $doc])
</body>
</html>
