@extends('layouts.auth')

@section('title', 'Daftar | ' . setting('brand_name', 'NITIP DI END'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#0E7490] via-[#155E75] to-[#083344] flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-xl p-6 sm:p-8 border border-[#E2E8F0] dark:border-[#2e323b]">
            <div class="flex items-center justify-center mb-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-[#0891B2] rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-xl">{{ strtoupper(substr(setting('brand_name', 'NITIP DI END'), 0, 1)) }}</span>
                    </div>
                    <span class="font-bold text-lg text-[#1E293B] dark:text-[#f1f5f9]">{{ setting('brand_name', 'NITIP DI END') }}</span>
                </div>
            </div>

            <h1 class="text-2xl font-semibold text-center text-[#1E293B] dark:text-[#f1f5f9] mb-1">Buat Akun Baru</h1>
            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] text-center mb-6">Gratis. Cukup sekali, langsung bisa nitip.</p>

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="text-xs font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-1.5 block">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-4 py-2.5 bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#343a44] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#0891B2]/40 focus:border-[#0891B2]"/>
                    @error('name') <p class="text-xs text-red-600 dark:text-red-300 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="username" class="text-xs font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-1.5 block">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required
                           placeholder="tanpa spasi"
                           class="w-full px-4 py-2.5 bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#343a44] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] placeholder-[#64748B] focus:outline-none focus:ring-2 focus:ring-[#0891B2]/40 focus:border-[#0891B2]"/>
                    @error('username') <p class="text-xs text-red-600 dark:text-red-300 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="text-xs font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-1.5 block">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           placeholder="kamu@email.com"
                           class="w-full px-4 py-2.5 bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#343a44] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] placeholder-[#64748B] focus:outline-none focus:ring-2 focus:ring-[#0891B2]/40 focus:border-[#0891B2]"/>
                    @error('email') <p class="text-xs text-red-600 dark:text-red-300 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="text-xs font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-1.5 block">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                           class="w-full px-4 py-2.5 bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#343a44] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#0891B2]/40 focus:border-[#0891B2]"/>
                    @error('password') <p class="text-xs text-red-600 dark:text-red-300 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="text-xs font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-1.5 block">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                           class="w-full px-4 py-2.5 bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#343a44] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#0891B2]/40 focus:border-[#0891B2]"/>
                </div>

                <button type="submit"
                        class="w-full min-h-[44px] px-6 py-3 bg-[#0891B2] hover:bg-[#0E7490] text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Daftar & Masuk
                </button>
            </form>

            <p class="text-sm text-center text-[#64748B] dark:text-[#cbd5e1] mt-6">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-[#0891B2] dark:text-[#67e8f9] hover:underline font-medium focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection