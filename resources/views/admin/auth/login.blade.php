@extends('layouts.auth')

@section('title', 'Masuk | ' . setting('brand_name'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#0E7490] via-[#155E75] to-[#083344] flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-[#1a1a1a] rounded-2xl shadow-xl p-6 sm:p-8 border border-[#2e323b]">
            {{-- Logo --}}
            <div class="flex items-center justify-center mb-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-[#0891B2] rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-xl">{{ strtoupper(substr(setting('brand_name', 'NITIP DI END'), 0, 1)) }}</span>
                    </div>
                    <span class="font-bold text-lg text-[#f1f5f9]">{{ setting('brand_name', 'NITIP DI END') }}</span>
                </div>
            </div>

            <h1 class="text-2xl font-semibold text-center text-[#f1f5f9] mb-1 flex items-center justify-center gap-2">Halo Admin <x-icon name="sparkles" class="w-6 h-6 text-[#67e8f9]" /></h1>
            <p class="text-sm text-[#cbd5e1] text-center mb-6">Masuk ke dashboard {{ setting('brand_name', 'NITIP DI END') }}</p>

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="space-y-5">
                    {{-- Username --}}
                    <div>
                        <label for="username" class="flex items-center gap-2 text-xs font-medium text-[#cbd5e1] mb-1.5">
                            <x-icon name="user" class="w-4 h-4" />
                            Username
                        </label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                               placeholder="admin atau 081234567890"
                               class="w-full px-4 py-2.5 bg-[#23252b] border border-[#343a44] rounded-lg text-[#f1f5f9] placeholder-[#94a3b8] focus:outline-none focus:ring-2 focus:ring-[#0891B2]/40 focus:border-[#0891B2]"/>
                        @error('username')
                            <p class="text-xs text-red-300 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="flex items-center gap-2 text-xs font-medium text-[#cbd5e1] mb-1.5">
                            <x-icon name="lock" class="w-4 h-4" />
                            Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required autocomplete="current-password"
                                   placeholder="Masukkan password"
                                   class="w-full px-4 py-2.5 bg-[#23252b] border border-[#343a44] rounded-lg text-[#f1f5f9] placeholder-[#94a3b8] pr-12 focus:outline-none focus:ring-2 focus:ring-[#0891B2]/40 focus:border-[#0891B2]"/>
                            <button type="button" onclick="togglePassword()" aria-label="Tampilkan atau sembunyikan password" title="Tampilkan password"
                                    class="absolute right-1 top-1/2 -translate-y-1/2 p-2 min-h-[44px] min-w-[44px] flex items-center justify-center text-[#94a3b8] hover:text-[#67e8f9] focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">
                                <x-icon id="eyeIcon" name="eye" class="w-5 h-5" />
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-red-300 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="remember" name="remember" value="on"
                               class="w-4 h-4 rounded border-[#343a44] bg-[#23252b] text-[#0891B2] cursor-pointer">
                        <label for="remember" class="text-sm text-[#cbd5e1] cursor-pointer hover:text-[#f1f5f9] transition-colors">
                            Ingat saya
                        </label>
                    </div>
                </div>

                <button type="submit"
                        class="w-full min-h-[44px] mt-6 px-6 py-2.5 bg-[#0891B2] hover:bg-[#0E7490] text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Masuk
                </button>
            </form>
        </div>

        {{-- Footer badge --}}
        <div class="mt-6 flex justify-center">
            <span class="inline-flex items-center px-4 py-2 rounded-xl bg-[#0891B2]/15 text-[#67e8f9] text-xs font-medium">
                {{ setting('brand_name', 'NITIP DI END') }} &middot; v{{ config('app.version', '1.0') }}
            </span>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>';
    }
}
</script>
@endsection