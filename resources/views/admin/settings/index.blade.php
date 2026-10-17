@extends('layouts.admin')
@section('title', 'Pengaturan | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">
    <x-page-header title="Pengaturan" subtitle="Kelola identitas brand, kontak, dan konfigurasi toko" class="animate-fade-up">
        <x-btn as="a" :href="route('home')" target="_blank" variant="outline" icon="external-link">Lihat di halaman depan</x-btn>
    </x-page-header>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <x-card class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 animate-fade-up" style="animation-delay: 80ms">
            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Semua perubahan akan langsung diterapkan ke situs Anda.</p>
            <x-btn type="submit" icon="check">Simpan Pengaturan</x-btn>
        </x-card>

        {{-- Branding --}}
        <x-card class="animate-fade-up" style="animation-delay: 120ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="sparkles" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Branding
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Nama Brand</label>
                    <input type="text" name="brand_name" value="{{ old('brand_name', setting('brand_name')) }}"
                           class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Tagline</label>
                    <input type="text" name="brand_tagline" value="{{ old('brand_tagline', setting('brand_tagline')) }}"
                           class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Pemilik Brand</label>
                    <input type="text" name="brand_owner" value="{{ old('brand_owner', setting('brand_owner')) }}"
                           class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
            </div>
        </x-card>

        {{-- Logo & Identities --}}
        <x-card class="animate-fade-up" style="animation-delay: 140ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="photo" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Logo &amp; Brand Identities
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Logo</label>
                    <input type="file" name="brand_logo"
                           class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Disarankan ukuran 200×60px, format PNG transparan.</p>
                    @if(setting('brand_logo'))
                        <div class="mt-2 flex items-center gap-3">
                            <img src="{{ asset('storage/' . setting('brand_logo')) }}" class="h-12 w-auto rounded" alt="Logo saat ini">
                            <p class="text-xs text-[#64748B] dark:text-[#cbd5e1]">Logo saat ini</p>
                        </div>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Favicon</label>
                    <input type="file" name="brand_favicon" accept="image/png,image/svg+xml"
                           class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Disarankan 32×32px atau 16×16px, format PNG.</p>
                    @if(setting('brand_favicon'))
                        <div class="mt-2 flex items-center gap-3">
                            <img src="{{ asset('storage/' . setting('brand_favicon')) }}" class="h-6 w-auto rounded" alt="Favicon saat ini">
                            <p class="text-xs text-[#64748B] dark:text-[#cbd5e1]">Favicon saat ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </x-card>

        {{-- Colors --}}
        <x-card class="animate-fade-up" style="animation-delay: 160ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="sparkles" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Warna Brand
            </h2>
            @php
                $brandColors = [
                    ['key' => 'primary_color', 'label' => 'Warna Teks Utama', 'fallback' => '#141414'],
                    ['key' => 'secondary_color', 'label' => 'Warna Teks Sekunder', 'fallback' => '#64748B'],
                    ['key' => 'accent_color', 'label' => 'Warna Aksen', 'fallback' => '#FFC800'],
                ];
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach ($brandColors as $color)
                    @php
                        $value = (string) old($color['key'], setting($color['key'], $color['fallback']));
                        if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) {
                            $value = $color['fallback'];
                        }
                    @endphp
                    <div>
                        <label for="{{ $color['key'] }}" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">{{ $color['label'] }}</label>
                        <div class="flex items-center gap-3">
                            <input type="color" id="{{ $color['key'] }}" name="{{ $color['key'] }}"
                                   value="{{ $value }}"
                                   class="w-12 h-12 shrink-0 rounded border border-[#E2E8F0] dark:border-[#404854] cursor-pointer"
                                   onchange="this.nextElementSibling.style.borderLeftColor = this.value; this.nextElementSibling.textContent = this.value.toUpperCase()">
                            {{-- Chip pakai border kiri, bukan latar, biar teks hex tetap terbaca
                                 berapa pun warna yang dipilih. --}}
                            <span class="font-mono text-xs px-2 py-2 rounded border border-[#E2E8F0] dark:border-[#404854] border-l-[10px] border-l-[#141414] text-[#1E293B] dark:text-[#f1f5f9]"
                                  style="border-left-color: {{ $value }}">{{ strtoupper($value) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-3">
                Ketiga warna ini mengganti warna yang sama di seluruh situs. Teks utama dan sekunder
                berlaku di mode terang karena mode gelap memakai palet kontrasnya sendiri; aksen
                berlaku di kedua mode. Pilih warna yang kontras dengan latarnya supaya teks tetap
                terbaca.
            </p>
        </x-card>

        {{-- Contact --}}
        <x-card class="animate-fade-up" style="animation-delay: 180ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="phone" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Kontak
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Nomor WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', setting('whatsapp')) }}"
                           placeholder="Contoh: 628123456789"
                           class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Format: negara+angka tanpa spasi/dash.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', setting('email')) }}"
                           class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Alamat</label>
                    <textarea name="address" rows="2"
                              class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('address', setting('address')) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Username Instagram</label>
                    <input type="text" name="instagram" value="{{ old('instagram', setting('instagram')) }}"
                           placeholder="tanpa @"
                           class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Username TikTok</label>
                    <input type="text" name="tiktok" value="{{ old('tiktok', setting('tiktok')) }}"
                           placeholder="username"
                           class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
            </div>
        </x-card>

        {{-- Site Info --}}
        <x-card class="animate-fade-up" style="animation-delay: 200ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="chat" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Informasi Situs
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Teks Footer</label>
                    <textarea name="footer_text" rows="2"
                              class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('footer_text', setting('footer_text')) }}</textarea>
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Teks yang muncul di footer halaman depan.</p>
                </div>
            </div>
        </x-card>

        {{-- SEO --}}
        <x-card class="animate-fade-up" style="animation-delay: 220ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="search" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> SEO
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Meta Description</label>
                    <textarea name="meta_description" rows="3"
                              class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('meta_description', setting('meta_description')) }}</textarea>
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Deskripsi untuk meta tag dan sosial media preview.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Meta Keywords</label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords', setting('meta_keywords')) }}"
                           placeholder="jastip, nitip, parfum, tumbler"
                           class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
            </div>
        </x-card>

        {{-- Business --}}
        <x-card class="animate-fade-up" style="animation-delay: 240ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="clipboard" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Pengaturan Bisnis
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Biaya Ongkir Default (Rp)</label>
                    <input type="number" name="shipping_cost" value="{{ old('shipping_cost', setting('shipping_cost', 0)) }}"
                           min="0" step="1000"
                           class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Ongkir perkiraan di halaman keranjang &amp; produk. Ongkir final dihitung dari ekspedisi yang dipilih saat checkout.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Minimum Order (Rp)</label>
                    <input type="number" name="minimum_order" value="{{ old('minimum_order', setting('minimum_order', 0)) }}"
                           min="0" step="1000"
                           class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Set Biaya Fee Global (Rp)</label>
                    <input type="number" name="setbiaya_fee" value="{{ old('setbiaya_fee', setting('setbiaya_fee', 0)) }}"
                           min="0" step="500"
                           class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Fee layanan global per produk. Dikenakan ke semua order.</p>
                </div>
            </div>
        </x-card>

        {{-- QRIS Payment --}}
        <x-card class="animate-fade-up" style="animation-delay: 260ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="credit-card" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Pembayaran QRIS
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer min-h-[44px] p-2 -m-2 rounded-lg hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b]">
                        <input type="hidden" name="qris_enabled" value="0">
                        <input type="checkbox" name="qris_enabled" value="1"
                               @checked((string) old('qris_enabled', setting('qris_enabled', '1')) === '1')
                               class="w-5 h-5 accent-[#EAB308]">
                        <span class="text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9]">Aktifkan Pembayaran QRIS</span>
                    </label>
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Dimatikan = kode QRIS disembunyikan dari konfirmasi, halaman bayar, dan cetak invoice. Pelanggan tetap bisa pesan, konfirmasinya lewat WhatsApp.</p>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Gambar QRIS (Static QR)</label>
                    <input type="file" name="qris_image" accept="image/*"
                           class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Upload QRIS statis dari e-wallet atau bank kamu. Akan ditampilkan saat customer checkout.</p>
                    @if(setting('qris_image'))
                        <div class="mt-2 flex items-center gap-3">
                            <img src="{{ asset('storage/' . setting('qris_image')) }}" class="h-24 w-auto rounded border border-[#E2E8F0] dark:border-[#404854]" alt="QRIS saat ini">
                            <p class="text-xs text-[#64748B] dark:text-[#cbd5e1]">QRIS saat ini</p>
                        </div>
                    @endif
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Nama Merchant (opsional)</label>
                    <input type="text" name="qris_merchant_name" value="{{ old('qris_merchant_name', setting('qris_merchant_name', setting('brand_name'))) }}"
                           placeholder="NITIP DI END"
                           class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Teks merchant yang ditampilkan di bawah QRIS.</p>
                </div>
            </div>
        </x-card>

        <div class="flex justify-end pt-4">
            <x-btn type="submit" icon="check">Simpan Semua Pengaturan</x-btn>
        </div>
    </form>
</div>
@endsection