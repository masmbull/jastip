@extends('layouts.admin')

@section('title', 'Kupon | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-up">
        <div>
            <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Daftar Kupon</h1>
            <p class="text-sm text-[#94A3B8] dark:text-[#cbd5e1] mt-1">Kelola kode diskon dan promo</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}"
           class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 dark:bg-orange-600 dark:hover:bg-orange-700 text-white font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
            <svg class="w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Kupon
        </a>
    </div>

    <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] overflow-hidden animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full">
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
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9]">
                                {{ $coupon->type === 'percentage' ? (float) $coupon->value . '%' : 'Rp ' . number_format((float) $coupon->value, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-[#64748B] dark:text-[#cbd5e1]">
                                {{ $coupon->valid_from?->format('d M Y') }} &ndash; {{ $coupon->valid_to?->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9]">
                                {{ $coupon->usage_count }}/{{ $coupon->usage_limit ?? '∞' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($coupon->is_active && now()->between($coupon->valid_from, $coupon->valid_to))
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400">Aktif</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-500/20 text-gray-500 dark:text-gray-400">Nonaktif</span>
                                @endif
                            </td>
<td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                       class="text-[#94A3B8] hover:text-orange-600 p-1" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H11a2 2 0 002-2V6a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.coupons.destroy', $coupon) }}"
                                          data-confirm="Hapus kupon ini?"
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
                            <td colspan="6" class="px-4 py-12 text-center">
                                <p class="mb-2 font-medium text-[#94A3B8]">Belum ada kupon.</p>
                                <a href="{{ route('admin.coupons.create') }}"
                                   class="text-sm text-orange-600 font-medium hover:underline">
                                    Tambah kupon pertama
                                </a>
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