@extends('layouts.admin')

@section('title', 'Detail Pelanggan | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">
    <x-page-header title="Detail Pelanggan" subtitle="{{ $user->name }}" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.users.index')" variant="ghost" icon="arrow-left">Kembali</x-btn>
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
              data-confirm="Hapus pelanggan ini?" class="inline">
            @csrf
            @method('DELETE')
            <x-btn type="submit" variant="danger" icon="trash">Hapus</x-btn>
        </form>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-card class="lg:col-span-1 animate-fade-up" style="animation-delay: 80ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4">Profil</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-[#64748B] dark:text-[#cbd5e1]">Nama</dt><dd class="text-right text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->name }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-[#64748B] dark:text-[#cbd5e1]">Username</dt><dd class="text-right font-mono text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->username ?? '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-[#64748B] dark:text-[#cbd5e1]">Email</dt><dd class="text-right text-[#1E293B] dark:text-[#f1f5f9] break-all">{{ $user->email }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-[#64748B] dark:text-[#cbd5e1]">Telepon</dt><dd class="text-right text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->profile->phone ?? '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-[#64748B] dark:text-[#cbd5e1]">Gender</dt><dd class="text-right capitalize text-[#1E293B] dark:text-[#f1f5f9]">{{ $user->profile->gender ?? '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-[#64748B] dark:text-[#cbd5e1]">Tier</dt><dd class="text-right"><x-badge tone="brand" class="capitalize">{{ $user->profile->membership_tier ?? 'bronze' }}</x-badge></dd></div>
                <div class="flex justify-between gap-4"><dt class="text-[#64748B] dark:text-[#cbd5e1]">Poin</dt><dd class="text-right text-[#1E293B] dark:text-[#f1f5f9] num">{{ $user->profile->loyalty_points ?? 0 }}</dd></div>
            </dl>
            <form method="POST" action="{{ route('admin.users.update-role', $user) }}"
                  class="pt-4 mt-4 border-t border-[#E2E8F0] dark:border-[#404854] space-y-2">
                @csrf
                @method('PUT')
                <label for="role" class="block text-xs font-medium text-[#64748B] dark:text-[#cbd5e1]">Role</label>
                <div class="flex gap-2">
                    <select id="role" name="role"
                            class="flex-1 px-3 py-2 text-sm border border-[#E2E8F0] dark:border-[#404854] rounded-lg bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300">
                        @foreach(['customer' => 'Pelanggan', 'admin' => 'Admin'] as $value => $label)
                            <option value="{{ $value }}" @selected($user->role === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-btn type="submit" variant="brand">Simpan</x-btn>
                </div>
            </form>
        </x-card>

        <x-card class="lg:col-span-2 animate-fade-up" style="animation-delay: 140ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4">Pesanan ({{ $user->orders->count() }})</h2>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[520px] text-sm">
                    <thead>
                        <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                            <th class="px-3 py-2">No.</th>
                            <th class="px-3 py-2">Tanggal</th>
                            <th class="px-3 py-2 text-right">Total</th>
                            <th class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                        @forelse($user->orders->sortByDesc('created_at') as $order)
                            <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                                <td class="px-3 py-2 font-mono text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->order_number }}</td>
                                <td class="px-3 py-2 text-[#64748B] dark:text-[#cbd5e1]">{{ $order->created_at->format('d M Y') }}</td>
                                <td class="px-3 py-2 text-right text-[#1E293B] dark:text-[#f1f5f9] num">{{ $order->formatted_total }}</td>
                                <td class="px-3 py-2">
                                    @php($tones = ['awaiting_payment' => 'warning', 'pending' => 'warning', 'confirmed' => 'info', 'processing' => 'info', 'ready' => 'neutral', 'shipped' => 'info', 'completed' => 'success', 'cancelled' => 'danger'])
                                    <x-badge :tone="$tones[$order->status] ?? 'neutral'">{{ $order->status_label }}</x-badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-empty-state icon="bag" title="Belum ada pesanan." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</div>
@endsection