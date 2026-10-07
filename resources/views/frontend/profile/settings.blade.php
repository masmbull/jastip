@extends('layouts.app')

@section('title', 'Pengaturan Akun | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#FDF6EC] to-[#ede7de] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-8">Pengaturan Akun</h1>

        {{-- Change Password --}}
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-8 border border-gray-200 dark:border-[#404854] mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-6">Ubah Password</h2>

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Password Saat Ini *</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#fb923c]">
                    @error('current_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Password Baru *</label>
                    <input type="password" name="password" required
                           placeholder="Min. 8 karakter"
                           class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#fb923c]">
                    <p class="text-xs text-gray-500 dark:text-[#9ca3af] mt-1">Minimal 8 karakter</p>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Konfirmasi Password Baru *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#fb923c]">
                    @error('password_confirmation')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-[#404854]">
                    <a href="{{ route('profile.show') }}" class="flex-1 px-4 py-2 border border-gray-300 dark:border-[#404854] text-gray-700 dark:text-[#cbd5e1] rounded-lg hover:bg-gray-100 dark:hover:bg-[#404854] transition text-center">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 px-4 py-2 bg-[#fb923c] text-white rounded-lg hover:bg-[#e6951b] transition font-medium">
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>

        {{-- Account Settings --}}
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-8 border border-gray-200 dark:border-[#404854]">
            <h2 class="text-xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-6">Pengaturan Akun</h2>

            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-[#404854] rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-[#f1f5f9]">Email Notification</p>
                        <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">Terima notifikasi pesanan via email</p>
                    </div>
                    <input type="checkbox" class="w-5 h-5" checked disabled>
                </div>

                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-[#404854] rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-[#f1f5f9]">WhatsApp Notification</p>
                        <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">Terima notifikasi pesanan via WhatsApp</p>
                    </div>
                    <input type="checkbox" class="w-5 h-5" checked disabled>
                </div>

                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-[#404854] rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-[#f1f5f9]">Marketing Email</p>
                        <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">Terima promosi dan penawaran terbaru</p>
                    </div>
                    <input type="checkbox" class="w-5 h-5">
                </div>
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="mt-8 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800 p-8">
            <h2 class="text-xl font-bold text-red-900 dark:text-red-200 mb-4">Zona Berbahaya</h2>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 border border-red-200 dark:border-red-800 rounded-lg">
                    <div>
                        <p class="font-medium text-red-900 dark:text-red-200">Hapus Akun</p>
                        <p class="text-sm text-red-700 dark:text-red-300">Menghapus akun dan semua data Anda secara permanen</p>
                    </div>
                    <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition" onclick="alert('Fitur ini sedang dikembangkan')">
                        Hapus Akun
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
