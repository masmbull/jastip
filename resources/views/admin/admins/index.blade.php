@extends('layouts.admin')

@section('title', 'Kelola Admin | ' . setting('brand_name'))

@section('content')
<div class="space-y-6" x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }"
     @keydown.escape.window="open = false"
     x-effect="open && $nextTick(() => $refs.nameInput && $refs.nameInput.focus())">

    <x-page-header title="Kelola Admin" subtitle="Akun admin yang bisa masuk ke panel ini. Bukan pelanggan." class="animate-fade-up">
        <button type="button" @click="open = true"
                class="nb-btn inline-flex items-center gap-1.5 px-4 py-2.5 bg-[#FFC800] text-[#141414] text-sm"
                aria-haspopup="dialog" aria-label="Tambah admin baru">
            <x-icon name="plus" class="w-4 h-4" /> Tambah Admin
        </button>
    </x-page-header>

    <div class="nb-table-shell">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px]">
                <thead>
                    <tr class="nb-table-head text-left text-xs uppercase">
                        <th scope="col" class="px-4 py-3 font-black">Admin</th>
                        <th scope="col" class="px-4 py-3 font-black">Email</th>
                        <th scope="col" class="px-4 py-3 font-black">Username</th>
                        <th scope="col" class="px-4 py-3 font-black">Ditambahkan</th>
                        <th scope="col" class="px-4 py-3 font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#141414]/15 dark:divide-[#FAF6EF]/15">
                    @forelse($admins as $admin)
                        <tr class="bg-white dark:bg-[#1E1E1E] hover:bg-[#FFFDF0] dark:hover:bg-[#262626] transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-extrabold text-[#141414] dark:text-[#FAF6EF]">{{ $admin->name }}</p>
                                @if(auth()->id() === $admin->id)
                                    <span class="text-[11px] font-black uppercase tracking-wide text-[#475569] dark:text-[#CBD5E1]">(Anda)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-[#334155] dark:text-[#CBD5E1] break-all">{{ $admin->email }}</td>
                            <td class="px-4 py-3 text-sm font-mono text-[#334155] dark:text-[#CBD5E1]">{{ $admin->username ?: '-' }}</td>
                            <td class="px-4 py-3 text-xs font-bold text-[#334155] dark:text-[#CBD5E1] num">{{ $admin->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.admins.edit', $admin) }}"
                                       class="inline-flex items-center justify-center w-11 h-11 border-2 border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF] rounded-[8px] text-[#475569] dark:text-[#CBD5E1] transition-colors focus:outline-none focus:ring-2 focus:ring-[#EAB308]"
                                       title="Edit / ganti kata sandi" aria-label="Edit admin {{ $admin->name }}">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </a>
                                    @if(auth()->id() !== $admin->id)
                                        <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}"
                                              data-confirm="Hapus admin {{ $admin->name }}? Mereka langsung kehilangan akses panel." class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-11 h-11 border-2 border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF] rounded-[8px] text-[#B91C1C] transition-colors focus:outline-none focus:ring-2 focus:ring-[#EAB308]"
                                                    title="Hapus admin" aria-label="Hapus admin {{ $admin->name }}">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state icon="user-circle" title="Belum ada admin.">
                                    Klik <span class="font-semibold">Tambah Admin</span> untuk membuat akun admin pertama.
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($admins->hasPages())
        <div>{{ $admins->links() }}</div>
    @endif

    <p class="text-xs font-medium text-[#475569] dark:text-[#CBD5E1]">
        Anda tidak bisa menghapus akun Anda sendiri. Admin terakhir tidak bisa dihapus — buat admin lain dulu.
    </p>

    {{-- Modal: Tambah Admin --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-[420] flex items-start justify-center overflow-y-auto p-4 sm:p-6"
         role="dialog" aria-modal="true" aria-labelledby="add-admin-title" @keydown.escape.window="open = false">
        <div class="fixed inset-0 bg-black/50" @click="open = false"></div>

        <div class="relative w-full max-w-lg my-4 sm:my-8 bg-[#FAF6EF] dark:bg-[#1E1E1E] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] shadow-[6px_6px_0_#141414] dark:shadow-[6px_6px_0_#FAF6EF]"
             @click.stop>

            <div class="flex items-center justify-between px-5 py-4 border-b-2 border-[#141414] dark:border-[#FAF6EF]">
                <h3 id="add-admin-title" class="text-lg font-black text-[#141414] dark:text-[#FAF6EF]">Tambah Admin Baru</h3>
                <button type="button" @click="open = false" aria-label="Tutup" title="Tutup"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-[8px] text-[#141414] dark:text-[#FAF6EF] hover:bg-black/5 dark:hover:bg-white/10 transition-colors focus:outline-none focus:ring-2 focus:ring-[#EAB308]">
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

            <form method="POST" action="{{ route('admin.admins.store') }}" class="px-5 py-4 space-y-4">
                @csrf

                <div>
                    <label for="admin-name" class="block text-sm font-bold text-[#141414] dark:text-[#FAF6EF] mb-1.5">Nama Lengkap <span class="text-[#B91C1C]">*</span></label>
                    <input id="admin-name" name="name" type="text" x-ref="nameInput" required maxlength="255"
                           value="{{ old('name') }}" placeholder="Nama admin" autocomplete="name"
                           class="nb-input w-full px-3 py-2.5 bg-white dark:bg-[#121212] text-[#141414] dark:text-[#FAF6EF]">
                </div>

                <div>
                    <label for="admin-email" class="block text-sm font-bold text-[#141414] dark:text-[#FAF6EF] mb-1.5">Email <span class="text-[#B91C1C]">*</span></label>
                    <input id="admin-email" name="email" type="email" required maxlength="255"
                           value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email"
                           class="nb-input w-full px-3 py-2.5 bg-white dark:bg-[#121212] text-[#141414] dark:text-[#FAF6EF]">
                </div>

                <div>
                    <label for="admin-username" class="block text-sm font-bold text-[#141414] dark:text-[#FAF6EF] mb-1.5">Username <span class="font-medium text-[#475569] dark:text-[#94A3B8]">(opsional)</span></label>
                    <input id="admin-username" name="username" type="text" maxlength="100"
                           value="{{ old('username') }}" placeholder="mis. admin2" autocomplete="username"
                           class="nb-input w-full px-3 py-2.5 bg-white dark:bg-[#121212] text-[#141414] dark:text-[#FAF6EF]">
                </div>

                <div>
                    <label for="admin-password" class="block text-sm font-bold text-[#141414] dark:text-[#FAF6EF] mb-1.5">Kata Sandi <span class="text-[#B91C1C]">*</span></label>
                    <input id="admin-password" name="password" type="password" required minlength="12" autocomplete="new-password"
                           class="nb-input w-full px-3 py-2.5 bg-white dark:bg-[#121212] text-[#141414] dark:text-[#FAF6EF]">
                    <p class="mt-1 text-xs font-medium text-[#475569] dark:text-[#94A3B8]">Minimal 12 karakter. Serahkan ke admin baru lewat kanal pribadi, jangan lewat grup.</p>
                </div>

                <div>
                    <label for="admin-password-confirm" class="block text-sm font-bold text-[#141414] dark:text-[#FAF6EF] mb-1.5">Ulangi Kata Sandi <span class="text-[#B91C1C]">*</span></label>
                    <input id="admin-password-confirm" name="password_confirmation" type="password" required minlength="12" autocomplete="new-password"
                           class="nb-input w-full px-3 py-2.5 bg-white dark:bg-[#121212] text-[#141414] dark:text-[#FAF6EF]">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t-2 border-[#141414]/15 dark:border-[#FAF6EF]/15">
                    <button type="button" @click="open = false"
                            class="nb-btn px-4 py-2.5 bg-white dark:bg-[#121212] text-[#141414] dark:text-[#FAF6EF] text-sm">
                        Batal
                    </button>
                    <button type="submit"
                            class="nb-btn px-4 py-2.5 bg-[#FFC800] text-[#141414] text-sm">
                        Simpan Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
