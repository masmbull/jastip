@extends('layouts.app')
@section('title', 'Checkout | Titipan Kamu')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-6 md:mb-8">Isi Data Pesanan</h1>

    <form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
            <!-- Form Column -->
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm p-4 md:p-6 space-y-4 transition-colors">
                    <h2 class="text-lg md:text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4">Data Diri</h2>
                    
                    <!-- Nama -->
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Nama Lengkap *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-3 md:px-4 py-2 md:py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 dark:focus:ring-orange-500/50 transition-colors @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-xs md:text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- WhatsApp -->
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Nomor WhatsApp *</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" required
                            placeholder="081234567890"
                            class="w-full px-3 md:px-4 py-2 md:py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 dark:focus:ring-orange-500/50 transition-colors @error('whatsapp') border-red-500 @enderror">
                        @error('whatsapp') <p class="text-xs md:text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email (opsional) -->
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Email (Opsional)</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}"
                            placeholder="kamu@email.com"
                            class="w-full px-3 md:px-4 py-2 md:py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 dark:focus:ring-orange-500/50 transition-colors @error('email') border-red-500 @enderror">
                        <p class="text-xs text-[#94A3B8] mt-1">Untuk kirim konfirmasi & status pesanan via email.</p>
                        @error('email') <p class="text-xs md:text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Alamat Lengkap *</label>
                        <textarea name="address" rows="3" required
                            placeholder="Rumah, kantor, atau tempat lain..."
                            class="w-full px-3 md:px-4 py-2 md:py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 dark:focus:ring-orange-500/50 resize-none transition-colors @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                        @error('address') <p class="text-xs md:text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Catatan (Opsional)</label>
                        <textarea name="notes" rows="2"
                            placeholder="Warna, ukuran, atau pesan khusus..."
                            class="w-full px-3 md:px-4 py-2 md:py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 dark:focus:ring-orange-500/50 resize-none transition-colors">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Shipping Method -->
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Metode Pengiriman *</label>
                        <select name="shipping_method" required
                            class="w-full px-3 md:px-4 py-2 md:py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 dark:focus:ring-orange-500/50 transition-colors @error('shipping_method') border-red-500 @enderror">
                            <option value="">Pilih metode</option>
                            <option value="Reguler" {{ old('shipping_method') == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                            <option value="Cepat" {{ old('shipping_method') == 'Cepat' ? 'selected' : '' }}>Cepat</option>
                            <option value="Instant" {{ old('shipping_method') == 'Instant' ? 'selected' : '' }}>Instant (COD)</option>
                            <option value="Ambil di tempat" {{ old('shipping_method') == 'Ambil di tempat' ? 'selected' : '' }}>Ambil di Tempat</option>
                        </select>
                        @error('shipping_method') <p class="text-xs md:text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Summary Column -->
            <div class="md:col-span-1">
                <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm p-4 md:p-6 transition-colors sticky top-4 md:top-6">
                    <h3 class="text-base md:text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4">Konfirmasi Pesanan</h3>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Kode Kupon (opsional)</label>
                        <input type="text" name="coupon_code" value="{{ old('coupon_code') }}"
                            placeholder="Mis. HEMAT10"
                            class="w-full px-3 py-2 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg uppercase focus:outline-none focus:ring-2 focus:ring-orange-300 dark:focus:ring-orange-500/50 transition-colors">
                    </div>

                    <div class="space-y-3 mb-4 text-xs md:text-sm">
                        <div class="flex justify-between text-[#64748B] dark:text-[#cbd5e1]">
                            <span>Produk ({{ $cartItems->sum('quantity') }} item)</span>
                            <span class="font-medium">{{ format_price($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-[#64748B] dark:text-[#cbd5e1]">
                            <span>Ongkir</span>
                            <span class="font-medium">{{ format_price(setting('shipping_cost', 0)) }}</span>
                        </div>
                        @if(($fee ?? 0) > 0)
                        <div class="flex justify-between text-[#64748B] dark:text-[#cbd5e1]">
                            <span>Biaya Fee</span>
                            <span class="font-medium">{{ format_price($fee) }}</span>
                        </div>
                        @endif
                        <div class="border-t border-[#E2E8F0] dark:border-[#404854] pt-3 flex justify-between text-base font-bold">
                            <span class="text-[#1E293B] dark:text-[#f1f5f9]">Total</span>
                            <span class="text-orange-600">{{ format_price($subtotal + setting('shipping_cost', 0) + ($fee ?? 0)) }}</span>
                        </div>
                    </div>

                    <button type="submit"
                        id="checkoutBtn"
                        class="w-full px-4 md:px-6 py-3 md:py-4 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-full shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-300 text-sm md:text-base">
                        Lanjut Bayar via QRIS
                    </button>
                    <p class="text-xs text-[#94A3B8] text-center mt-3">
                        Pembayaran langsung QRIS. Setelah submit, scan QRIS & konfirmasi via WhatsApp.
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 mt-4">
                    <h3 class="text-sm font-bold text-[#1E293B] mb-3 flex items-center gap-2">
                        <span class="inline-block w-2 h-2 bg-orange-500 rounded-full"></span>
                        Metode Pembayaran
                    </h3>
                    <div class="flex items-center gap-3 p-3 bg-orange-50 rounded-lg border border-orange-200">
                        <svg class="w-7 h-7 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                            <rect x="14" y="14" width="3" height="3"></rect>
                            <rect x="18" y="18" width="3" height="3"></rect>
                            <rect x="14" y="18" width="3" height="3"></rect>
                        </svg>
                        <div>
                            <p class="font-bold text-orange-700 text-sm">QRIS</p>
                            <p class="text-xs text-orange-600">Scan pakai e-wallet apa saja</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const btn = e.submitter;
        btn.disabled = true;
        btn.innerHTML = '<span class="flex items-center justify-center gap-2"><svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Sedang menyiapkan...</span>';
    });
    </script>
</div>
@endsection