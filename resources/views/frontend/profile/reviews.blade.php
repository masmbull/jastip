@extends('layouts.app')

@section('title', 'Ulasan Saya | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-8">Ulasan Saya</h1>

        <div class="space-y-4">
            @forelse($reviews as $review)
            <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-6 border border-gray-200 dark:border-[#404854]">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9]">{{ $review->product->name }}</h3>
                        <div class="flex text-yellow-400 mt-2">
                            @for($i = 0; $i < $review->rating; $i++)
                                ⭐
                            @endfor
                        </div>
                    </div>
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                        {{ $review->status === 'approved' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' : 
                           ($review->status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200' :
                           'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200') }}">
                        {{ $review->status }}
                    </span>
                </div>
                <h4 class="font-semibold text-gray-900 dark:text-[#f1f5f9] mb-2">{{ $review->title }}</h4>
                <p class="text-gray-700 dark:text-[#cbd5e1] mb-3">{{ $review->content }}</p>
                <p class="text-sm text-gray-500 dark:text-[#9ca3af]">{{ $review->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-[#9ca3af] text-lg">Belum ada ulasan</p>
            </div>
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
