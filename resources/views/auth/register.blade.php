@extends('layouts.auth')

@section('title', 'Daftar | ' . setting('brand_name', 'NITIP DI END'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#0E7490] via-[#155E75] to-[#083344] flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-xl p-8 border border-[#E2E8F0] dark:border-[#2e323b]">
            <div class="flex items-center justify-center mb-6">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 bg-[#0891B2] rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-xl">{{ strtoupper(substr(setting('brand_name', 'NITIP DI END'), 0, 1)) }}</span>
                    </div>
                    <span class="font-bold text-lg text-[#1E293B] dark:text-[#FDF6EC]">{{ setting('brand_name', 'NITIP DI END') }}</span>
                </div>
            </div>

            <h1 class="text-2xl font-semibold text-center text-[#1E293B] dark:text-[#FDF6EC] mb-1">Buat Akun Baru</h1>
            <p class="text-sm text-[#64748B] dark:text-[#b0b4bd] text-center mb-6">Gratis. Cukup sekali, langsung bisa nitip.</p>

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                <div>
                    @error('name') <p class="text-xs text-red-500 mb-1">{{ $message }}</p> @enderror
                    <label class="text-xs font-medium text-[#64748B] dark:text-[#b0b4bd] mb-1.5 block">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-4 py-2.5 bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#343a44] rounded-lg text-[#1E293B] dark:text-[#FDF6EC] focus:outline-none focus:ring-2 focus:ring-[#0891B2]/40 focus:border-[#0891B2]"/>
                </div>

                <div>
                    @error('username') <p class="text-xs text-red-500 mb-1">{{ $message }}</p> @enderror
                    <label class="text-xs font-medium text-[#64748B] dark:text-[#b0b4bd] mb-1.5 block">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                           placeholder="tanpa spasi"
                           class="w-full px-4 py-2.5 bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#343a44] rounded-lg text-[#1E293B] dark:text-[#FDF6EC] placeholder-[#94A3B8] focus:outline-none focus:ring-2 focus:ring-[#0891B2]/40 focus:border-[#0891B2]"/>
                </div>

                <div>
                    @error('email') <p class="text-xs text-red-500 mb-1">{{ $message }}</p> @enderror
                    <label class="text-xs font-medium text-[#64748B] dark:text-[#b0b4bd] mb-1.5 block">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="kamu@email.com"
                           class="w-full px-4 py-2.5 bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#343a44] rounded-lg text-[#1E293B] dark:text-[#FDF6EC] placeholder-[#94A3B8] focus:outline-none focus:ring-2 focus:ring-[#0891B2]/40 focus:border-[#0891B2]"/>
                </div>

                <div>
                    @error('password') <p class="text-xs text-red-500 mb-1">{{ $message }}</p> @enderror
                    <label class="text-xs font-medium text-[#64748B] dark:text-[#b0b4bd] mb-1.5 block">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2.5 bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#343a44] rounded-lg text-[#1E293B] dark:text-[#FDF6EC] focus:outline-none focus:ring-2 focus:ring-[#0891B2]/40 focus:border-[#0891B2]"/>
                </div>

                <div>
                    <label class="text-xs font-medium text-[#64748B] dark:text-[#b0b4bd] mb-1.5 block">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-2.5 bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#343a44] rounded-lg text-[#1E293B] dark:text-[#FDF6EC] focus:outline-none focus:ring-2 focus:ring-[#0891B2]/40 focus:border-[#0891B2]"/>
                </div>

                <button type="submit"
                        class="w-full px-6 py-3 bg-[#0891B2] hover:bg-[#0E7490] text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                    Daftar & Masuk
                </button>
            </form>

            <p class="text-sm text-center text-[#64748B] dark:text-[#b0b4bd] mt-6">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-[#0891B2] hover:underline font-medium">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
