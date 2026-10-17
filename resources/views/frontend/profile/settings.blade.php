@extends('layouts.app')

@section('title', 'Pengaturan Akun | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-8">Pengaturan Akun</h1>

        {{-- Change Password --}}
        <x-card class="mb-6">
            <h2 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-6">Ubah Password</h2>

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">Password Saat Ini *</label>
                    <input type="password" id="current_password" name="current_password" required autocomplete="current-password"
                           class="nb-input w-full">
                    @error('current_password')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">Password Baru *</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                           placeholder="Min. 8 karakter"
                           class="nb-input w-full">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Minimal 8 karakter</p>
                    @error('password')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">Konfirmasi Password Baru *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                           class="nb-input w-full">
                    @error('password_confirmation')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col-reverse sm:flex-row gap-3 pt-4 border-t border-[#E2E8F0] dark:border-[#404854]">
                    <x-btn as="a" :href="route('profile.show')" variant="ghost" class="flex-1">Batal</x-btn>
                    <x-btn type="submit" variant="brand" class="flex-1">Ubah Password</x-btn>
                </div>
            </form>
        </x-card>

        {{-- Notification Preferences --}}
        <x-card>
            <h2 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-6">Notifikasi</h2>

            <div class="space-y-4">
                <div class="flex items-center justify-between gap-4 p-4 border border-[#E2E8F0] dark:border-[#404854] rounded-lg">
                    <div>
                        <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">Notifikasi In-App</p>
                        <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Terima pembaruan status pesanan di halaman notifikasi</p>
                    </div>
                    <input type="checkbox" class="w-5 h-5 shrink-0" checked disabled title="Notifikasi in-app selalu aktif" aria-label="Notifikasi in-app selalu aktif">
                </div>

                <form method="POST" action="{{ route('profile.notify-prefs') }}">
                    @csrf
                    @method('PUT')
                    <div class="flex items-center justify-between gap-4 p-4 border border-[#E2E8F0] dark:border-[#404854] rounded-lg">
                        <div>
                            <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">Notifikasi WhatsApp</p>
                            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Terima pembaruan pesanan via WhatsApp</p>
                        </div>
                        <input type="hidden" name="notify_whatsapp" value="0">
                        <input type="checkbox" name="notify_whatsapp" value="1"
                               onchange="this.form.submit()"
                               aria-label="Aktifkan notifikasi WhatsApp"
                               @checked(old('notify_whatsapp', $user->notify_whatsapp))
                               class="w-5 h-5 shrink-0 cursor-pointer">
                    </div>

                    <div class="flex items-center justify-between gap-4 p-4 border border-[#E2E8F0] dark:border-[#404854] rounded-lg mt-4">
                        <div>
                            <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">Notifikasi Email</p>
                            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Terima konfirmasi & status pesanan via email</p>
                        </div>
                        <input type="hidden" name="notify_email" value="0">
                        <input type="checkbox" name="notify_email" value="1"
                               onchange="this.form.submit()"
                               aria-label="Aktifkan notifikasi Email"
                               @checked(old('notify_email', $user->notify_email))
                               class="w-5 h-5 shrink-0 cursor-pointer">
                    </div>

                    <noscript>
                        <button type="submit" class="mt-3 px-4 py-2.5 bg-[#06B6D4] text-[#141414] rounded-lg hover:bg-[#FFC800] transition font-medium text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                            Simpan
                        </button>
                    </noscript>
                </form>
            </div>
        </x-card>

        {{-- Danger Zone --}}
        <div class="mt-8 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-200 dark:border-red-500/30 p-5 sm:p-8">
            <h2 class="text-xl font-bold text-red-900 dark:text-red-200 mb-4">Zona Berbahaya</h2>

            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 border border-red-200 dark:border-red-500/30 rounded-lg">
                    <div>
                        <p class="font-medium text-red-900 dark:text-red-200">Hapus Akun</p>
                        <p class="text-sm text-red-700 dark:text-red-300">Menghapus akun dan semua data Anda secara permanen</p>
                    </div>
                    <button type="button" disabled title="Fitur belum tersedia"
                            class="px-4 py-2.5 bg-red-300 dark:bg-red-500/30 text-white rounded-lg cursor-not-allowed shrink-0">
                        Segera Hadir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection