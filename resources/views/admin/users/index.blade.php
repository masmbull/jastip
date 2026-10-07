@extends('layouts.admin')

@section('title', 'Pelanggan | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Daftar Pelanggan</h1>
        <p class="text-sm text-[#94A3B8] dark:text-[#cbd5e1] mt-1">Kelola akun pelanggan</p>
    </div>

    <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Telepon</th>
                        <th class="px-4 py-3">Tier</th>
                        <th class="px-4 py-3">Poin</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                    @forelse($users as $user)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->name }}</p>
                                <p class="text-xs font-mono text-[#94A3B8] dark:text-[#64748B]">{{ $user->username }}</p>
                            </td>
                            <td class="px-4 py-3 text-[#64748B] dark:text-[#cbd5e1]">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-[#64748B] dark:text-[#cbd5e1]">{{ $user->profile->phone ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-400 capitalize">
                                    {{ $user->profile->membership_tier ?? 'bronze' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->profile->loyalty_points ?? 0 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="text-[#94A3B8] hover:text-orange-600 p-1" title="Lihat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.users.destroy', $user) }}"
                                          data-confirm="Hapus pelanggan ini?"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142a2 2 0 01-1.994 1.827H7.864a2 2 0 01-1.994-1.827L5 7M10 11V6a1 1 0 011-1h2a1 1 0 011 1v5m-4 0v9h6v-9m-6 0L8 21h8l-2-9h-4z"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-[#94A3B8]">Belum ada pelanggan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
        <div>{{ $users->links() }}</div>
    @endif
</div>
@endsection