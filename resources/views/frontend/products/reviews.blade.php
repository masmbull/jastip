@extends('layouts.app')

@section('title', 'Ulasan ' . $product->name)

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
{{-- Product Info Card --}}
        <x-card class="mb-8">
            <div class="flex flex-col md:flex-row gap-6">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                     class="w-24 h-24 object-cover rounded-lg shadow" loading="lazy" decoding="async">
                <div class="flex-1 min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-2">{{ $product->name }}</h1>
                    <p class="text-[#64748B] dark:text-[#cbd5e1] mb-3 line-clamp-2">{{ $product->description }}</p>
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center">
                            <span class="text-2xl font-bold text-amber-500 num">{{ number_format($averageRating, 1, ',', '.') }}</span>
                            <span class="text-[#64748B] dark:text-[#cbd5e1] ml-2">dari 5 (<span class="num">{{ $reviewCount }}</span> ulasan)</span>
                        </div>
                        <x-btn as="a" :href="route('products.show', $product)" variant="outline" icon="arrow-left" class="rounded-lg">
                            Kembali ke Produk
                        </x-btn>
                    </div>
                </div>
            </div>
        </x-card>

{{-- Rating Distribution --}}
        <x-card class="mb-8">
            <h2 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-6">Distribusi Rating</h2>
            <div class="space-y-3">
                @foreach([5, 4, 3, 2, 1] as $star)
                    @php $count = $ratingDistribution[$star]; @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-12 text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] flex items-center gap-1 num">{{ $star }} <x-icon name="star" class="w-4 h-4 text-amber-500" aria-hidden="true" /></span>
                        <div class="flex-1 bg-[#E2E8F0] dark:bg-[#404854] rounded-full h-2">
                            <div class="bg-amber-400 h-2 rounded-full"
                                 style="width: {{ $reviewCount > 0 ? ($count / $reviewCount) * 100 : 0 }}%"></div>
                        </div>
                        <span class="text-sm text-[#64748B] dark:text-[#cbd5e1] w-8 text-right num">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>

{{-- Add Review Form --}}
        @auth
        <x-card class="mb-8">
            <h2 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-6">Tulis Ulasan Anda</h2>
            <form id="reviewForm" class="space-y-4">
                @csrf

                {{-- Rating --}}
                <div>
                    <span class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-3">Rating</span>
                    <div class="flex gap-2" id="ratingStars" role="group" aria-label="Pilih rating">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" class="star-btn w-11 h-11 flex items-center justify-center rounded-lg text-[#94A3B8] dark:text-[#64748B] hover:text-amber-400 transition focus:outline-none focus:ring-2 focus:ring-orange-300"
                                    data-rating="{{ $i }}" aria-label="Beri {{ $i }} bintang">
                                <x-icon name="star" class="w-7 h-7" />
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating" required>
                </div>

                {{-- Title --}}
                <div>
                    <label for="reviewTitle" class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Judul Ulasan</label>
                    <input type="text" id="reviewTitle" name="title" required maxlength="100"
                           class="w-full px-4 py-2 bg-white dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] placeholder-[#94A3B8] dark:placeholder-[#64748B] focus:outline-none focus:ring-2 focus:ring-orange-300"
                           placeholder="Contoh: Produk berkualitas dan terpercaya">
                </div>

                {{-- Content --}}
                <div>
                    <label for="reviewContent" class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Ulasan Detail</label>
                    <textarea name="content" id="reviewContent" required maxlength="1000" rows="5"
                              class="w-full px-4 py-2 bg-white dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] placeholder-[#94A3B8] dark:placeholder-[#64748B] focus:outline-none focus:ring-2 focus:ring-orange-300"
                              placeholder="Bagikan pengalaman Anda dengan produk ini..."></textarea>
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1 num" id="charCount">0 / 1000</p>
                </div>

                {{-- Submit --}}
                <x-btn type="submit" variant="brand" class="w-full py-3 text-base">
                    Kirim Ulasan
                </x-btn>
            </form>
        </x-card>
        @else
        <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 rounded-xl p-6 mb-8">
            <p class="text-blue-900 dark:text-blue-200">
                <a href="{{ route('login') }}" class="font-bold hover:underline rounded focus:outline-none focus:ring-2 focus:ring-orange-300">Masuk</a> untuk menulis ulasan
            </p>
        </div>
        @endauth

{{-- Reviews List --}}
        <div class="space-y-4">
            <h2 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Ulasan Pelanggan</h2>

            @forelse($reviews as $review)
            <x-card>
                {{-- Review Header --}}
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold shrink-0" aria-hidden="true">
                                {{ strtoupper(substr($review->user?->name ?? 'P', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">{{ $review->user?->name ?? 'Pengguna' }}</p>
                                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex text-amber-500" role="img" aria-label="Rating {{ $review->rating }} dari 5">
                                @for($i = 0; $i < $review->rating; $i++)
                                    <x-icon name="star" class="w-4 h-4" aria-hidden="true" />
                                @endfor
                            </div>
                            <span class="text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ $review->rating }} dari 5</span>
                        </div>
                    </div>
                </div>

                {{-- Review Title and Content --}}
                <h3 class="font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-2">{{ $review->title }}</h3>
                <p class="text-[#64748B] dark:text-[#cbd5e1] mb-4 leading-relaxed">{{ $review->content }}</p>

                {{-- Helpful Button --}}
                <div class="flex items-center gap-3 border-t border-[#E2E8F0] dark:border-[#404854] pt-4">
                    <button class="helpful-btn flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-500/10 transition focus:outline-none focus:ring-2 focus:ring-orange-300"
                            data-review-id="{{ $review->id }}" aria-label="Tandai ulasan bermanfaat">
                        <x-icon name="check-circle" class="w-5 h-5" /> <span class="helpful-count num">{{ $review->helpful_count }}</span>
                    </button>
                    <span class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Bermanfaat?</span>
                </div>
            </x-card>
            @empty
            <x-empty-state icon="chat" title="Belum ada ulasan untuk produk ini">
                Jadilah yang pertama memberikan ulasan!
            </x-empty-state>
            @endforelse

            {{-- Pagination --}}
            @if($reviews->hasPages())
            <div class="mt-8">
                {{ $reviews->links('components.pagination') }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Rating stars interaction
let selectedRating = 0;

document.querySelectorAll('.star-btn').forEach(btn => {
    btn.addEventListener('mouseenter', function() {
        const rating = parseInt(this.dataset.rating);
        highlightStars(rating);
    });

    btn.addEventListener('click', function() {
        selectedRating = parseInt(this.dataset.rating);
        document.getElementById('rating').value = selectedRating;
        highlightStars(selectedRating);
    });
});

document.getElementById('ratingStars')?.addEventListener('mouseleave', function() {
    highlightStars(selectedRating);
});

function highlightStars(rating) {
    document.querySelectorAll('.star-btn').forEach((btn, index) => {
        if (index < rating) {
            btn.classList.add('text-yellow-400');
        } else {
            btn.classList.remove('text-yellow-400');
        }
    });
}

// Character counter
document.querySelector('textarea[name="content"]')?.addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length + ' / 1000';
});

// Form submission
document.getElementById('reviewForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    
    try {
        const response = await fetch('{{ route("reviews.store", $product) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (response.ok) {
            window.toast('Terima kasih! Ulasan Anda akan ditinjau admin sebelum ditampilkan.', 'success');
            this.reset();
            selectedRating = 0;
            highlightStars(0);
        } else {
            window.toast(data.message || 'Terjadi kesalahan', 'error');
        }
    } catch (error) {
        window.toast('Terjadi kesalahan: ' + error.message, 'error');
    }
});

// Helpful button
document.querySelectorAll('.helpful-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        if (this.dataset.voted) return; // guard: prevent double-fire
        this.dataset.voted = '1';
        const reviewId = this.dataset.reviewId;

        try {
            const response = await fetch(`/ulasan/${reviewId}/helpful`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (response.ok) {
                this.querySelector('.helpful-count').textContent = data.helpful_count;
                this.style.opacity = '0.5';
                this.style.pointerEvents = 'none';
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
});
</script>
@endsection
