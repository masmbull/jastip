@extends('layouts.auth')

@section('title', 'Masuk | ' . setting('brand_name', 'NITIP DI END'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#0E7490] via-[#155E75] to-[#083344] flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-xl p-6 sm:p-8 border border-[#E2E8F0] dark:border-[#2e323b]">
            {{-- Logo --}}
            <div class="flex items-center justify-center mb-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-[#0891B2] rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-xl">{{ strtoupper(substr(setting('brand_name', 'NITIP DI END'), 0, 1)) }}</span>
                    </div>
                    <span class="font-bold text-lg text-[#1E293B] dark:text-[#f1f5f9]">{{ setting('brand_name', 'NITIP DI END') }}</span>
                </div>
            </div>

            <h1 class="text-2xl font-semibold text-center text-[#1E293B] dark:text-[#f1f5f9] mb-1">Masuk ke Akun</h1>
            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] text-center mb-6">Masuk untuk pesan & lacak titipanmu.</p>

            @if (session('success'))
                <div class="mb-4 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-300 text-sm px-3 py-2">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-300 text-sm px-3 py-2">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">

                <div>
                    <label for="login" class="flex items-center gap-2 text-xs font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-1.5">
                        <x-icon name="user" class="w-4 h-4" />
                        Username / Email
                    </label>
                    <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus
                           placeholder="nama atau kamu@email.com"
                           class="w-full px-4 py-2.5 bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#343a44] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] placeholder-[#64748B] focus:outline-none focus:ring-2 focus:ring-[#0891B2]/40 focus:border-[#0891B2]"/>
                    @error('login')
                        <p class="text-xs text-red-600 dark:text-red-300 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="flex items-center gap-2 text-xs font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-1.5">
                        <x-icon name="lock" class="w-4 h-4" />
                        Password
                    </label>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                           placeholder="Masukkan password"
                           class="w-full px-4 py-2.5 bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#343a44] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] placeholder-[#64748B] focus:outline-none focus:ring-2 focus:ring-[#0891B2]/40 focus:border-[#0891B2]"/>
                    @error('password')
                        <p class="text-xs text-red-600 dark:text-red-300 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 min-h-[44px] text-sm text-[#1E293B] dark:text-[#cbd5e1] cursor-pointer">
                        <input type="checkbox" name="remember" value="on"
                               class="w-4 h-4 rounded border-[#E2E8F0] dark:border-[#343a44] text-[#0891B2] cursor-pointer">
                        Ingat saya
                    </label>
                </div>

                <button type="submit"
                        class="w-full min-h-[44px] px-6 py-3 bg-[#0891B2] hover:bg-[#0E7490] text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Masuk
                </button>
            </form>

            <p class="text-sm text-center text-[#64748B] dark:text-[#cbd5e1] mt-6">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-[#0891B2] dark:text-[#67e8f9] hover:underline font-medium focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">Daftar sekarang</a>
            </p>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1 min-h-[44px] px-3 text-xs text-white/80 hover:text-white focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">
                <x-icon name="arrow-left" class="w-4 h-4" />
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection