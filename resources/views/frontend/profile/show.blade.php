@extends('layouts.app')

@section('title', 'Profil Saya | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Profile Header --}}
        <x-card class="mb-8">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-center gap-4 sm:gap-6 min-w-0">
                    @if($profile->profile_picture)
                        <img src="{{ asset('storage/' . $profile->profile_picture) }}" alt="{{ $user->name }}"
                             class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover border-4 border-[#06B6D4] shrink-0">
                    @else
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-[#06B6D4] flex items-center justify-center text-white text-3xl font-bold shrink-0">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <h1 class="text-2xl sm:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9] truncate">{{ $user->name }}</h1>
                        <p class="text-[#64748B] dark:text-[#cbd5e1] truncate">{{ $user->email }}</p>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <x-badge tone="brand">
                                <x-icon name="star" class="w-3.5 h-3.5" />
                                {{ ucfirst($profile->membership_tier ?? 'bronze') }}
                            </x-badge>
                            <x-badge tone="info">
                                <x-icon name="gift" class="w-3.5 h-3.5" />
                                {{ $profile->loyalty_points ?? 0 }} Poin
                            </x-badge>
                        </div>
                    </div>
                </div>
                <x-btn as="a" :href="route('profile.edit')" variant="brand" icon="pencil" class="w-full md:w-auto shrink-0">
                    Edit Profil
                </x-btn>
            </div>
        </x-card>

        {{-- Navigation Tabs --}}
        @php
            $profileTabs = [
                ['route' => 'profile.show', 'pattern' => 'profile.show', 'icon' => 'clipboard', 'label' => 'Ringkasan'],
                ['route' => 'profile.orders', 'pattern' => 'profile.orders', 'icon' => 'cube', 'label' => 'Pesanan (' . $orders->total() . ')'],
                ['route' => 'profile.addresses', 'pattern' => 'profile.addresses', 'icon' => 'map-pin', 'label' => 'Alamat'],
                ['route' => 'profile.reviews', 'pattern' => 'profile.reviews', 'icon' => 'star', 'label' => 'Ulasan'],
                ['route' => 'profile.wishlist', 'pattern' => 'profile.wishlist', 'icon' => 'heart', 'label' => 'Wishlist'],
                ['route' => 'notifications.index', 'pattern' => 'notifications.*', 'icon' => 'bell', 'label' => 'Notifikasi'],
                ['route' => 'chat.index', 'pattern' => 'chat.*', 'icon' => 'chat', 'label' => 'Bantuan'],
                ['route' => 'profile.loyalty', 'pattern' => 'profile.loyalty', 'icon' => 'gift', 'label' => 'Loyalitas'],
                ['route' => 'profile.settings', 'pattern' => 'profile.settings', 'icon' => 'lock', 'label' => 'Pengaturan'],
            ];
        @endphp
        <nav class="mb-8 -mx-1 flex flex-wrap gap-1 border-b border-[#E2E8F0] dark:border-[#404854] overflow-x-auto" aria-label="Navigasi profil">
            @foreach($profileTabs as $tab)
                @php($active = request()->routeIs($tab['pattern']))
                <a href="{{ route($tab['route']) }}"
                   @if($active) aria-current="page" @endif
                   class="inline-flex items-center gap-1.5 px-4 py-3 text-sm font-medium border-b-2 -mb-px whitespace-nowrap transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300 rounded-t
                          {{ $active ? 'border-[#06B6D4] text-[#06B6D4]' : 'border-transparent text-[#64748B] dark:text-[#cbd5e1] hover:text-[#06B6D4]' }}">
                    <x-icon :name="$tab['icon']" class="w-4 h-4" />
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Content Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <x-card>
                <div class="text-center">
                    <x-icon name="cube" class="w-10 h-10 mx-auto mb-2 text-orange-500" />
                    <p class="text-[#64748B] dark:text-[#cbd5e1] text-sm">Total Pesanan</p>
                    <p class="text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mt-2 num">{{ $orders->total() }}</p>
                </div>
            </x-card>

            <x-card>
                <div class="text-center">
                    <x-icon name="star" class="w-10 h-10 mx-auto mb-2 text-orange-500" />
                    <p class="text-[#64748B] dark:text-[#cbd5e1] text-sm">Total Ulasan</p>
                    <p class="text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mt-2 num">{{ $reviews->total() }}</p>
                </div>
            </x-card>

            <x-card>
                <div class="text-center">
                    <x-icon name="gift" class="w-10 h-10 mx-auto mb-2 text-orange-500" />
                    <p class="text-[#64748B] dark:text-[#cbd5e1] text-sm">Poin Loyalitas</p>
                    <p class="text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mt-2 num">{{ $profile->loyalty_points ?? 0 }}</p>
                </div>
            </x-card>
        </div>

        {{-- Recent Orders --}}
        <x-card padded="false" class="mb-8">
            <div class="flex items-center justify-between gap-4 p-5 sm:p-6 border-b border-[#E2E8F0] dark:border-[#404854]">
                <h2 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Pesanan Terbaru</h2>
                <a href="{{ route('profile.orders') }}" class="inline-flex items-center gap-1 text-sm font-medium text-[#06B6D4] hover:underline focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">
                    Lihat Semua <x-icon name="arrow-right" class="w-4 h-4" />
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-[#E2E8F0] dark:border-[#404854]">
                        <tr>
                            <th scope="col" class="text-left py-2 px-4 font-medium text-[#64748B] dark:text-[#cbd5e1]">No. Pesanan</th>
                            <th scope="col" class="text-right py-2 px-4 font-medium text-[#64748B] dark:text-[#cbd5e1]">Total</th>
                            <th scope="col" class="text-left py-2 px-4 font-medium text-[#64748B] dark:text-[#cbd5e1]">Status</th>
                            <th scope="col" class="text-left py-2 px-4 font-medium text-[#64748B] dark:text-[#cbd5e1]">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders->take(5) as $order)
                        <tr class="border-b border-[#E2E8F0] dark:border-[#404854] last:border-0 hover:bg-[#F8FAFC] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="py-3 px-4">
                                <a href="{{ route('profile.order-detail', $order) }}" class="text-[#06B6D4] hover:underline num">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-right num text-[#1E293B] dark:text-[#f1f5f9]">{{ format_price($order->total) }}</td>
                            <td class="py-3 px-4">
                                @php($tone = match ($order->status) {
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                    'awaiting_payment', 'pending' => 'warning',
                                    'shipped' => 'info',
                                    default => 'brand',
                                })
                                <x-badge :tone="$tone">{{ $order->status_label }}</x-badge>
                            </td>
                            <td class="py-3 px-4 num text-[#64748B] dark:text-[#cbd5e1]">{{ $order->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
                                <x-empty-state icon="receipt-percent" title="Belum ada pesanan">
                                    Mulai titip produk impian kamu dari katalog.
                                </x-empty-state>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</div>
@endsection