@extends('layouts.admin')

@section('title', 'Laporan Pelanggan | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Laporan Pelanggan</h1>
        <p class="text-sm text-[#94A3B8] dark:text-[#cbd5e1] mt-1">Daftar pelanggan beserta jumlah pesanan</p>
    </div>

    <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Telepon</th>
                        <th class="px-4 py-3">Pesanan</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                    @forelse($users as $user)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-4 py-3 font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-[#64748B] dark:text-[#cbd5e1]">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-[#64748B] dark:text-[#cbd5e1]">{{ $user->profile->phone ?? '-' }}</td>
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->orders_count }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-[#fb923c] hover:underline text-sm">Lihat</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-[#94A3B8]">Belum ada pelanggan.</td>
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