@extends('layouts.auth')

@section('title', 'Masuk Admin | ' . setting('brand_name'))

@section('panel-title', 'Kelola jastip dari satu panel.')
@section('panel-copy', 'Catat pesanan, kirim invoice, pantau ongkir dan status barang.')

@section('content')
    <div class="flex items-center gap-3 mb-6">
        <span class="h-11 w-11 rounded-[10px] bg-[#141414] text-[#FFC800] border-2 border-[#141414] flex items-center justify-center font-black text-xl">
            {{ strtoupper(substr(setting('brand_name', 'N'), 0, 1)) }}
        </span>
        <div>
            <p class="font-black text-lg tracking-tight leading-none">{{ setting('brand_name', 'NITIP DI END') }}</p>
            <p class="text-xs font-bold uppercase tracking-wide text-[#475569]">Panel Admin</p>
        </div>
    </div>

    <h1 class="text-2xl font-black tracking-tight">Masuk Admin</h1>
    <p class="text-sm font-medium text-[#475569] mt-1 mb-6">Gunakan akun admin untuk melanjutkan.</p>

    @if (session('error'))
        <div class="mb-4 rounded-[8px] bg-[#FEE2E2] border-2 border-[#141414] text-[#7F1D1D] text-sm font-medium px-3 py-2">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="username" class="flex items-center gap-2 text-xs font-black uppercase tracking-wide mb-1.5">
                <x-icon name="user" class="w-4 h-4" /> Username
            </label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                   placeholder="admin"
                   class="nb-input w-full px-4 py-2.5 bg-white"/>
            @error('username') <p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="flex items-center gap-2 text-xs font-black uppercase tracking-wide mb-1.5">
                <x-icon name="lock" class="w-4 h-4" /> Password
            </label>
            <div class="relative">
                <input type="password" id="password" name="password" required autocomplete="current-password"
                       placeholder="Masukkan password"
                       class="nb-input w-full px-4 py-2.5 pr-14 bg-white"/>
                <button type="button" onclick="togglePassword()" aria-label="Tampilkan atau sembunyikan password"
                        title="Tampilkan password"
                        class="absolute right-1 top-1/2 -translate-y-1/2 min-h-[44px] min-w-[44px] flex items-center justify-center text-[#475569] hover:text-[#141414] rounded focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <x-icon id="eyeIcon" name="eye" class="w-5 h-5" />
                </button>
            </div>
            @error('password') <p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 min-h-[44px] text-sm font-medium cursor-pointer">
            <input type="checkbox" id="remember" name="remember" value="on"
                   class="w-4 h-4 rounded-[4px] border-2 border-[#141414] text-[#141414] cursor-pointer">
            Ingat saya
        </label>

        <button type="submit"
                class="nb-btn w-full min-h-[48px] px-6 py-3 bg-[#FFC800] text-[#141414] font-black text-base">
            Masuk
        </button>
    </form>

    <p class="mt-6 text-center text-xs font-medium text-[#475569]">
        {{ setting('brand_name', 'NITIP DI END') }} &middot; v{{ config('app.version', '1.0') }}
    </p>
@endsection

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (!input || !icon) return;
    const showing = input.type === 'password';
    input.type = showing ? 'text' : 'password';
    icon.style.opacity = showing ? '0.55' : '1';
}
</script>
@endpush