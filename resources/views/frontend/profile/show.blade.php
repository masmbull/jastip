@extends('layouts.app')

@section('title', 'Profil Saya | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#FDF6EC] to-[#ede7de] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Profile Header --}}
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-8 border border-gray-200 dark:border-[#404854] mb-8">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
                <div class="flex items-center gap-6 mb-6 md:mb-0">
                    @if($profile->profile_picture)
                        <img src="{{ asset('storage/' . $profile->profile_picture) }}" alt="{{ $user->name }}"
                             class="w-24 h-24 rounded-full object-cover border-4 border-[#fb923c]">
                    @else
                        <div class="w-24 h-24 rounded-full bg-[#fb923c] flex items-center justify-center text-white text-3xl font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-[#f1f5f9]">{{ $user->name }}</h1>
                        <p class="text-gray-600 dark:text-[#cbd5e1]">{{ $user->email }}</p>
                        <div class="mt-2 flex items-center gap-3">
                            <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 rounded-full text-sm font-medium">
                                ⭐ {{ ucfirst($profile->membership_tier ?? 'bronze') }}
                            </span>
                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded-full text-sm font-medium">
                                🎁 {{ $profile->loyalty_points ?? 0 }} Poin
                            </span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="px-6 py-2 bg-[#fb923c] text-white rounded-lg hover:bg-[#e6951b] transition">
                    Edit Profil
                </a>
            </div>
        </div>

        {{-- Navigation Tabs --}}
        <div class="flex flex-wrap gap-2 mb-8 border-b border-gray-200 dark:border-[#404854]">
            <a href="{{ route('profile.show') }}" class="px-4 py-2 font-medium border-b-2 {{ request()->routeIs('profile.show') ? 'border-[#fb923c] text-[#fb923c]' : 'border-transparent text-gray-600 dark:text-[#9ca3af]' }}">
                📋 Ringkasan
            </a>
            <a href="{{ route('profile.orders') }}" class="px-4 py-2 font-medium border-b-2 {{ request()->routeIs('profile.orders') ? 'border-[#fb923c] text-[#fb923c]' : 'border-transparent text-gray-600 dark:text-[#9ca3af]' }}">
                📦 Pesanan ({{ $orders->total() }})
            </a>
            <a href="{{ route('profile.addresses') }}" class="px-4 py-2 font-medium border-b-2 {{ request()->routeIs('profile.addresses') ? 'border-[#fb923c] text-[#fb923c]' : 'border-transparent text-gray-600 dark:text-[#9ca3af]' }}">
                📍 Alamat
            </a>
            <a href="{{ route('profile.reviews') }}" class="px-4 py-2 font-medium border-b-2 {{ request()->routeIs('profile.reviews') ? 'border-[#fb923c] text-[#fb923c]' : 'border-transparent text-gray-600 dark:text-[#9ca3af]' }}">
                ⭐ Ulasan
            </a>
            <a href="{{ route('profile.wishlist') }}" class="px-4 py-2 font-medium border-b-2 {{ request()->routeIs('profile.wishlist') ? 'border-[#fb923c] text-[#fb923c]' : 'border-transparent text-gray-600 dark:text-[#9ca3af]' }}">
                💗 Wishlist
            </a>
            <a href="{{ route('notifications.index') }}" class="px-4 py-2 font-medium border-b-2 {{ request()->routeIs('notifications.*') ? 'border-[#fb923c] text-[#fb923c]' : 'border-transparent text-gray-600 dark:text-[#9ca3af]' }}">
                🔔 Notifikasi
            </a>
            <a href="{{ route('profile.loyalty') }}" class="px-4 py-2 font-medium border-b-2 {{ request()->routeIs('profile.loyalty') ? 'border-[#fb923c] text-[#fb923c]' : 'border-transparent text-gray-600 dark:text-[#9ca3af]' }}">
                🎁 Loyalitas
            </a>
            <a href="{{ route('profile.settings') }}" class="px-4 py-2 font-medium border-b-2 {{ request()->routeIs('profile.settings') ? 'border-[#fb923c] text-[#fb923c]' : 'border-transparent text-gray-600 dark:text-[#9ca3af]' }}">
                ⚙️ Pengaturan
            </a>
        </div>

        {{-- Content Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Stats Card --}}
            <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-6 border border-gray-200 dark:border-[#404854]">
                <div class="text-center">
                    <div class="text-4xl mb-2">📦</div>
                    <p class="text-gray-600 dark:text-[#cbd5e1] text-sm">Total Pesanan</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-[#f1f5f9] mt-2">{{ $orders->total() }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-6 border border-gray-200 dark:border-[#404854]">
                <div class="text-center">
                    <div class="text-4xl mb-2">⭐</div>
                    <p class="text-gray-600 dark:text-[#cbd5e1] text-sm">Total Ulasan</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-[#f1f5f9] mt-2">{{ $reviews->total() }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-6 border border-gray-200 dark:border-[#404854]">
                <div class="text-center">
                    <div class="text-4xl mb-2">🎁</div>
                    <p class="text-gray-600 dark:text-[#cbd5e1] text-sm">Poin Loyalitas</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-[#f1f5f9] mt-2">{{ $profile->loyalty_points ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-6 border border-gray-200 dark:border-[#404854] mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-[#f1f5f9]">Pesanan Terbaru</h2>
                <a href="{{ route('profile.orders') }}" class="text-[#fb923c] hover:underline text-sm">Lihat Semua →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200 dark:border-[#404854]">
                        <tr>
                            <th class="text-left py-2 px-4 text-gray-600 dark:text-[#cbd5e1]">No. Pesanan</th>
                            <th class="text-left py-2 px-4 text-gray-600 dark:text-[#cbd5e1]">Total</th>
                            <th class="text-left py-2 px-4 text-gray-600 dark:text-[#cbd5e1]">Status</th>
                            <th class="text-left py-2 px-4 text-gray-600 dark:text-[#cbd5e1]">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders->take(5) as $order)
                        <tr class="border-b border-gray-200 dark:border-[#404854] hover:bg-gray-50 dark:hover:bg-[#404854]">
                            <td class="py-3 px-4">
                                <a href="{{ route('profile.order-detail', $order) }}" class="text-[#fb923c] hover:underline">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-gray-900 dark:text-[#f1f5f9]">{{ format_price($order->total) }}</td>
                            <td class="py-3 px-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $order->status_badge_class }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-600 dark:text-[#cbd5e1]">{{ $order->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500 dark:text-[#9ca3af]">
                                Belum ada pesanan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
