@extends('layouts.app')

@section('title', (isset($address) ? 'Edit' : 'Tambah') . ' Alamat | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <a href="{{ route('profile.addresses') }}" class="inline-flex items-center gap-1 text-sm font-medium text-[#06B6D4] hover:underline focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">
                <x-icon name="arrow-left" class="w-4 h-4" /> Kembali ke Alamat
            </a>
        </div>

        <x-card>
            <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-6">
                {{ isset($address) ? 'Edit Alamat' : 'Tambah Alamat Baru' }}
            </h1>

            <form method="POST" action="{{ isset($address) ? route('addresses.update', $address) : route('addresses.store') }}" class="space-y-5">
                @csrf
                @if(isset($address))
                    @method('PUT')
                @endif

                {{-- Label --}}
                <div>
                    <label for="label" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">Label (Rumah, Kantor, dll)</label>
                    <input type="text" id="label" name="label" value="{{ old('label', $address->label ?? '') }}" maxlength="50"
                           placeholder="Contoh: Rumah, Kantor, Toko"
                           class="w-full px-4 py-2.5 bg-[#F8FAFC] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300">
                    @error('label')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Full Name --}}
                <div>
                    <label for="full_name" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">Nama Lengkap *</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $address->full_name ?? '') }}" required
                           class="w-full px-4 py-2.5 bg-[#F8FAFC] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300">
                    @error('full_name')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">No. Telepon *</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $address->phone ?? '') }}" required
                           placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-2.5 bg-[#F8FAFC] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300">
                    @error('phone')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Street Address --}}
                <div>
                    <label for="street_address" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">Alamat Lengkap *</label>
                    <textarea id="street_address" name="street_address" required rows="3"
                              class="w-full px-4 py-2.5 bg-[#F8FAFC] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300"
                              placeholder="Jalan, nomor rumah, RT/RW, dll">{{ old('street_address', $address->street_address ?? '') }}</textarea>
                    @error('street_address')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- City --}}
                <div>
                    <label for="city" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">Kota/Kabupaten *</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $address->city ?? '') }}" required
                           class="w-full px-4 py-2.5 bg-[#F8FAFC] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300">
                    @error('city')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Province --}}
                <div>
                    <label for="province" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">Provinsi *</label>
                    <input type="text" id="province" name="province" value="{{ old('province', $address->province ?? '') }}" required
                           class="w-full px-4 py-2.5 bg-[#F8FAFC] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300">
                    @error('province')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Postal Code --}}
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">Kode Pos *</label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" required
                           placeholder="12345" inputmode="numeric"
                           class="w-full px-4 py-2.5 bg-[#F8FAFC] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] num focus:outline-none focus:ring-2 focus:ring-orange-300">
                    @error('postal_code')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-2">Catatan (Opsional)</label>
                    <textarea id="notes" name="notes" rows="2" maxlength="500"
                              class="w-full px-4 py-2.5 bg-[#F8FAFC] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300"
                              placeholder="Contoh: Sebelah minimarket, pintu berwarna biru">{{ old('notes', $address->notes ?? '') }}</textarea>
                    @error('notes')
                        <p class="text-red-600 dark:text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Set as Default --}}
                <div class="flex items-center gap-3 p-4 bg-[#F8FAFC] dark:bg-[#2e323b] rounded-lg">
                    <input type="checkbox" id="is_default" name="is_default" value="1"
                           {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}
                           class="w-5 h-5 rounded cursor-pointer">
                    <label for="is_default" class="text-sm text-[#1E293B] dark:text-[#cbd5e1] cursor-pointer">
                        Jadikan alamat default
                    </label>
                </div>

                {{-- Submit --}}
                <div class="flex flex-col-reverse sm:flex-row gap-3 pt-6 border-t border-[#E2E8F0] dark:border-[#404854]">
                    <x-btn as="a" :href="route('profile.addresses')" variant="ghost" class="flex-1">Batal</x-btn>
                    <x-btn type="submit" variant="brand" class="flex-1">
                        {{ isset($address) ? 'Simpan Perubahan' : 'Tambah Alamat' }}
                    </x-btn>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection