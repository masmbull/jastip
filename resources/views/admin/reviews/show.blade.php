@extends('layouts.admin')

@section('title', 'Detail Ulasan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.reviews.index') }}" class="text-[#06B6D4] hover:underline"><x-icon name="arrow-left" class="inline-block w-5 h-5 align-text-bottom" /> Kembali</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2">
            {{-- Review Card --}}
            <div class="bg-white dark:bg-[#23252b] rounded-lg shadow border border-gray-200 dark:border-[#404854] p-8 mb-6">
                {{-- Product Info --}}
                <div class="mb-8 pb-8 border-b border-gray-200 dark:border-[#404854]">
                    <h2 class="text-sm font-medium text-gray-600 dark:text-[#cbd5e1] mb-2">Produk yang Diulas</h2>
                    <div class="flex items-start gap-4">
                        <img src="{{ $review->product->image_url }}" alt="{{ $review->product->name }}"
                             class="w-16 h-16 object-cover rounded">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-[#f1f5f9]">{{ $review->product->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-[#cbd5e1] mt-1">{{ $review->product->formatted_price }}</p>
                        </div>
                    </div>
                </div>

                {{-- User Info --}}
                <div class="mb-8 pb-8 border-b border-gray-200 dark:border-[#404854]">
                    <h2 class="text-sm font-medium text-gray-600 dark:text-[#cbd5e1] mb-4">Pembuat Ulasan</h2>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-[#06B6D4] rounded-full flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-[#f1f5f9]">{{ $review->user->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">{{ $review->user->email }}</p>
                            @if($review->order)
                            <p class="text-sm text-gray-600 dark:text-[#cbd5e1] mt-1">
                                <strong>Order:</strong> #{{ $review->order->order_number }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Review Content --}}
                <div class="mb-8 pb-8 border-b border-gray-200 dark:border-[#404854]">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex text-yellow-400">
                            @for($i = 0; $i < $review->rating; $i++)
                                <x-icon name="star" class="inline-block w-5 h-5 align-text-bottom" />
                            @endfor
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-[#cbd5e1]">{{ $review->rating }} dari 5</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-3">{{ $review->title }}</h3>
                    <p class="text-gray-700 dark:text-[#cbd5e1] leading-relaxed whitespace-pre-wrap">{{ $review->content }}</p>
                </div>

                {{-- Meta Info --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-gray-600 dark:text-[#9ca3af]">Dibuat pada</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-[#f1f5f9]">{{ $review->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 dark:text-[#9ca3af]">Status</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-[#f1f5f9] capitalize">{{ $review->status }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 dark:text-[#9ca3af]">Bermanfaat</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-[#f1f5f9]">{{ $review->helpful_count }} votes</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 dark:text-[#9ca3af]">Tidak Bermanfaat</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-[#f1f5f9]">{{ $review->unhelpful_count }} votes</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Actions --}}
        <div>
            {{-- Status Card --}}
            <div class="bg-white dark:bg-[#23252b] rounded-lg shadow border border-gray-200 dark:border-[#404854] p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9] mb-4">Status Ulasan</h3>
                
                @if($review->status === 'pending')
                    <div class="space-y-3">
                        <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition">
                                <x-icon name="check-circle" class="inline-block w-5 h-5 align-text-bottom" /> Setujui
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition">
                                <x-icon name="x" class="inline-block w-5 h-5 align-text-bottom" /> Tolak
                            </button>
                        </form>
                    </div>
                @else
                    <div class="px-4 py-3 rounded-lg bg-gray-100 dark:bg-[#404854] text-center">
                        @if($review->status === 'approved')
                            <p class="text-green-700 dark:text-green-200 font-medium"><x-icon name="check-circle" class="inline-block w-5 h-5 align-text-bottom" /> Sudah Disetujui</p>
                        @else
                            <p class="text-red-700 dark:text-red-200 font-medium"><x-icon name="x" class="inline-block w-5 h-5 align-text-bottom" /> Sudah Ditolak</p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Delete Card --}}
            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800 p-6">
                <h3 class="text-sm font-bold text-red-900 dark:text-red-200 mb-3">Zona Berbahaya</h3>
                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" data-confirm="Yakin ingin menghapus ulasan ini?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                        Hapus Ulasan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
