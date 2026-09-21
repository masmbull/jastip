<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', setting('brand_name', 'NITIP DI END'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        /* Apply saved / system dark preference BEFORE styles load (no FOUC). */
        (function () {
            if (localStorage.theme === 'dark' ||
                (!localStorage.theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js" defer></script>
</head>
<body class="bg-[#f5f0e8] text-[#333333] font-sans antialiased min-h-screen">
    @yield('content')
</body>
</html>
