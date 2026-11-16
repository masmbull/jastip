<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=794">
    <title>Invoice {{ $doc['number'] }} — gambar dibagikan</title>

    {{--
        Gambar dibagikan ke WhatsApp. Render oleh Chrome headless (Browsershot)
        yang SAMA dengan PDF, jadi alignment dan ikon selalu sama persis.
        Layout ini sengaja persis dengan pratinjau `/i/{token}/gambar` saat
        di-screenshot fullPage: body lebar 794px dengan padding 20/42px,
        latar putih, dan .invoice-doc mengisi seluruh lebar.
    --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { margin: 0; padding: 0; background: #FFFFFF; }
        body {
            width: 794px;
            padding: 20px 42px;
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
            background: #FFFFFF;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .invoice-doc, .invoice-doc * {
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
        }
        .qris-invoice-image { object-fit: contain; }
    </style>
</head>
<body>
    @include('invoices._template', ['doc' => $doc])
</body>
</html>
