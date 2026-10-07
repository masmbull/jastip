@extends('layouts.app')

@section('title', 'Alamat Saya | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#FDF6EC] to-[#ede7de] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-[#f1f5f9]">Alamat Saya</h1>
            <a href="{{ route('addresses.create') }}" class="px-4 py-2 bg-[#fb923c] text-white rounded-lg hover:bg-[#e6951b] transition">
                + Tambah Alamat
            </a>
        </div>

        {{-- Addresses Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($addresses as $address)
            <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-6 border {{ $address->is_default ? 'border-[#fb923c] ring-2 ring-[#fb923c]' : 'border-gray-200 dark:border-[#404854]' }}">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9]">
                            {{ $address->label ?? 'Alamat Utama' }}
                        </h3>
                        @if($address->is_default)
                            <span class="inline-block mt-1 px-2 py-1 bg-[#fb923c] text-white text-xs rounded">
                                Alamat Default
                            </span>
                        @endif
                    </div>
                </div>

                <div class="space-y-2 mb-4">
                    <p class="text-gray-700 dark:text-[#cbd5e1]">
                        <strong>Nama:</strong> {{ $address->full_name }}
                    </p>
                    <p class="text-gray-700 dark:text-[#cbd5e1]">
                        <strong>Telepon:</strong> {{ $address->phone }}
                    </p>
                    <p class="text-gray-700 dark:text-[#cbd5e1]">
                        <strong>Alamat:</strong> {{ $address->street_address }}
                    </p>
                    <p class="text-gray-700 dark:text-[#cbd5e1]">
                        <strong>Kota:</strong> {{ $address->city }}, {{ $address->province }}
                    </p>
                    <p class="text-gray-700 dark:text-[#cbd5e1]">
                        <strong>Kode Pos:</strong> {{ $address->postal_code }}
                    </p>
                    @if($address->notes)
                    <p class="text-gray-700 dark:text-[#cbd5e1]">
                        <strong>Catatan:</strong> {{ $address->notes }}
                    </p>
                    @endif
                </div>

                <div class="flex gap-2 pt-4 border-t border-gray-200 dark:border-[#404854]">
                    @if(!$address->is_default)
                    <form method="POST" action="{{ route('addresses.default', $address) }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 text-sm text-[#fb923c] hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded transition">
                            Jadikan Default
                        </button>
                    </form>
                    @endif
                    
                    <a href="{{ route('addresses.edit', $address) }}" class="flex-1 px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition text-center">
                        Edit
                    </a>
                    
                    <form method="POST" action="{{ route('addresses.destroy', $address) }}" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus alamat ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 dark:text-[#9ca3af] text-lg mb-4">Belum ada alamat yang tersimpan</p>
                <a href="{{ route('addresses.create') }}" class="px-4 py-2 bg-[#fb923c] text-white rounded-lg hover:bg-[#e6951b] transition inline-block">
                    + Tambah Alamat Pertama
                </a>
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
