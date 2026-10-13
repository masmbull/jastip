@extends('layouts.admin')

@section('title', 'Pelanggan | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">

    <x-page-header title="Daftar Pelanggan" subtitle="Kelola akun pelanggan" class="animate-fade-up" />

    <x-card padded="false" class="animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px]">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Telepon</th>
                        <th class="px-4 py-3">Tier</th>
                        <th class="px-4 py-3 text-right">Poin</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854] stagger">
                    @forelse($users as $user)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->name }}</p>
                                <p class="text-xs font-mono text-[#94A3B8] dark:text-[#64748B]">{{ $user->username }}</p>
                            </td>
                            <td class="px-4 py-3 text-[#64748B] dark:text-[#cbd5e1]">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-[#64748B] dark:text-[#cbd5e1]">{{ $user->profile->phone ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <x-badge tone="brand" class="capitalize">{{ $user->profile->membership_tier ?? 'bronze' }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-right text-[#1E293B] dark:text-[#f1f5f9] num">{{ $user->profile->loyalty_points ?? 0 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 p-1.5 rounded-lg hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300"
                                       title="Lihat" aria-label="Lihat pelanggan {{ $user->name }}">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.users.destroy', $user) }}"
                                          data-confirm="Hapus pelanggan ini?"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-[#64748B] dark:text-[#cbd5e1] hover:text-red-600 dark:hover:text-red-400 p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300"
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
                                <x-empty-state icon="user" title="Belum ada pelanggan." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    @if($users->hasPages())
        <div>{{ $users->links() }}</div>
    @endif

</div>
@endsection