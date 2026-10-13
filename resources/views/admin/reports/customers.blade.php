@extends('layouts.admin')

@section('title', 'Laporan Pelanggan | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">

    <x-page-header title="Laporan Pelanggan" subtitle="Daftar pelanggan beserta jumlah pesanan" class="animate-fade-up" />

    <x-card padded="false" class="animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px]">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Telepon</th>
                        <th class="px-4 py-3 text-right">Pesanan</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854] stagger">
                    @forelse($users as $user)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-4 py-3 font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-[#64748B] dark:text-[#cbd5e1]">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-[#64748B] dark:text-[#cbd5e1]">{{ $user->profile->phone ?? '-' }}</td>
                            <td class="px-4 py-3 text-right text-[#1E293B] dark:text-[#f1f5f9] num">{{ $user->orders_count }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="inline-flex items-center gap-1.5 text-sm font-medium text-orange-600 dark:text-orange-400 hover:underline rounded focus:outline-none focus:ring-2 focus:ring-orange-300">
                                    <x-icon name="eye" class="w-4 h-4" /> Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
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