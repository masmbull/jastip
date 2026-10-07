@extends('layouts.admin')

@section('title', (isset($coupon) ? 'Edit' : 'Tambah') . ' Kupon | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">{{ isset($coupon) ? 'Edit Kupon' : 'Tambah Kupon' }}</h1>
        <a href="{{ route('admin.coupons.index') }}" class="text-sm text-orange-600 hover:underline">Kembali</a>
    </div>

    <form method="POST"
          action="{{ isset($coupon) ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}"
          class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] p-6 space-y-6 max-w-2xl">
        @csrf
        @isset($coupon) @method('PUT') @endisset

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Kode Kupon *</label>
                <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}" required
                    class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] dark:bg-[#2e323b] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('code') border-red-500 @enderror">
                @error('code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Tipe *</label>
                <select name="type" required
                    class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] dark:bg-[#2e323b] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('type') border-red-500 @enderror">
                    <option value="percentage" @selected(old('type', $coupon->type ?? '') === 'percentage')>Persentase (%)</option>
                    <option value="fixed" @selected(old('type', $coupon->type ?? '') === 'fixed')>Nominal (Rp)</option>
                </select>
                @error('type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Nilai Diskon *</label>
                <input type="number" step="0.01" min="0" name="value" value="{{ old('value', $coupon->value ?? '') }}" required
                    class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] dark:bg-[#2e323b] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('value') border-red-500 @enderror">
                @error('value') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Minimal Belanja</label>
                <input type="number" step="0.01" min="0" name="minimum_purchase" value="{{ old('minimum_purchase', $coupon->minimum_purchase ?? '') }}"
                    class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] dark:bg-[#2e323b] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
<div>
                <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Maks. Diskon</label>
                <input type="number" step="0.01" min="0" name="maximum_discount" value="{{ old('maximum_discount', $coupon->maximum_discount ?? '') }}"
                    class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] dark:bg-[#2e323b] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Batas Pemakaian</label>
                <input type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}"
                    class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] dark:bg-[#2e323b] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Berlaku Dari *</label>
                <input type="date" name="valid_from" value="{{ old('valid_from', isset($coupon) ? $coupon->valid_from?->format('Y-m-d') : '') }}" required
                    class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] dark:bg-[#2e323b] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('valid_from') border-red-500 @enderror">
                @error('valid_from') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Berlaku Sampai *</label>
                <input type="date" name="valid_to" value="{{ old('valid_to', isset($coupon) ? $coupon->valid_to?->format('Y-m-d') : '') }}" required
                    class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] dark:bg-[#2e323b] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('valid_to') border-red-500 @enderror">
                @error('valid_to') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Deskripsi</label>
            <textarea name="description" rows="2"
                class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] dark:bg-[#2e323b] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('description', $coupon->description ?? '') }}</textarea>
        </div>

        @isset($coupon)
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active))
                class="w-4 h-4 text-orange-600 border-[#E2E8F0] rounded">
            <span class="text-sm text-[#1E293B] dark:text-[#f1f5f9]">Kupon aktif</span>
        </label>
        @endisset

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2E8F0] dark:border-[#404854]">
            <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 text-sm text-[#94A3B8] hover:text-[#1E293B] dark:hover:text-[#f1f5f9]">Batal</a>
            <button type="submit" class="px-6 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg shadow-sm">Simpan</button>
        </div>
    </form>
</div>
@endsection