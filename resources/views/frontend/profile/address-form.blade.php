@extends('layouts.app')

@section('title', (isset($address) ? 'Edit' : 'Tambah') . ' Alamat | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#FDF6EC] to-[#ede7de] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-6">
            <a href="{{ route('profile.addresses') }}" class="text-[#fb923c] hover:underline">← Kembali ke Alamat</a>
        </div>

        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-8 border border-gray-200 dark:border-[#404854]">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-6">
                {{ isset($address) ? 'Edit Alamat' : 'Tambah Alamat Baru' }}
            </h1>

            <form method="POST" action="{{ isset($address) ? route('addresses.update', $address) : route('addresses.store') }}" class="space-y-5">
                @csrf
                @if(isset($address))
                    @method('PUT')
                @endif

                {{-- Label --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Label (Rumah, Kantor, dll)</label>
                    <input type="text" name="label" value="{{ old('label', $address->label ?? '') }}" maxlength="50"
                           placeholder="Contoh: Rumah, Kantor, Toko"
                           class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#fb923c]">
                    @error('label')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Full Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Nama Lengkap *</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $address->full_name ?? '') }}" required
                           class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#fb923c]">
                    @error('full_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">No. Telepon *</label>
                    <input type="tel" name="phone" value="{{ old('phone', $address->phone ?? '') }}" required
                           placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#fb923c]">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Street Address --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Alamat Lengkap *</label>
                    <textarea name="street_address" required rows="3"
                              class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#fb923c]"
                              placeholder="Jalan, nomor rumah, RT/RW, dll">{{ old('street_address', $address->street_address ?? '') }}</textarea>
                    @error('street_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- City --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Kota/Kabupaten *</label>
                    <input type="text" name="city" value="{{ old('city', $address->city ?? '') }}" required
                           class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#fb923c]">
                    @error('city')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Province --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Provinsi *</label>
                    <input type="text" name="province" value="{{ old('province', $address->province ?? '') }}" required
                           class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#fb923c]">
                    @error('province')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Postal Code --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Kode Pos *</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" required
                           placeholder="12345"
                           class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#fb923c]">
                    @error('postal_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-[#cbd5e1] mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" rows="2" maxlength="500"
                              class="w-full px-4 py-2 bg-gray-100 dark:bg-[#404854] border border-gray-300 dark:border-[#404854] rounded-lg dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-[#fb923c]"
                              placeholder="Contoh: Sebelah minimarket, pintu berwarna biru">{{ old('notes', $address->notes ?? '') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Set as Default --}}
                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-[#404854] rounded-lg">
                    <input type="checkbox" id="is_default" name="is_default" value="1"
                           {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 rounded cursor-pointer">
                    <label for="is_default" class="text-sm text-gray-700 dark:text-[#cbd5e1] cursor-pointer">
                        Jadikan alamat default
                    </label>
                </div>

                {{-- Submit --}}
                <div class="flex gap-3 pt-6 border-t border-gray-200 dark:border-[#404854]">
                    <a href="{{ route('profile.addresses') }}" class="flex-1 px-4 py-2 border border-gray-300 dark:border-[#404854] text-gray-700 dark:text-[#cbd5e1] rounded-lg hover:bg-gray-100 dark:hover:bg-[#404854] transition text-center">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 px-4 py-2 bg-[#fb923c] text-white rounded-lg hover:bg-[#e6951b] transition font-medium">
                        {{ isset($address) ? 'Simpan Perubahan' : 'Tambah Alamat' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
