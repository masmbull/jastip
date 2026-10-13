@extends('layouts.admin')

@section('title', 'Detail Ulasan')

@section('content')
<div class="space-y-6">

    <x-page-header title="Detail Ulasan" subtitle="{{ $review->title }}" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.reviews.index')" variant="ghost" icon="arrow-left">Kembali</x-btn>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-card class="lg:col-span-2 animate-fade-up" style="animation-delay: 80ms">
            <div class="pb-6 mb-6 border-b border-[#E2E8F0] dark:border-[#404854]">
                <h2 class="text-xs font-medium uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1] mb-3">Produk yang Diulas</h2>
                <div class="flex items-start gap-4">
                    <img src="{{ $review->product->image_url }}" alt="{{ $review->product->name }}"
                         class="w-16 h-16 object-cover rounded-lg" loading="lazy" decoding="async">
                    <div class="min-w-0">
                        <h3 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9]">{{ $review->product->name }}</h3>
                        <p class="text-sm text-orange-600 dark:text-orange-400 font-medium mt-1 num">{{ $review->product->formatted_price }}</p>
                    </div>
                </div>
            </div>

            <div class="pb-6 mb-6 border-b border-[#E2E8F0] dark:border-[#404854]">
                <h2 class="text-xs font-medium uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1] mb-3">Pembuat Ulasan</h2>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-[#06B6D4] rounded-full flex items-center justify-center text-white font-bold shrink-0" aria-hidden="true">
                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9]">{{ $review->user->name }}</h3>
                        <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] break-all">{{ $review->user->email }}</p>
                        @if($review->order)
                        <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] mt-1">
                            Order: <span class="font-mono text-[#1E293B] dark:text-[#f1f5f9]">#{{ $review->order->order_number }}</span>
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="pb-6 mb-6 border-b border-[#E2E8F0] dark:border-[#404854]">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex text-yellow-500 dark:text-yellow-400" role="img" aria-label="Rating {{ $review->rating }} dari 5">
                        @for($i = 0; $i < $review->rating; $i++)
                            <x-icon name="star" class="w-5 h-5" />
                        @endfor
                    </div>
                    <span class="text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] num">{{ $review->rating }} dari 5</span>
                </div>
                <h3 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-3">{{ $review->title }}</h3>
                <p class="text-[#475569] dark:text-[#cbd5e1] leading-relaxed whitespace-pre-wrap">{{ $review->content }}</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1]">Dibuat pada</p>
                    <p class="text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ $review->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1]">Status</p>
                    <p class="text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] capitalize">{{ $review->status }}</p>
                </div>
                <div>
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1]">Bermanfaat</p>
                    <p class="text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ $review->helpful_count }} votes</p>
                </div>
                <div>
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1]">Tidak Bermanfaat</p>
                    <p class="text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ $review->unhelpful_count }} votes</p>
                </div>
            </div>
        </x-card>

        <div class="space-y-6 animate-fade-up" style="animation-delay: 140ms">
            <x-card>
                <h3 class="text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4">Status Ulasan</h3>

                @if($review->status === 'pending')
                    <div class="space-y-3">
                        <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                            @csrf
                            <x-btn type="submit" variant="brand" icon="check-circle" class="w-full">Setujui</x-btn>
                        </form>
                        <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                            @csrf
                            <x-btn type="submit" variant="danger" icon="x" class="w-full">Tolak</x-btn>
                        </form>
                    </div>
                @else
                    <div class="px-4 py-3 rounded-lg bg-[#F1F5F9] dark:bg-[#2e323b] text-center">
                        @if($review->status === 'approved')
                            <p class="text-emerald-700 dark:text-emerald-300 font-medium inline-flex items-center gap-1.5"><x-icon name="check-circle" class="w-5 h-5" /> Sudah Disetujui</p>
                        @else
                            <p class="text-red-700 dark:text-red-300 font-medium inline-flex items-center gap-1.5"><x-icon name="x" class="w-5 h-5" /> Sudah Ditolak</p>
                        @endif
                    </div>
                @endif
            </x-card>

            <div class="bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-200 dark:border-red-500/30 p-5 sm:p-6">
                <h3 class="text-sm font-bold text-red-900 dark:text-red-200 mb-3">Zona Berbahaya</h3>
                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" data-confirm="Yakin ingin menghapus ulasan ini?">
                    @csrf
                    @method('DELETE')
                    <x-btn type="submit" variant="danger" icon="trash" class="w-full">Hapus Ulasan</x-btn>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection