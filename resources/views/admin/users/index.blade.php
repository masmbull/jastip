@extends('layouts.admin')

@section('title', 'Pelanggan | ' . setting('brand_name'))

@section('content')
@php
    $tierTones = ['bronze' => 'warning', 'silver' => 'neutral', 'gold' => 'brand', 'platinum' => 'info'];
@endphp
<div class="space-y-6" x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }"
     @keydown.escape.window="open = false"
     x-effect="open && $nextTick(() => $refs.nameInput && $refs.nameInput.focus())">

    <x-page-header title="Daftar Pelanggan" subtitle="Kelola akun pelanggan" class="animate-fade-up">
        <button type="button" @click="open = true"
                class="nb-btn inline-flex items-center gap-1.5 px-4 py-2.5 bg-[#0E7490] text-white text-sm"
                aria-haspopup="dialog" aria-label="Tambah pelanggan baru">
            <x-icon name="plus" class="w-4 h-4" /> Tambah Pelanggan
        </button>
    </x-page-header>

    <div class="nb-table-shell">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px]">
                <thead>
                    <tr class="nb-table-head text-left text-xs uppercase">
                        <th scope="col" class="px-4 py-3 font-black">Pelanggan</th>
                        <th scope="col" class="px-4 py-3 font-black">Email</th>
                        <th scope="col" class="px-4 py-3 font-black">Telepon</th>
                        <th scope="col" class="px-4 py-3 font-black">Tier</th>
                        <th scope="col" class="px-4 py-3 text-right font-black">Poin</th>
                        <th scope="col" class="px-4 py-3 font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#141414]/15 dark:divide-[#FAF6EF]/15">
                    @forelse($users as $user)
                        <tr class="bg-white dark:bg-[#1E1E1E] hover:bg-[#FFFDF0] dark:hover:bg-[#262626] transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-extrabold text-[#141414] dark:text-[#FAF6EF]">{{ $user->name }}</p>
                                @if($user->username)
                                    <p class="text-xs font-mono text-[#475569] dark:text-[#94A3B8]">{{ $user->username }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-[#334155] dark:text-[#CBD5E1] break-all">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-[#334155] dark:text-[#CBD5E1] num">{{ $user->profile->phone ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <x-badge :tone="$tierTones[$user->profile->membership_tier ?? 'bronze'] ?? 'neutral'" class="capitalize">
                                    {{ $user->profile->membership_tier ?? 'bronze' }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-[#141414] dark:text-[#FAF6EF] num">{{ $user->profile->loyalty_points ?? 0 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="inline-flex items-center justify-center w-11 h-11 border-2 border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF] rounded-[8px] text-[#475569] dark:text-[#CBD5E1] transition-colors focus:outline-none focus:ring-2 focus:ring-[#0E7490]"
                                       title="Lihat" aria-label="Lihat pelanggan {{ $user->name }}">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          data-confirm="Hapus pelanggan {{ $user->name }}?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-11 h-11 border-2 border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF] rounded-[8px] text-[#B91C1C] transition-colors focus:outline-none focus:ring-2 focus:ring-[#0E7490]"
                                                title="Hapus" aria-label="Hapus pelanggan {{ $user->name }}">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="user" title="Belum ada pelanggan.">
                                    Klik <span class="font-semibold">Tambah Pelanggan</span> untuk menambah data pelanggan pertama.
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
        <div>{{ $users->links() }}</div>
    @endif

    {{-- Modal: Tambah Pelanggan --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-[420] flex items-start justify-center overflow-y-auto p-4 sm:p-6"
         role="dialog" aria-modal="true" aria-labelledby="add-customer-title" @keydown.escape.window="open = false">
        <div class="fixed inset-0 bg-black/50" @click="open = false"></div>

        <div class="relative w-full max-w-lg my-4 sm:my-8 bg-[#FAF6EF] dark:bg-[#1E1E1E] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] shadow-[6px_6px_0_#141414] dark:shadow-[6px_6px_0_#FAF6EF]"
             @click.stop>

            <div class="flex items-center justify-between px-5 py-4 border-b-2 border-[#141414] dark:border-[#FAF6EF]">
                <h3 id="add-customer-title" class="text-lg font-black text-[#141414] dark:text-[#FAF6EF]">Tambah Pelanggan Baru</h3>
                <button type="button" @click="open = false" aria-label="Tutup" title="Tutup"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-[8px] text-[#141414] dark:text-[#FAF6EF] hover:bg-black/5 dark:hover:bg-white/10 transition-colors focus:outline-none focus:ring-2 focus:ring-[#0E7490]">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            @if($errors->any())
                <div class="mx-5 mt-4 border-2 border-[#B91C1C] bg-[#FEE2E2] text-[#7F1D1D] text-sm font-medium rounded-[8px] p-3">
                    <p class="font-black mb-1">Periksa kembali isian berikut:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}" class="px-5 py-4 space-y-4">
                @csrf

                <div>
                    <label for="customer-name" class="block text-sm font-bold text-[#141414] dark:text-[#FAF6EF] mb-1.5">Nama Lengkap <span class="text-[#B91C1C]">*</span></label>
                    <input id="customer-name" name="name" type="text" x-ref="nameInput" required maxlength="255"
                           value="{{ old('name') }}" placeholder="Nama pelanggan" autocomplete="name"
                           class="nb-input w-full px-3 py-2.5 bg-white dark:bg-[#121212] text-[#141414] dark:text-[#FAF6EF]">
                </div>

                <div>
                    <label for="customer-email" class="block text-sm font-bold text-[#141414] dark:text-[#FAF6EF] mb-1.5">Email <span class="text-[#B91C1C]">*</span></label>
                    <input id="customer-email" name="email" type="email" required maxlength="255"
                           value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email"
                           class="nb-input w-full px-3 py-2.5 bg-white dark:bg-[#121212] text-[#141414] dark:text-[#FAF6EF]">
                </div>

                <div>
                    <label for="customer-phone" class="block text-sm font-bold text-[#141414] dark:text-[#FAF6EF] mb-1.5">No. WhatsApp / Telepon</label>
                    <input id="customer-phone" name="phone" type="tel" maxlength="30"
                           value="{{ old('phone') }}" placeholder="08123456789" autocomplete="tel"
                           class="nb-input w-full px-3 py-2.5 bg-white dark:bg-[#121212] text-[#141414] dark:text-[#FAF6EF]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="customer-tier" class="block text-sm font-bold text-[#141414] dark:text-[#FAF6EF] mb-1.5">Tier Keanggotaan</label>
                        <select id="customer-tier" name="tier"
                                class="nb-input w-full px-3 py-2.5 bg-white dark:bg-[#121212] text-[#141414] dark:text-[#FAF6EF]">
                            @foreach(['bronze' => 'Bronze', 'silver' => 'Silver', 'gold' => 'Gold', 'platinum' => 'Platinum'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('tier', 'bronze') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="customer-points" class="block text-sm font-bold text-[#141414] dark:text-[#FAF6EF] mb-1.5">Poin Awal</label>
                        <input id="customer-points" name="points" type="number" min="0" step="1"
                               value="{{ old('points', 0) }}" placeholder="0"
                               class="nb-input w-full px-3 py-2.5 bg-white dark:bg-[#121212] text-[#141414] dark:text-[#FAF6EF] num">
                    </div>
                </div>

                <p class="text-xs font-medium text-[#475569] dark:text-[#94A3B8]">
                    Password akun dibuat otomatis dan ditampilkan sekali setelah disimpan. Pelanggan bisa menggantinya lewat "Lupa Password".
                </p>

                <div class="flex items-center justify-end gap-2 pt-2 border-t-2 border-[#141414]/15 dark:border-[#FAF6EF]/15">
                    <button type="button" @click="open = false"
                            class="nb-btn px-4 py-2.5 bg-white dark:bg-[#121212] text-[#141414] dark:text-[#FAF6EF] text-sm">
                        Batal
                    </button>
                    <button type="submit"
                            class="nb-btn px-4 py-2.5 bg-[#0E7490] text-white text-sm">
                        Simpan Pelanggan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection