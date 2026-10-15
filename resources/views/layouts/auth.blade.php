<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', setting('brand_name', 'NITIP DI END'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#FAF6EF">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-colors')
</head>
{{-- Halaman auth gaya neo-brutalist: split panel.
     Kiri = panel brand/nilai jual (blok tinta), kanan = form di kertas.
     Struktur split panel terinspirasi referensi, tampilannya murni gaya kita. --}}
<body class="bg-[#FAF6EF] text-[#141414] font-sans antialiased min-h-screen">

<div class="min-h-screen grid lg:grid-cols-2">

    {{-- Panel brand --}}
    <aside class="relative hidden lg:flex flex-col justify-between bg-[#141414] text-[#FAF6EF] p-10 xl:p-14">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 w-max">
            <span class="h-11 w-11 rounded-[10px] bg-[#FFC800] border-2 border-[#FAF6EF] flex items-center justify-center text-[#141414] font-black text-xl">
                {{ strtoupper(substr(setting('brand_name', 'N'), 0, 1)) }}
            </span>
            <span class="font-black text-lg tracking-tight">{{ setting('brand_name', 'NITIP DI END') }}</span>
        </a>

        <div class="max-w-md">
            <h1 class="text-4xl xl:text-5xl font-black leading-[1.05] tracking-tight">
                @yield('panel-title', 'Titip apa pun, kami urus sampai rumah.')
            </h1>
            <p class="mt-4 text-[#D6D3D1] leading-relaxed">
                @yield('panel-copy', 'Catat pesanan, hitung ongkir, buat invoice, dan pantau status titipan dalam satu tempat.')
            </p>

            <ul class="mt-8 space-y-3">
                @foreach([
                    'Invoice otomatis, bisa dikirim ke WhatsApp',
                    'Ongkir semua ekspedisi dalam satu klik',
                    'Checklist belanja dan status PO',
                ] as $point)
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 h-6 w-6 shrink-0 rounded-[6px] bg-[#05E17A] border-2 border-[#FAF6EF] flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-[#141414]" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span class="text-sm font-medium text-[#FAF6EF]">{{ $point }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <p class="text-xs text-[#A8A29E]">&copy; {{ date('Y') }} {{ setting('brand_name', 'NITIP DI END') }}</p>
    </aside>

    {{-- Panel form --}}
    <main class="flex items-center justify-center p-4 sm:p-8">
        <div class="w-full max-w-md">
            {{-- Brand ringkas untuk mobile (panel kiri disembunyikan) --}}
            <a href="{{ route('home') }}" class="lg:hidden inline-flex items-center gap-3 mb-6">
                <span class="h-10 w-10 rounded-[10px] bg-[#FFC800] border-2 border-[#141414] flex items-center justify-center text-[#141414] font-black text-lg">
                    {{ strtoupper(substr(setting('brand_name', 'N'), 0, 1)) }}
                </span>
                <span class="font-black text-base tracking-tight">{{ setting('brand_name', 'NITIP DI END') }}</span>
            </a>

            <div class="bg-white border-2 border-[#141414] rounded-[12px] p-6 sm:p-8" style="box-shadow: 4px 4px 0 #141414">
                @yield('content')
            </div>
        </div>
    </main>
</div>

</body>
</html>