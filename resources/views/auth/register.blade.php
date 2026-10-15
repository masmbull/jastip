@extends('layouts.auth')

@section('title', 'Daftar | ' . setting('brand_name', 'NITIP DI END'))

@section('panel-title', 'Mulai kelola jastip kamu.')
@section('panel-copy', 'Gratis. Cukup sekali daftar, langsung bisa catat dan kirim invoice.')

@section('content')
    <div class="flex items-center gap-3 mb-6">
        <span class="h-11 w-11 rounded-[10px] bg-[#FFC800] border-2 border-[#141414] flex items-center justify-center text-[#141414] font-black text-xl">
            {{ strtoupper(substr(setting('brand_name', 'NITIP DI END'), 0, 1)) }}
        </span>
        <span class="font-black text-lg tracking-tight">{{ setting('brand_name', 'NITIP DI END') }}</span>
    </div>

    <h1 class="text-2xl font-black tracking-tight">Buat Akun Baru</h1>
    <p class="text-sm font-medium text-[#475569] mt-1 mb-6">Isi data di bawah, langsung bisa nitip.</p>

    <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-xs font-black uppercase tracking-wide mb-1.5">Nama Lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                   class="nb-input w-full px-4 py-2.5 bg-white"/>
            @error('name') <p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="username" class="block text-xs font-black uppercase tracking-wide mb-1.5">Username</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required
                   placeholder="tanpa spasi"
                   class="nb-input w-full px-4 py-2.5 bg-white"/>
            @error('username') <p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-xs font-black uppercase tracking-wide mb-1.5">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   placeholder="kamu@email.com"
                   class="nb-input w-full px-4 py-2.5 bg-white"/>
            @error('email') <p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-xs font-black uppercase tracking-wide mb-1.5">Password</label>
                <input type="password" id="password" name="password" required autocomplete="new-password"
                       class="nb-input w-full px-4 py-2.5 bg-white"/>
                @error('password') <p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-xs font-black uppercase tracking-wide mb-1.5">Ulangi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                       class="nb-input w-full px-4 py-2.5 bg-white"/>
            </div>
        </div>

        <button type="submit"
                class="nb-btn w-full min-h-[48px] px-6 py-3 bg-[#FFC800] text-[#141414] font-black text-base">
            Daftar &amp; Masuk
        </button>
    </form>

    <p class="text-sm font-medium text-[#475569] text-center mt-6">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-black text-[#141414] underline underline-offset-2 hover:text-[#B45309]">Masuk di sini</a>
    </p>
@endsection