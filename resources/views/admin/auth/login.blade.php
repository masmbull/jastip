@extends('layouts.admin')

@section('title', 'Masuk | ' . setting('brand_name'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#FAF7F2] to-[#F0EDE7] flex items-center justify-center p-8">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-[#E8E0D8]">
            <div class="flex items-center justify-center mb-6">
                <div class="flex items-center space-x-2">
                    <div class="h-10 w-10 bg-rose-500 rounded-full flex items-center justify-center">
                        <span class="text-lg text-white font-bold">{{ strtoupper(substr(setting('brand_name', 'NITIP DI END'), 0, 1)) }}</span>
                    </div>
                    <span class="font-bold text-lg text-[#333333]">{{ setting('brand_name', 'NITIP DI END') }}</span>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-[#333333] text-center mb-2">Halo Admin 👋</h1>
            <p class="text-sm text-[#999999] text-center mb-6">Masuk ke dashboard {{ setting('brand_name', 'NITIP DI END') }}</p>

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-[#333333] mb-2">
                            <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Email
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@nitipdiend.com"
                               class="w-full px-4 py-3 border border-[#E8E0D8] rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-300 @error('email') border-red-500 @enderror">
                        @error('email')<p class="text-sm text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#333333] mb-2">
                            <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required placeholder="Masukkan password"
                                   class="w-full px-4 py-3 border border-[#E8E0D8] rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-300 @error('password') border-red-500 @enderror pr-10">
                            <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#999999] hover:text-[#333333]">
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')<p class="text-sm text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <button type="submit" class="w-full mt-6 px-6 py-3 bg-rose-500 hover:bg-rose-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    Masuk
                </button>
            </form>

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