@extends('layouts.admin')

@section('title', 'Moderasi Ulasan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-[#f1f5f9]">Moderasi Ulasan</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-[#cbd5e1]">Kelola ulasan produk dari pelanggan</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-[#23252b] rounded-lg shadow p-6 border border-gray-200 dark:border-[#404854]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-[#9ca3af] text-sm font-medium">Total Ulasan</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-[#f1f5f9] mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="text-4xl">📝</div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#23252b] rounded-lg shadow p-6 border border-gray-200 dark:border-[#404854]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-[#9ca3af] text-sm font-medium">Pending</p>
                    <p class="text-3xl font-bold text-yellow-500 mt-2">{{ $stats['pending'] }}</p>
                </div>
                <div class="text-4xl">⏳</div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#23252b] rounded-lg shadow p-6 border border-gray-200 dark:border-[#404854]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-[#9ca3af] text-sm font-medium">Disetujui</p>
                    <p class="text-3xl font-bold text-green-500 mt-2">{{ $stats['approved'] }}</p>
                </div>
                <div class="text-4xl">✅</div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#23252b] rounded-lg shadow p-6 border border-gray-200 dark:border-[#404854]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-[#9ca3af] text-sm font-medium">Ditolak</p>
                    <p class="text-3xl font-bold text-red-500 mt-2">{{ $stats['rejected'] }}</p>
                </div>
                <div class="text-4xl">❌</div>
            </div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="mb-6 border-b border-gray-200 dark:border-[#404854]">
        <nav class="flex gap-4" aria-label="Tabs">
            <a href="{{ route('admin.reviews.index') }}" 
               class="px-4 py-2 font-medium text-sm border-b-2 {{ !isset($status) ? 'border-[#fb923c] text-[#fb923c]' : 'border-transparent text-gray-600 dark:text-[#9ca3af] hover:text-gray-900 dark:hover:text-[#f1f5f9]' }}">
                Semua ({{ $stats['total'] }})
            </a>
            <a href="{{ route('admin.reviews.index') }}?status=pending"
               class="px-4 py-2 font-medium text-sm border-b-2 {{ isset($status) && $status === 'pending' ? 'border-[#fb923c] text-[#fb923c]' : 'border-transparent text-gray-600 dark:text-[#9ca3af] hover:text-gray-900 dark:hover:text-[#f1f5f9]' }}">
                Pending ({{ $stats['pending'] }})
            </a>
            <a href="{{ route('admin.reviews.index') }}?status=approved"
               class="px-4 py-2 font-medium text-sm border-b-2 {{ isset($status) && $status === 'approved' ? 'border-[#fb923c] text-[#fb923c]' : 'border-transparent text-gray-600 dark:text-[#9ca3af] hover:text-gray-900 dark:hover:text-[#f1f5f9]' }}">
                Disetujui ({{ $stats['approved'] }})
            </a>
            <a href="{{ route('admin.reviews.index') }}?status=rejected"
               class="px-4 py-2 font-medium text-sm border-b-2 {{ isset($status) && $status === 'rejected' ? 'border-[#fb923c] text-[#fb923c]' : 'border-transparent text-gray-600 dark:text-[#9ca3af] hover:text-gray-900 dark:hover:text-[#f1f5f9]' }}">
                Ditolak ({{ $stats['rejected'] }})
            </a>
        </nav>
    </div>

    {{-- Reviews Table --}}
    <div class="bg-white dark:bg-[#23252b] rounded-lg shadow border border-gray-200 dark:border-[#404854] overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-[#404854] border-b border-gray-200 dark:border-[#404854]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-[#cbd5e1] uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-[#cbd5e1] uppercase tracking-wider">Pembuat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-[#cbd5e1] uppercase tracking-wider">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-[#cbd5e1] uppercase tracking-wider">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-[#cbd5e1] uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-[#cbd5e1] uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-[#404854]">
                @forelse($reviews as $review)
                <tr class="hover:bg-gray-50 dark:hover:bg-[#404854] transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-[#f1f5f9]">{{ $review->product->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-600 dark:text-[#cbd5e1]">{{ $review->user->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex text-yellow-400">
                            @for($i = 0; $i < $review->rating; $i++)
                                ⭐
                            @endfor
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-600 dark:text-[#cbd5e1] max-w-xs truncate">{{ $review->title }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($review->status === 'pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                                Pending
                            </span>
                        @elseif($review->status === 'approved')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                Disetujui
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">
                                Ditolak
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.reviews.show', $review) }}" class="text-[#fb923c] hover:underline">
                            Lihat
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-[#9ca3af]">
                        Tidak ada ulasan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($reviews->hasPages())
    <div class="mt-4">
        {{ $reviews->links() }}
    </div>
    @endif
</div>
@endsection
