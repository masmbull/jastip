<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', setting('brand_name', 'NITIP DI END'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-colors')
</head>
<body class="bg-[#ecfeff] text-[#1E293B] font-sans antialiased min-h-screen">
    @yield('content')
</body>
</html>
