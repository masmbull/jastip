@extends('layouts.app')

@section('title', 'Alamat Saya | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Alamat Saya</h1>
            <x-btn as="a" :href="route('addresses.create')" variant="brand" icon="plus" class="w-full sm:w-auto">Tambah Alamat</x-btn>
        </div>

        {{-- Addresses Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($addresses as $address)
            <x-card class="{{ $address->is_default ? 'border-[#06B6D4] ring-2 ring-[#06B6D4]' : '' }}">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9]">
                            {{ $address->label ?? 'Alamat Utama' }}
                        </h3>
                        @if($address->is_default)
                            <div class="mt-1">
                                <x-badge tone="info">Alamat Default</x-badge>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-2 mb-4 text-[#64748B] dark:text-[#cbd5e1]">
                    <p><strong class="text-[#1E293B] dark:text-[#f1f5f9]">Nama:</strong> {{ $address->full_name }}</p>
                    <p><strong class="text-[#1E293B] dark:text-[#f1f5f9]">Telepon:</strong> <span class="num">{{ $address->phone }}</span></p>
                    <p><strong class="text-[#1E293B] dark:text-[#f1f5f9]">Alamat:</strong> {{ $address->street_address }}</p>
                    <p><strong class="text-[#1E293B] dark:text-[#f1f5f9]">Kota:</strong> {{ $address->city }}, {{ $address->province }}</p>
                    <p><strong class="text-[#1E293B] dark:text-[#f1f5f9]">Kode Pos:</strong> <span class="num">{{ $address->postal_code }}</span></p>
                    @if($address->notes)
                        <p><strong class="text-[#1E293B] dark:text-[#f1f5f9]">Catatan:</strong> {{ $address->notes }}</p>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row gap-2 pt-4 border-t border-[#E2E8F0] dark:border-[#404854]">
                    @if(!$address->is_default)
                    <form method="POST" action="{{ route('addresses.default', $address) }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full min-h-[44px] px-3 py-2 text-sm font-medium text-[#06B6D4] hover:bg-orange-50 dark:hover:bg-orange-500/10 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-orange-300">
                            Jadikan Default
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('addresses.edit', $address) }}" class="flex-1 inline-flex items-center justify-center min-h-[44px] px-3 py-2 text-sm font-medium text-[#141414] dark:text-[#FAF6EF] dark:text-[#141414] dark:text-[#FAF6EF] hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-orange-300 text-center">
                        Edit
                    </a>

                    <form method="POST" action="{{ route('addresses.destroy', $address) }}" class="flex-1"
                          data-confirm="Yakin ingin menghapus alamat ini?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full min-h-[44px] px-3 py-2 text-sm font-medium text-red-600 dark:text-red-300 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-orange-300">
                            Hapus
                        </button>
                    </form>
                </div>
            </x-card>
            @empty
            <div class="col-span-full">
                <x-card>
                    <x-empty-state icon="map-pin" title="Belum ada alamat yang tersimpan">
                        Tambahkan alamat pengiriman supaya pesanan kamu bisa diproses.
                        <div class="mt-4">
                            <x-btn as="a" :href="route('addresses.create')" variant="brand" icon="plus">Tambah Alamat Pertama</x-btn>
                        </div>
                    </x-empty-state>
                </x-card>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($addresses->hasPages())
        <div class="mt-8">
            {{ $addresses->links() }}
        </div>
        @endif
    </div>
</div>
@endsection