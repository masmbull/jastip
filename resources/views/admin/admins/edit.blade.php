@extends('layouts.admin')

@section('title', 'Edit Admin | ' . setting('brand_name'))

@section('content')
<div class="space-y-6 max-w-2xl">
    <x-page-header title="Edit Admin" subtitle="{{ $user->name }}{{ auth()->id() === $user->id ? ' (akun Anda)' : '' }}" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.admins.index')" variant="ghost" icon="arrow-left">Kembali</x-btn>
    </x-page-header>

    @if($errors->any())
        <div id="admin-edit-errors" role="alert" tabindex="-1"
             class="rounded-[8px] border-2 border-red-800 bg-red-50 px-4 py-3 text-sm text-red-900 dark:bg-red-950 dark:text-red-100">
            <p class="font-bold">Perubahan belum disimpan. Periksa pesan berikut:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-card class="animate-fade-up" style="animation-delay: 80ms">
        <x-admin-section-title icon="user" title="Data Admin" />
        <form method="POST" action="{{ route('admin.admins.update', $user) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="edit-name" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Nama Lengkap</label>
                <input id="edit-name" type="text" name="name" required maxlength="255"
                       value="{{ old('name', $user->name) }}"
                       class="nb-input w-full">
            </div>
            <div>
                <label for="edit-email" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Email</label>
                <input id="edit-email" type="email" name="email" required maxlength="255"
                       value="{{ old('email', $user->email) }}"
                       class="nb-input w-full">
            </div>
            <div>
                <label for="edit-username" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Username</label>
                <input id="edit-username" type="text" name="username" maxlength="100"
                       value="{{ old('username', $user->username) }}"
                       class="nb-input w-full">
            </div>

            <div class="flex justify-end">
                <x-btn type="submit" variant="brand" icon="check">Simpan Data</x-btn>
            </div>
        </form>
    </x-card>

    <x-card class="animate-fade-up" style="animation-delay: 140ms">
        <x-admin-section-title icon="lock" title="Ganti Kata Sandi" class="mb-1" />
        <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mb-4">
            {{ auth()->id() === $user->id
                ? 'Mengganti milik Anda sendiri: wajib memasukkan kata sandi lama.'
                : 'Reset oleh admin: tidak perlu kata sandi lama. Akun ' . $user->name . ' langsung memakai kata sandi baru ini.' }}
        </p>
        <form method="POST" action="{{ route('admin.admins.password', $user) }}" class="space-y-4" autocomplete="off"
              data-confirm="Ganti kata sandi {{ auth()->id() === $user->id ? 'Anda' : $user->name }}?">
            @csrf
            @method('PUT')

            @if(auth()->id() === $user->id)
                <div>
                    <label for="current-password" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Kata Sandi Lama</label>
                    <input id="current-password" type="password" name="current_password" required autocomplete="current-password"
                           class="nb-input w-full">
                </div>
            @endif
            <div>
                <label for="new-password" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Kata Sandi Baru</label>
                <input id="new-password" type="password" name="password" required minlength="12" autocomplete="new-password"
                       class="nb-input w-full">
                <p class="mt-1 text-xs font-medium text-[#475569] dark:text-[#94A3B8]">Minimal 12 karakter.</p>
            </div>
            <div>
                <label for="new-password-confirm" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Ulangi Kata Sandi Baru</label>
                <input id="new-password-confirm" type="password" name="password_confirmation" required minlength="12" autocomplete="new-password"
                       class="nb-input w-full">
            </div>

            <div class="flex justify-end">
                <x-btn type="submit" variant="brand" icon="lock">Ganti Kata Sandi</x-btn>
            </div>
        </form>
    </x-card>
</div>
@endsection
