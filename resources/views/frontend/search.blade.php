@extends('layouts.app')

@section('title', 'Cari Produk | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#FDF6EC] to-[#ede7de] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Search Bar --}}
        <div class="mb-8">
            <form method="GET" action="{{ route('search') }}" class="flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..."
                       class="flex-1 px-4 py-3 bg-white dark:bg-[#23252b] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] placeholder-gray-500 dark:placeholder-[#9ca3af] focus:outline-none focus:ring-2 focus:ring-[#fb923c]">
                <button type="submit" class="px-6 py-3 bg-[#fb923c] text-white rounded-lg hover:bg-[#e6951b] transition font-medium">
                    Cari
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Sidebar Filters --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-6 border border-gray-200 dark:border-[#404854] sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9] mb-4">Filter</h3>

                    <form method="GET" action="{{ route('search') }}" class="space-y-6">
                        {{-- Search --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-[#cbd5e1]">Pencarian</label>
                            <input type="text" name="q" value="{{ request('q') }}"
                                   class="w-full mt-2 px-3 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] text-sm">
                        </div>

                        {{-- Category --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-[#cbd5e1]">Kategori</label>
                            <select name="category" class="w-full mt-2 px-3 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] text-sm">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Price Range --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-3 block">Harga</label>
                            <div class="space-y-2">
                                <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="Min" step="10000"
                                       class="w-full px-3 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] text-sm">
                                <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="Max" step="10000"
                                       class="w-full px-3 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] text-sm">
                            </div>
                        </div>

                        {{-- Stock Filter --}}
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="in_stock" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }}
                                   class="w-4 h-4 rounded cursor-pointer">
                            <label for="in_stock" class="text-sm text-gray-700 dark:text-[#cbd5e1] cursor-pointer">
                                Stok Tersedia
                            </label>
                        </div>

                        {{-- Featured Filter --}}
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="featured" name="featured" value="1" {{ request('featured') ? 'checked' : '' }}
                                   class="w-4 h-4 rounded cursor-pointer">
                            <label for="featured" class="text-sm text-gray-700 dark:text-[#cbd5e1] cursor-pointer">
                                Produk Unggulan
                            </label>
                        </div>

                        {{-- Sort --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-[#cbd5e1]">Urutkan</label>
                            <select name="sort" class="w-full mt-2 px-3 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] text-sm">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                            </select>
                        </div>

                        {{-- Buttons --}}
                        <div class="space-y-2">
                            <button type="submit" class="w-full px-4 py-2 bg-[#fb923c] text-white rounded-lg hover:bg-[#e6951b] transition font-medium">
                                Terapkan Filter
                            </button>
                            <a href="{{ route('search') }}" class="block w-full px-4 py-2 border border-gray-300 dark:border-[#404854] text-gray-700 dark:text-[#cbd5e1] rounded-lg hover:bg-gray-100 dark:hover:bg-[#404854] transition text-center text-sm">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Products Grid --}}
            <div class="lg:col-span-3">
                {{-- Results Info --}}
                <div class="mb-6 flex items-center justify-between">
                    <p class="text-gray-600 dark:text-[#cbd5e1]">
                        Menampilkan <strong>{{ $products->count() }}</strong> dari <strong>{{ $products->total() }}</strong> produk
                    </p>
                </div>

                @if($products->count() > 0)
                    {{-- Products --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        @foreach($products as $product)
                        <div class="group">
                            <a href="{{ route('products.show', $product) }}" class="block relative overflow-hidden rounded-lg bg-white dark:bg-[#23252b] shadow-lg hover:shadow-xl transition">
                                {{-- Product Image --}}
                                <div class="relative overflow-hidden bg-gray-200 dark:bg-[#404854] h-48">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                    
                                    @if($product->is_featured)
                                    <span class="absolute top-2 left-2 px-2 py-1 bg-[#fb923c] text-white text-xs font-bold rounded">UNGGULAN</span>
                                    @endif
                                </div>

                                {{-- Product Info --}}
                                <div class="p-4">
                                    <h3 class="font-bold text-gray-900 dark:text-[#f1f5f9] mb-2 line-clamp-2">{{ $product->name }}</h3>
                                    
                                    {{-- Rating --}}
                                    @if($product->rating)
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-sm text-yellow-400">⭐ {{ $product->rating }}</span>
                                        <span class="text-xs text-gray-600 dark:text-[#9ca3af]">({{ $product->sold_count }}+ terjual)</span>
                                    </div>
                                    @endif

                                    {{-- Price --}}
                                    <p class="text-lg font-bold text-[#fb923c] mb-3">{{ $product->formatted_price }}</p>

                                    {{-- Stock Badge --}}
                                    <span class="inline-block text-xs px-2 py-1 rounded {{ $product->is_in_stock() ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200' }}">
                                        {{ $product->is_in_stock() ? 'Tersedia' : 'Habis' }}
                                    </span>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($products->hasPages())
                    <div class="mt-12">
                        {{ $products->links() }}
                    </div>
                    @endif
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-12">
                        <div class="text-5xl mb-4">🔍</div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-2">Produk Tidak Ditemukan</h3>
                        <p class="text-gray-600 dark:text-[#cbd5e1] mb-6">Coba ubah filter atau pencarian Anda</p>
                        <a href="{{ route('search') }}" class="px-6 py-2 bg-[#fb923c] text-white rounded-lg hover:bg-[#e6951b] transition inline-block">
                            Reset Filter
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
