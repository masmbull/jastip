@extends('layouts.admin')

@section('title', 'Kupon | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">
    <x-page-header title="Daftar Kupon" subtitle="Kelola kode diskon dan promo" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.coupons.create')" icon="plus">Tambah Kupon</x-btn>
    </x-page-header>

    <div class="nb-table-shell animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px]">
                <thead>
                    <tr class="nb-table-head text-left text-xs uppercase">
                        <th scope="col" class="px-4 py-3 font-black">Kode</th>
                        <th scope="col" class="px-4 py-3 font-black">Diskon</th>
                        <th scope="col" class="px-4 py-3 font-black">Periode</th>
                        <th scope="col" class="px-4 py-3 font-black">Kuota</th>
                        <th scope="col" class="px-4 py-3 font-black">Status</th>
                        <th scope="col" class="px-4 py-3 font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#141414]/15 dark:divide-[#FAF6EF]/15">
                    @forelse($coupons as $coupon)
                        <tr class="bg-white transition-colors hover:bg-[#FFFDF0] dark:bg-[#1E1E1E] dark:hover:bg-[#262626]">
                            <td class="px-4 py-3">
                                <p class="font-mono font-extrabold text-[#141414] dark:text-[#FAF6EF]">{{ $coupon->code }}</p>
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
                                       class="text-[#64748B] dark:text-[#cbd5e1] hover:text-[#141414] dark:hover:text-[#FFC800] p-1.5 rounded-lg hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] transition-colors" title="Ubah" aria-label="Ubah">
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
                                    <a href="{{ route('admin.coupons.create') }}" class="text-[#141414] dark:text-[#FFC800] font-medium hover:underline">Tambah kupon pertama</a>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($coupons->hasPages())
        <div>{{ $coupons->links() }}</div>
    @endif
</div>
@endsection