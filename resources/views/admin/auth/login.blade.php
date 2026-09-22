@extends('layouts.auth')

@section('title', 'Masuk | ' . setting('brand_name'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#FDF6EC] to-[#ede7de] flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-[#1a1a1a] rounded-2xl shadow-xl p-8 border border-[#2e323b]">
            {{-- Logo --}}
            <div class="flex items-center justify-center mb-6">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 bg-[#F5A623] rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-xl">{{ strtoupper(substr(setting('brand_name', 'NITIP DI END'), 0, 1)) }}</span>
                    </div>
                    <span class="font-bold text-lg text-[#FDF6EC]">{{ setting('brand_name', 'NITIP DI END') }}</span>
                </div>
            </div>

            <h1 class="text-2xl font-semibold text-center text-[#FDF6EC] mb-1">Halo Admin 👋</h1>
            <p class="text-sm text-[#b0b4bd] text-center mb-6">Masuk ke dashboard {{ setting('brand_name', 'NITIP DI END') }}</p>

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="space-y-5">
                    {{-- Username --}}
                    <div>
                        @error('username')
                            <p class="text-xs text-[#fca5a5] mb-1">{{ $message }}</p>
                        @enderror
                        <label class="flex items-center gap-2 text-xs font-medium text-[#b0b4bd] mb-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Username
                        </label>
                        <input type="text" name="username" value="{{ old('username') }}" required autofocus
                               placeholder="admin atau 081234567890"
                               class="w-full px-4 py-2.5 bg-[#23252b] border border-[#343a44] rounded-lg text-[#FDF6EC] placeholder-[#9ca3af]/60 focus:outline-none focus:ring-2 focus:ring-[#F5A623]/40 focus:border-[#F5A623]"/>
                    </div>

                    {{-- Password --}}
                    <div>
                        @error('password')
                            <p class="text-xs text-[#fca5a5] mb-1">{{ $message }}</p>
                        @enderror
                        <label class="flex items-center gap-2 text-xs font-medium text-[#b0b4bd] mb-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   placeholder="Masukkan password"
                                   class="w-full px-4 py-2.5 bg-[#23252b] border border-[#343a44] rounded-lg text-[#FDF6EC] placeholder-[#9ca3af]/60 pr-10 focus:outline-none focus:ring-2 focus:ring-[#F5A623]/40 focus:border-[#F5A623]"/>
                            <button type="button" onclick="togglePassword()"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#9ca3af]/60 hover:text-[#F5A623]">
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="w-full mt-6 px-6 py-2.5 bg-[#F5A623] hover:bg-[#e6951b] text-[#1a1a1a] font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                    Masuk
                </button>
            </form>
        </div>

        {{-- Footer badge --}}
        <div class="mt-6 flex justify-center">
            <span class="inline-flex items-center px-4 py-2 rounded-xl bg-[#F5A623]/15 text-[#F5A623] text-xs font-medium">
                {{ setting('brand_name', 'NITIP DI END') }} · v{{ config('app.version', '1.0') }}
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
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.879 10.996a4 4 0 01-4.866-4.866 4 4 0 016.76-6.76M7.892 10.996A4 4 0 0112.76 4.23 4 4 0 0117.628 10.99c0 2.307-.54 4.497-1.48 6.416M18.27 9.5a4 4 0 01-4.866-4.866 4 4 0 016.76-6.76"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}
</script>
@endsection
