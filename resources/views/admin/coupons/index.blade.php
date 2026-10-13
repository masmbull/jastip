@extends('layouts.admin')

@section('title', 'Kupon | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">
    <x-page-header title="Daftar Kupon" subtitle="Kelola kode diskon dan promo" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.coupons.create')" icon="plus">Tambah Kupon</x-btn>
    </x-page-header>

    <x-card padded="false" class="animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px]">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Diskon</th>
                        <th class="px-4 py-3">Periode</th>
                        <th class="px-4 py-3">Kuota</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-mono font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $coupon->code }}</p>
                                @if($coupon->description)
                                    <p class="text-xs text-[#94A3B8] dark:text-[#64748B]">{{ $coupon->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9] num">
                                {{ $coupon->type === 'percentage' ? (float) $coupon->value . '%' : 'Rp ' . number_format((float) $coupon->value, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-[#64748B] dark:text-[#cbd5e1]">
                                {{ $coupon->valid_from?->format('d M Y') }} &ndash; {{ $coupon->valid_to?->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9] num">
                                {{ $coupon->usage_count }}/{{ $coupon->usage_limit ?? '∞' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($coupon->is_active && now()->between($coupon->valid_from, $coupon->valid_to))
                                    <x-badge tone="success">Aktif</x-badge>
                                @else
                                    <x-badge tone="neutral">Nonaktif</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                       class="text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 p-1.5 rounded-lg hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] transition-colors" title="Ubah" aria-label="Ubah">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}"
                                          data-confirm="Hapus kupon ini?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-[#64748B] dark:text-[#cbd5e1] hover:text-red-600 dark:hover:text-red-400 p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors" title="Hapus" aria-label="Hapus">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="ticket" title="Belum ada kupon.">
                                    <a href="{{ route('admin.coupons.create') }}" class="text-orange-600 dark:text-orange-400 font-medium hover:underline">Tambah kupon pertama</a>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    @if($coupons->hasPages())
        <div>{{ $coupons->links() }}</div>
    @endif
</div>
@endsection