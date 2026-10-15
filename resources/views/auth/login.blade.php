@extends('layouts.auth')

@section('title', 'Masuk | ' . setting('brand_name', 'NITIP DI END'))

@section('panel-title', 'Masuk, lanjut kelola titipanmu.')
@section('panel-copy', 'Semua pesanan, invoice, dan status titipan ada di satu tempat.')

@section('content')
    <div class="flex items-center gap-3 mb-6">
        <span class="h-11 w-11 rounded-[10px] bg-[#FFC800] border-2 border-[#141414] flex items-center justify-center text-[#141414] font-black text-xl">
            {{ strtoupper(substr(setting('brand_name', 'NITIP DI END'), 0, 1)) }}
        </span>
        <span class="font-black text-lg tracking-tight">{{ setting('brand_name', 'NITIP DI END') }}</span>
    </div>

    <h1 class="text-2xl font-black tracking-tight">Masuk ke Akun</h1>
    <p class="text-sm font-medium text-[#475569] mt-1 mb-6">Masuk untuk pesan dan lacak titipanmu.</p>

    @if (session('success'))
        <div class="mb-4 rounded-[8px] bg-[#DCFCE7] border-2 border-[#141414] text-[#14532D] text-sm font-medium px-3 py-2">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-[8px] bg-[#FEE2E2] border-2 border-[#141414] text-[#7F1D1D] text-sm font-medium px-3 py-2">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="redirect" value="{{ request('redirect') }}">

        <div>
            <label for="login" class="flex items-center gap-2 text-xs font-black uppercase tracking-wide mb-1.5">
                <x-icon name="user" class="w-4 h-4" />
                Username / Email
            </label>
            <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus
                   placeholder="nama atau kamu@email.com"
                   class="nb-input w-full px-4 py-2.5 bg-white"/>
            @error('login')
                <p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="flex items-center gap-2 text-xs font-black uppercase tracking-wide mb-1.5">
                <x-icon name="lock" class="w-4 h-4" />
                Password
            </label>
            <input type="password" id="password" name="password" required autocomplete="current-password"
                   placeholder="Masukkan password"
                   class="nb-input w-full px-4 py-2.5 bg-white"/>
            @error('password')
                <p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2 min-h-[44px] text-sm font-medium cursor-pointer">
            <input type="checkbox" name="remember" value="on"
                   class="w-4 h-4 rounded-[4px] border-2 border-[#141414] text-[#141414] cursor-pointer">
            Ingat saya
        </label>

        <button type="submit"
                class="nb-btn w-full min-h-[48px] px-6 py-3 bg-[#FFC800] text-[#141414] font-black text-base">
            Masuk
        </button>
    </form>

    <p class="text-sm font-medium text-[#475569] text-center mt-6">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-black text-[#141414] underline underline-offset-2 hover:text-[#B45309]">Daftar sekarang</a>
    </p>
@endsection