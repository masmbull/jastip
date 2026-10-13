@extends('layouts.app')

@section('title', 'Ulasan Saya | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-8">Ulasan Saya</h1>

        <div class="space-y-4">
            @forelse($reviews as $review)
            <x-card>
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9]">{{ $review->product->name }}</h3>
                        <div class="flex text-orange-400 mt-2" role="img" aria-label="Rating {{ $review->rating }} dari 5">
                            @for($i = 0; $i < $review->rating; $i++)
                                <x-icon name="star" class="w-5 h-5" />
                            @endfor
                        </div>
                    </div>
                    @php($rtone = match ($review->status) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        default => 'danger',
                    })
                    <x-badge :tone="$rtone" class="shrink-0">{{ $review->status }}</x-badge>
                </div>
                <h4 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-2">{{ $review->title }}</h4>
                <p class="text-[#64748B] dark:text-[#cbd5e1] mb-3">{{ $review->content }}</p>
                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">{{ $review->created_at->diffForHumans() }}</p>
            </x-card>
            @empty
            <x-card>
                <x-empty-state icon="star" title="Belum ada ulasan">
                    Ulasan kamu setelah pesanan selesai akan muncul di sini.
                </x-empty-state>
            </x-card>
            @endforelse
        </div>

        @if($reviews->hasPages())
        <div class="mt-8">
            {{ $reviews->links() }}
        </div>
        @endif
    </div>
</div>
@endsection