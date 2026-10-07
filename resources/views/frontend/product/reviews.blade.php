@extends('layouts.app')

@section('title', 'Ulasan ' . $product->name . ' | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#FDF6EC] to-[#ede7de] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Product Info Card --}}
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-6 mb-8 border border-gray-200 dark:border-[#404854]">
            <div class="flex flex-col md:flex-row gap-6">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                     class="w-24 h-24 object-cover rounded-lg shadow">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-2">{{ $product->name }}</h1>
                    <p class="text-gray-600 dark:text-[#cbd5e1] mb-3">{{ $product->description }}</p>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center">
                            <span class="text-2xl font-bold text-yellow-400">{{ number_format($averageRating, 1, ',', '.') }}</span>
                            <span class="text-gray-500 dark:text-[#cbd5e1] ml-2">dari 5 ({{ $reviewCount }} ulasan)</span>
                        </div>
                        <a href="{{ route('products.show', $product) }}" 
                           class="px-4 py-2 bg-[#fb923c] text-white rounded-lg hover:bg-[#e6951b] transition">
                            Kembali ke Produk
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rating Distribution --}}
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-6 mb-8 border border-gray-200 dark:border-[#404854]">
            <h2 class="text-xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-6">Distribusi Rating</h2>
            <div class="space-y-3">
                @foreach([5, 4, 3, 2, 1] as $star)
                    @php $count = $ratingDistribution[$star]; @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-12 text-sm font-medium text-gray-700 dark:text-[#cbd5e1]">{{ $star }} ⭐</span>
                        <div class="flex-1 bg-gray-200 dark:bg-[#404854] rounded-full h-2">
                            <div class="bg-yellow-400 h-2 rounded-full" 
                                 style="width: {{ $reviewCount > 0 ? ($count / $reviewCount) * 100 : 0 }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-[#cbd5e1] w-8 text-right">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Add Review Form --}}
        @auth
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-6 mb-8 border border-gray-200 dark:border-[#404854]">
            <h2 class="text-xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-6">Tulis Ulasan Anda</h2>
            <form id="reviewForm" class="space-y-4">
                @csrf
                
                {{-- Rating --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-3">Rating</label>
                    <div class="flex gap-2" id="ratingStars">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" class="star-btn text-3xl transition"
                                    data-rating="{{ $i }}">
                                ☆
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating" required>
                </div>

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Judul Ulasan</label>
                    <input type="text" name="title" required maxlength="100"
                           class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] placeholder-gray-500 dark:placeholder-[#9ca3af] focus:outline-none focus:ring-2 focus:ring-[#fb923c]"
                           placeholder="Contoh: Produk berkualitas dan terpercaya">
                </div>

                {{-- Content --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Ulasan Detail</label>
                    <textarea name="content" required maxlength="1000" rows="5"
                              class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] placeholder-gray-500 dark:placeholder-[#9ca3af] focus:outline-none focus:ring-2 focus:ring-[#fb923c]"
                              placeholder="Bagikan pengalaman Anda dengan produk ini..."></textarea>
                    <p class="text-xs text-gray-500 dark:text-[#9ca3af] mt-1" id="charCount">0 / 1000</p>
                </div>

                {{-- Submit --}}
                <button type="submit" class="w-full px-4 py-3 bg-[#fb923c] hover:bg-[#e6951b] text-white font-semibold rounded-lg transition">
                    Kirim Ulasan
                </button>
            </form>
        </div>
        @else
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 mb-8">
            <p class="text-blue-900 dark:text-blue-200">
                <a href="{{ route('login') }}" class="font-bold hover:underline">Masuk</a> untuk menulis ulasan
            </p>
        </div>
        @endauth

        {{-- Reviews List --}}
        <div class="space-y-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-[#f1f5f9]">Ulasan Pelanggan</h2>
            
            @forelse($reviews as $review)
            <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-6 border border-gray-200 dark:border-[#404854]">
                {{-- Review Header --}}
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-[#fb923c] rounded-full flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($review->user?->name ?? 'P', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-[#f1f5f9]">{{ $review->user?->name ?? 'Pengguna' }}</p>
                                <p class="text-sm text-gray-500 dark:text-[#9ca3af]">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex text-yellow-400">
                                @for($i = 0; $i < $review->rating; $i++)
                                    ⭐
                                @endfor
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-[#cbd5e1]">{{ $review->rating }} dari 5</span>
                        </div>
                    </div>
                </div>

                {{-- Review Title and Content --}}
                <h3 class="font-bold text-gray-900 dark:text-[#f1f5f9] mb-2">{{ $review->title }}</h3>
                <p class="text-gray-700 dark:text-[#cbd5e1] mb-4 leading-relaxed">{{ $review->content }}</p>

                {{-- Helpful Button --}}
                <div class="flex items-center gap-3 border-t border-gray-200 dark:border-[#404854] pt-4">
                    <button class="helpful-btn flex items-center gap-2 px-3 py-1 text-sm text-gray-600 dark:text-[#cbd5e1] hover:text-[#fb923c] transition"
                            data-review-id="{{ $review->id }}">
                        👍 <span class="helpful-count">{{ $review->helpful_count }}</span>
                    </button>
                    <span class="text-sm text-gray-500 dark:text-[#9ca3af]">Bermanfaat?</span>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-500 dark:text-[#cbd5e1]">
                <p class="text-lg">Belum ada ulasan untuk produk ini</p>
                <p class="text-sm mt-2">Jadilah yang pertama memberikan ulasan!</p>
            </div>
            @endforelse

            {{-- Pagination --}}
            @if($reviews->hasPages())
            <div class="mt-8">
                {{ $reviews->links() }}
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
            btn.textContent = '⭐';
            btn.classList.add('text-yellow-400');
        } else {
            btn.textContent = '☆';
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
