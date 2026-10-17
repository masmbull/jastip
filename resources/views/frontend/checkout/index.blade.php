@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-6 md:mb-8">Isi Data Pesanan</h1>

    <form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm" x-data="ongkirChecker()">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
            <!-- Form Column -->
            <div class="md:col-span-2 space-y-6">
                <x-card class="space-y-4">
                    <h2 class="text-lg md:text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4">Data Diri</h2>
                    
                    <!-- Nama -->
                    <div>
                        <label for="co-name" class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Nama Lengkap *</label>
                        <input type="text" id="co-name" name="name" value="{{ old('name') }}" required autocomplete="name"
                            class="nb-input w-full @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-xs md:text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- WhatsApp -->
                    <div>
                        <label for="co-wa" class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Nomor WhatsApp *</label>
                        <input type="text" id="co-wa" name="whatsapp" value="{{ old('whatsapp') }}" required autocomplete="tel"
                            placeholder="081234567890"
                            class="nb-input w-full @error('whatsapp') border-red-500 @enderror">
                        @error('whatsapp') <p class="text-xs md:text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email (opsional) -->
                    <div>
                        <label for="co-email" class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Email (Opsional)</label>
                        <input type="email" id="co-email" name="email" value="{{ old('email', auth()->user()?->email) }}" autocomplete="email"
                            placeholder="kamu@email.com"
                            class="nb-input w-full @error('email') border-red-500 @enderror">
                        <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Untuk kirim konfirmasi &amp; status pesanan via email.</p>
                        @error('email') <p class="text-xs md:text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label for="co-address" class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Alamat Lengkap *</label>
                        <textarea id="co-address" name="address" rows="3" required autocomplete="street-address"
                            placeholder="Rumah, kantor, atau tempat lain..."
                            class="nb-input w-full @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                        @error('address') <p class="text-xs md:text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label for="co-notes" class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Catatan (Opsional)</label>
                        <textarea id="co-notes" name="notes" rows="2"
                            placeholder="Warna, ukuran, atau pesan khusus..."
                            class="nb-input w-full">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Kota & Ekspedisi + Cek Ongkir -->
                    <div class="p-4 rounded-lg border border-[#E2E8F0] dark:border-[#404854] bg-[#F8FAFC] dark:bg-[#2e323b]">
                        <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2 flex items-center gap-2">
                            <x-icon name="truck" class="w-4 h-4 text-orange-500 dark:text-orange-400" />
                            Kota Tujuan & Ekspedisi *
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="co-city" class="sr-only">Kota tujuan</label>
                                <select id="co-city" name="city" x-model="city" @change="check()" required
                                    class="nb-input w-full @error('city') border-red-500 @enderror">
                                    <option value="">Pilih kota tujuan</option>
                                    @foreach($cities as $name => $meta)
                                        <option value="{{ $name }}" @selected(old('city') === $name)>{{ $name }}, {{ $meta['province'] }}</option>
                                    @endforeach
                                </select>
                                @error('city') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="co-courier" class="sr-only">Ekspedisi</label>
                                <select id="co-courier" name="courier" x-model="courier" @change="check()" required
                                    class="nb-input w-full @error('courier') border-red-500 @enderror">
                                    <option value="">Pilih ekspedisi</option>
                                    <template x-for="r in okRows" :key="r.code">
                                        <option :value="r.code" x-text="r.name + ' · ' + (r.etd || '') + ' · ' + r.price_formatted"></option>
                                    </template>
                                </select>
                                @error('courier') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <input type="hidden" name="weight" :value="weight">

                        <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-2 flex items-center gap-1">
                            <x-icon name="cube" class="w-4 h-4" />
                            Estimasi berat: <span x-text="weight + ' kg'"></span>
                        </p>

{{-- Hasil ongkir --}}
                        <div class="mt-3" x-show="loading" x-cloak>
                            <x-spinner variant="brand" label="Menghitung ongkir..." class="text-xs text-[#64748B] dark:text-[#cbd5e1]" />
                        </div>
                        <div class="mt-3" x-show="!loading && error" x-cloak>
                            <span class="text-xs text-red-500" x-text="error"></span>
                        </div>
                        <div class="mt-3" x-show="!loading && !error && selected" x-cloak>
                            <div class="rounded-lg bg-[#FFC800]/10 border border-[#141414] px-3 py-2 text-sm text-[#141414] dark:text-[#FAF6EF] dark:text-[#141414] dark:text-[#FAF6EF]">
                                Ongkir <strong x-text="selected.name"></strong>: <strong x-text="selectedPrice"></strong>
                                <span class="text-xs" x-text="selected ? ' · ' + (selected.etd || '') : ''"></span>
                            </div>
                        </div>
                        <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-2">Ongkir dihitung otomatis berdasarkan kota, ekspedisi, dan berat. Admin konfirmasi tarif final saat pengiriman.</p>
                    </div>
                </x-card>
            </div>

            <!-- Summary Column -->
            <div class="md:col-span-1">
                <x-card class="md:p-6 sticky top-4 md:top-6">
                    <h3 class="text-base md:text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4">Konfirmasi Pesanan</h3>

                    <div class="mb-4">
                        <label for="co-coupon" class="block text-xs font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Kode Kupon (opsional)</label>
                        <input type="text" id="co-coupon" name="coupon_code" value="{{ old('coupon_code') }}"
                            placeholder="Mis. HEMAT10"
                            class="nb-input w-full">
                    </div>

                    <div class="space-y-3 mb-4 text-xs md:text-sm">
                        <div class="flex justify-between text-[#64748B] dark:text-[#cbd5e1]">
                            <span>Produk (<span class="num">{{ $cartItems->sum('quantity') }}</span> item)</span>
                            <span class="font-medium num">{{ format_price($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-[#64748B] dark:text-[#cbd5e1]">
                            <span>Ongkir</span>
                            <span class="font-medium num" x-text="selectedPrice || 'Pilih ekspedisi'">Pilih ekspedisi</span>
                        </div>
                        @if(($fee ?? 0) > 0)
                        <div class="flex justify-between text-[#64748B] dark:text-[#cbd5e1]">
                            <span>Biaya Fee</span>
                            <span class="font-medium num">{{ format_price($fee) }}</span>
                        </div>
                        @endif
                        <div class="border-t border-[#E2E8F0] dark:border-[#404854] pt-3 flex justify-between text-base font-bold">
                            <span class="text-[#1E293B] dark:text-[#f1f5f9]">Total</span>
                            <span class="text-orange-600 dark:text-orange-400 num" x-text="totalFormatted">{{ format_price($subtotal + ($fee ?? 0)) }}</span>
                        </div>
                    </div>

                    <button type="submit"
                        id="checkoutBtn"
                        class="w-full px-4 md:px-6 py-3 md:py-4 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-full shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-300 text-sm md:text-base">
                        Lanjut Bayar via QRIS
                    </button>
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] text-center mt-3">
                        Pembayaran langsung QRIS. Setelah submit, scan QRIS &amp; konfirmasi via WhatsApp.
                    </p>
                </x-card>

                <x-card class="mt-4">
                    <h3 class="text-sm font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-3 flex items-center gap-2">
                        <span class="inline-block w-2 h-2 bg-orange-500 rounded-full" aria-hidden="true"></span>
                        Metode Pembayaran
                    </h3>
                    <div class="flex items-center gap-3 p-3 bg-orange-50 dark:bg-orange-500/10 rounded-lg border border-orange-200 dark:border-orange-500/30">
                        <x-icon name="qr-code" class="w-7 h-7 text-orange-600 dark:text-orange-400" />
                        <div>
                            <p class="font-bold text-orange-700 dark:text-orange-300 text-sm">QRIS</p>
                            <p class="text-xs text-orange-600 dark:text-orange-400">Scan pakai e-wallet apa saja</p>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </form>

    <script>
    function ongkirChecker() {
        return {
            city: '{{ old('city') }}',
            courier: '{{ old('courier') }}',
            weight: {{ (float) old('weight', $defaultWeight) }},
            rows: [],
            loading: false,
            error: '',
            get okRows() { return this.rows.filter(r => r.ok); },
            get selected() { return this.rows.find(r => r.code === this.courier && r.ok) || null; },
            get selectedPrice() { return this.selected ? this.selected.price_formatted : ''; },
            get totalFormatted() {
                const base = {{ (int) $subtotal + (int) ($fee ?? 0) }};
                const ship = this.selected ? this.selected.price : 0;
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(base + ship);
            },
            init() { if (this.city) this.check(); },
            async check() {
                if (!this.city) { this.rows = []; return; }
                this.loading = true; this.error = '';
                try {
                    const url = '{{ route('shipping.check') }}?city=' + encodeURIComponent(this.city) + '&weight=' + this.weight;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    this.rows = data.rows || [];
                    if (!this.selected) this.courier = this.okRows.length ? this.okRows[0].code : '';
                } catch (e) {
                    this.error = 'Gagal menghitung ongkir. Coba lagi.';
                } finally {
                    this.loading = false;
                }
            }
        };
    }

    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const btn = e.submitter;
        btn.disabled = true;
        btn.innerHTML = '<span class="flex items-center justify-center gap-2"><svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Sedang menyiapkan...</span>';
    });
    </script>
</div>
@endsection