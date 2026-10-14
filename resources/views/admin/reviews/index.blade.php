@extends('layouts.admin')

@section('title', 'Moderasi Ulasan')

@section('content')
<div class="space-y-6">

    <x-page-header title="Moderasi Ulasan" subtitle="Kelola ulasan produk dari pelanggan" class="animate-fade-up" />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-up nb-rainbow" style="animation-delay: 80ms">
        <x-stat-card label="Total Ulasan" :value="$stats['total']" icon="clipboard" />
        <x-stat-card label="Pending" :value="$stats['pending']" icon="clock" />
        <x-stat-card label="Disetujui" :value="$stats['approved']" icon="check-circle" />
        <x-stat-card label="Ditolak" :value="$stats['rejected']" icon="x" />
    </div>

    <div class="border-b border-[#E2E8F0] dark:border-[#404854] animate-fade-up" style="animation-delay: 140ms">
        <nav class="flex gap-1 sm:gap-4 overflow-x-auto" aria-label="Filter status ulasan">
            <a href="{{ route('admin.reviews.index') }}"
               class="px-3 sm:px-4 py-2 font-medium text-sm border-b-2 whitespace-nowrap transition-colors {{ !isset($status) ? 'border-[#06B6D4] text-[#0891B2] dark:text-[#22D3EE]' : 'border-transparent text-[#64748B] dark:text-[#cbd5e1] hover:text-[#1E293B] dark:hover:text-[#f1f5f9]' }}"
               @if(!isset($status)) aria-current="page" @endif>
                Semua ({{ $stats['total'] }})
            </a>
            <a href="{{ route('admin.reviews.index') }}?status=pending"
               class="px-3 sm:px-4 py-2 font-medium text-sm border-b-2 whitespace-nowrap transition-colors {{ isset($status) && $status === 'pending' ? 'border-[#06B6D4] text-[#0891B2] dark:text-[#22D3EE]' : 'border-transparent text-[#64748B] dark:text-[#cbd5e1] hover:text-[#1E293B] dark:hover:text-[#f1f5f9]' }}"
               @if(isset($status) && $status === 'pending') aria-current="page" @endif>
                Pending ({{ $stats['pending'] }})
            </a>
            <a href="{{ route('admin.reviews.index') }}?status=approved"
               class="px-3 sm:px-4 py-2 font-medium text-sm border-b-2 whitespace-nowrap transition-colors {{ isset($status) && $status === 'approved' ? 'border-[#06B6D4] text-[#0891B2] dark:text-[#22D3EE]' : 'border-transparent text-[#64748B] dark:text-[#cbd5e1] hover:text-[#1E293B] dark:hover:text-[#f1f5f9]' }}"
               @if(isset($status) && $status === 'approved') aria-current="page" @endif>
                Disetujui ({{ $stats['approved'] }})
            </a>
            <a href="{{ route('admin.reviews.index') }}?status=rejected"
               class="px-3 sm:px-4 py-2 font-medium text-sm border-b-2 whitespace-nowrap transition-colors {{ isset($status) && $status === 'rejected' ? 'border-[#06B6D4] text-[#0891B2] dark:text-[#22D3EE]' : 'border-transparent text-[#64748B] dark:text-[#cbd5e1] hover:text-[#1E293B] dark:hover:text-[#f1f5f9]' }}"
               @if(isset($status) && $status === 'rejected') aria-current="page" @endif>
                Ditolak ({{ $stats['rejected'] }})
            </a>
        </nav>
    </div>

    <x-card padded="false" class="animate-fade-up" style="animation-delay: 200ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px]">
                <thead class="bg-[#F1F5F9] dark:bg-[#2e323b] border-b border-[#E2E8F0] dark:border-[#404854]">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">Produk</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">Pembuat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">Rating</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">Judul</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854] stagger">
                    @forelse($reviews as $review)
                    <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $review->product->name }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-[#64748B] dark:text-[#cbd5e1]">{{ $review->user->name }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex text-yellow-500 dark:text-yellow-400" role="img" aria-label="Rating {{ $review->rating }} dari 5">
                                @for($i = 0; $i < $review->rating; $i++)
                                    <x-icon name="star" class="w-4 h-4" />
                                @endfor
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-[#64748B] dark:text-[#cbd5e1] max-w-xs truncate">{{ $review->title }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($review->status === 'pending')
                                <x-badge tone="warning">Pending</x-badge>
                            @elseif($review->status === 'approved')
                                <x-badge tone="success">Disetujui</x-badge>
                            @else
                                <x-badge tone="danger">Ditolak</x-badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.reviews.show', $review) }}"
                               class="inline-flex items-center gap-1.5 text-[#0891B2] dark:text-[#22D3EE] font-medium hover:underline rounded focus:outline-none focus:ring-2 focus:ring-orange-300">
                                <x-icon name="eye" class="w-4 h-4" /> Lihat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <x-empty-state icon="star" title="Tidak ada ulasan">
                                @isset($status)
                                    Tidak ada ulasan dengan status ini.
                                @else
                                    Ulasan baru dari pelanggan akan tampil di sini.
                                @endisset
                            </x-empty-state>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    @if($reviews->hasPages())
        <div>{{ $reviews->links() }}</div>
    @endif

</div>
@endsection