@extends('layouts.admin')

@section('title', 'Detail Pelanggan | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Detail Pelanggan</h1>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-orange-600 hover:underline">Kembali</a>
        </div>
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
              data-confirm="Hapus pelanggan ini?" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 text-sm bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg">Hapus</button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] p-6 space-y-3">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Profil</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-[#94A3B8]">Nama</dt><dd class="text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-[#94A3B8]">Username</dt><dd class="font-mono text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->username ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-[#94A3B8]">Email</dt><dd class="text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->email }}</dd></div>
                <div class="flex justify-between"><dt class="text-[#94A3B8]">Telepon</dt><dd class="text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->profile->phone ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-[#94A3B8]">Gender</dt><dd class="text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->profile->gender ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-[#94A3B8]">Tier</dt><dd class="capitalize text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->profile->membership_tier ?? 'bronze' }}</dd></div>
                <div class="flex justify-between"><dt class="text-[#94A3B8]">Poin</dt><dd class="text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->profile->loyalty_points ?? 0 }}</dd></div>
            </dl>
        </div>

        <div class="lg:col-span-2 bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] p-6 space-y-4">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Pesanan ({{ $user->orders->count() }})</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                            <th class="px-3 py-2">No.</th>
                            <th class="px-3 py-2">Tanggal</th>
                            <th class="px-3 py-2">Total</th>
                            <th class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                        @forelse($user->orders->sortByDesc('created_at') as $order)
                            <tr>
                                <td class="px-3 py-2 font-mono text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->order_number }}</td>
                                <td class="px-3 py-2 text-[#64748B] dark:text-[#cbd5e1]">{{ $order->created_at->format('d M Y') }}</td>
                                <td class="px-3 py-2 text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->formatted_total }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-6 text-center text-[#94A3B8]">Belum ada pesanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection