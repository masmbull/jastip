@extends('layouts.admin')
@section('title', 'Pengaturan | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">
    <x-page-header title="Pengaturan" subtitle="Kelola identitas brand, kontak, dan konfigurasi toko" class="animate-fade-up">
        <x-btn as="a" :href="route('home')" target="_blank" variant="outline" icon="external-link">Lihat di halaman depan</x-btn>
    </x-page-header>

    @if($errors->any())
        <div id="settings-errors" role="alert" tabindex="-1"
             class="rounded-[8px] border-2 border-red-800 bg-red-50 px-4 py-3 text-sm text-red-900 dark:bg-red-950 dark:text-red-100">
            <p class="font-bold">Pengaturan belum disimpan. Periksa pesan berikut:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <nav aria-label="Bagian pengaturan" class="sticky top-0 z-20 -mx-4 overflow-x-auto border-y-2 border-[#141414] bg-[#FAF6EF] px-4 py-2 dark:border-[#FAF6EF] dark:bg-[#121212] sm:mx-0 sm:rounded-[8px] sm:border-2">
        <div class="flex w-max min-w-full gap-2">
            @foreach([
                ['branding', 'Brand'],
                ['media', 'Logo & favicon'],
                ['colors', 'Warna'],
                ['contact', 'Kontak'],
                ['site', 'Situs'],
                ['seo', 'SEO'],
                ['business', 'Bisnis'],
                ['payment', 'QRIS'],
                ['bank', 'Rekening'],
                ['whatsapp', 'WhatsApp'],
            ] as [$id, $label])
                <a href="#settings-{{ $id }}"
                   class="inline-flex min-h-[44px] items-center whitespace-nowrap rounded-[6px] border-2 border-[#141414] bg-white px-3 text-sm font-bold text-[#141414] hover:bg-[#FFC800] focus:outline-none focus:ring-2 focus:ring-[#141414] dark:border-[#FAF6EF] dark:bg-[#1E1E1E] dark:text-[#FAF6EF] dark:focus:ring-[#FFC800]">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </nav>

    <form id="settings-form" data-settings-form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <x-card class="sticky top-[60px] z-10 flex flex-col gap-3 border-2 border-[#141414] bg-white sm:flex-row sm:items-center sm:justify-between dark:border-[#FAF6EF] dark:bg-[#1E1E1E]">
            <p id="settings-save-status" role="status" aria-live="polite" class="text-sm font-medium text-[#475569] dark:text-[#cbd5e1]">
                Semua perubahan sudah disimpan.
            </p>
            <x-btn type="submit" class="settings-submit min-h-[44px]" icon="check">
                <span data-submit-label>Simpan Pengaturan</span>
            </x-btn>
        </x-card>

        {{-- Branding --}}
        <x-card id="settings-branding" class="scroll-mt-32 animate-fade-up" style="animation-delay: 120ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="sparkles" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Branding
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="setting-brand-name" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Nama Brand</label>
                    <input id="setting-brand-name" type="text" name="brand_name" value="{{ old('brand_name', setting('brand_name')) }}"
                           class="nb-input w-full">
                </div>
                <div>
                    <label for="setting-brand-tagline" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Tagline</label>
                    <input id="setting-brand-tagline" type="text" name="brand_tagline" value="{{ old('brand_tagline', setting('brand_tagline')) }}"
                           class="nb-input w-full">
                </div>
                <div class="sm:col-span-2">
                    <label for="setting-brand-owner" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Pemilik Brand</label>
                    <input id="setting-brand-owner" type="text" name="brand_owner" value="{{ old('brand_owner', setting('brand_owner')) }}"
                           class="nb-input w-full">
                </div>
            </div>
        </x-card>

        {{-- Logo & Identities --}}
        <x-card id="settings-media" class="scroll-mt-32 animate-fade-up" style="animation-delay: 140ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="photo" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Logo &amp; Brand Identities
            </h2>
            <div class="space-y-4">
                <div>
                    <label for="setting-brand-logo" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Logo</label>
                    <input id="setting-brand-logo" type="file" name="brand_logo" accept="image/png,image/jpeg,image/svg+xml,image/gif,image/webp,image/x-icon"
                           class="nb-input w-full">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">PNG, JPG, SVG, GIF, WEBP, atau ICO. Disarankan PNG transparan ukuran 200×60px. Maksimal 10 MB.</p>
                    @error('brand_logo')
                        <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                    @if(setting('brand_logo'))
                        @if($mediaStatus['brand_logo']['public'] ?? false)
                            <div data-image-preview="brand_logo" class="mt-2 flex items-center gap-3">
                                <img data-preview-image src="{{ media_url(setting('brand_logo')) }}" class="h-12 w-auto rounded" alt="Pratinjau logo">
                                <p data-preview-label class="text-xs text-[#64748B] dark:text-[#cbd5e1]">Logo saat ini</p>
                            </div>
                        @else
                            <p data-media-warning role="status" class="mt-2 text-sm text-[#991B1B] dark:text-red-300">
                                @if($mediaStatus['brand_logo']['stored'] ?? false)
                                    File logo ada, tetapi belum bisa diakses publik. Jalankan perbaikan storage melalui deploy terbaru.
                                @else
                                    File logo tidak ditemukan di server. Upload ulang logo.
                                @endif
                            </p>
                            <div data-image-preview="brand_logo" class="mt-2 hidden items-center gap-3">
                                <img data-preview-image class="h-12 w-auto rounded" alt="Pratinjau logo yang dipilih">
                                <p data-preview-label class="text-xs text-[#64748B] dark:text-[#cbd5e1]"></p>
                            </div>
                        @endif
                    @else
                        <div data-image-preview="brand_logo" class="mt-2 hidden items-center gap-3">
                            <img data-preview-image class="h-12 w-auto rounded" alt="Pratinjau logo yang dipilih">
                            <p data-preview-label class="text-xs text-[#64748B] dark:text-[#cbd5e1]"></p>
                        </div>
                    @endif
                </div>
                <div>
                    <label for="setting-brand-favicon" class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Favicon</label>
                    <input id="setting-brand-favicon" type="file" name="brand_favicon" accept="image/png,image/jpeg,image/svg+xml,image/gif,image/webp,image/x-icon"
                           class="nb-input w-full">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">PNG, JPG, SVG, GIF, WEBP, atau ICO. Disarankan 32×32px atau 16×16px, format PNG. Maksimal 10 MB.</p>
                    @error('brand_favicon')
                        <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                    @if(setting('brand_favicon'))
                        @if($mediaStatus['brand_favicon']['public'] ?? false)
                        <div data-image-preview="brand_favicon" class="mt-2 flex items-center gap-3">
                            <img data-preview-image src="{{ media_url(setting('brand_favicon')) }}" class="h-6 w-auto rounded" alt="Pratinjau favicon">
                            <p data-preview-label class="text-xs text-[#64748B] dark:text-[#cbd5e1]">Favicon saat ini</p>
                        </div>
                        @else
                            <p data-media-warning role="status" class="mt-2 text-sm text-red-700 dark:text-red-300">Favicon tidak dapat diakses dari penyimpanan publik. Unggah file baru.</p>
                            <div data-image-preview="brand_favicon" class="mt-2 hidden items-center gap-3">
                                <img data-preview-image class="h-6 w-auto rounded" alt="Pratinjau favicon yang dipilih">
                                <p data-preview-label class="text-xs text-[#64748B] dark:text-[#cbd5e1]"></p>
                            </div>
                        @endif
                    @else
                        <div data-image-preview="brand_favicon" class="mt-2 hidden items-center gap-3">
                            <img data-preview-image class="h-6 w-auto rounded" alt="Pratinjau favicon yang dipilih">
                            <p data-preview-label class="text-xs text-[#64748B] dark:text-[#cbd5e1]"></p>
                        </div>
                    @endif
                </div>
            </div>
        </x-card>

        {{-- Colors --}}
        <x-card id="settings-colors" class="scroll-mt-32 animate-fade-up" style="animation-delay: 160ms">
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
        <x-card id="settings-contact" class="scroll-mt-32 animate-fade-up" style="animation-delay: 180ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="phone" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Kontak
            </h2>
            <div class="space-y-4">
                <div>
                    <label for="setting-whatsapp" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Nomor WhatsApp</label>
                    <input id="setting-whatsapp" type="tel" name="whatsapp" value="{{ old('whatsapp', setting('whatsapp')) }}"
                           inputmode="tel" autocomplete="tel" maxlength="16"
                           placeholder="Contoh: 628123456789"
                           class="nb-input w-full">
                    @error('whatsapp')
                        <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Format: negara+angka tanpa spasi/dash.</p>
                </div>
                <div>
                    <label for="setting-email" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Email</label>
                    <input id="setting-email" type="email" name="email" value="{{ old('email', setting('email')) }}"
                           class="nb-input w-full">
                    @error('email')
                        <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="setting-address" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Alamat</label>
                    <textarea id="setting-address" name="address" rows="2"
                              class="nb-input w-full">{{ old('address', setting('address')) }}</textarea>
                </div>
                <div>
                    <label for="setting-instagram" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Username Instagram</label>
                    <input id="setting-instagram" type="text" name="instagram" value="{{ old('instagram', setting('instagram')) }}"
                           placeholder="tanpa @"
                           class="nb-input w-full">
                </div>
                <div>
                    <label for="setting-tiktok" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Username TikTok</label>
                    <input id="setting-tiktok" type="text" name="tiktok" value="{{ old('tiktok', setting('tiktok')) }}"
                           placeholder="username"
                           class="nb-input w-full">
                </div>
            </div>
        </x-card>

        {{-- Site Info --}}
        <x-card id="settings-site" class="scroll-mt-32 animate-fade-up" style="animation-delay: 200ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="chat" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Informasi Situs
            </h2>
            <div class="space-y-4">
                <div>
                    <label for="setting-footer-text" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Teks Footer</label>
                    <textarea id="setting-footer-text" name="footer_text" rows="2"
                              class="nb-input w-full">{{ old('footer_text', setting('footer_text')) }}</textarea>
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Teks yang muncul di footer halaman depan.</p>
                </div>
            </div>
        </x-card>

        {{-- SEO --}}
        <x-card id="settings-seo" class="scroll-mt-32 animate-fade-up" style="animation-delay: 220ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="search" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> SEO
            </h2>
            <div class="space-y-4">
                <div>
                    <label for="setting-meta-description" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Meta Description</label>
                    <textarea id="setting-meta-description" name="meta_description" rows="3"
                              class="nb-input w-full">{{ old('meta_description', setting('meta_description')) }}</textarea>
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Deskripsi untuk meta tag dan sosial media preview.</p>
                </div>
                <div>
                    <label for="setting-meta-keywords" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Meta Keywords</label>
                    <input id="setting-meta-keywords" type="text" name="meta_keywords" value="{{ old('meta_keywords', setting('meta_keywords')) }}"
                           placeholder="jastip, nitip, parfum, tumbler"
                           class="nb-input w-full">
                </div>
            </div>
        </x-card>

        {{-- Business --}}
        <x-card id="settings-business" class="scroll-mt-32 animate-fade-up" style="animation-delay: 240ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="clipboard" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Pengaturan Bisnis
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="setting-shipping-cost" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Biaya Ongkir Default (Rp)</label>
                    <input id="setting-shipping-cost" type="number" name="shipping_cost" value="{{ old('shipping_cost', setting('shipping_cost', 0)) }}"
                           min="0" step="1000"
                           class="nb-input w-full">
                    @error('shipping_cost')
                        <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Ongkir perkiraan di halaman keranjang &amp; produk. Ongkir final dihitung dari ekspedisi yang dipilih saat checkout.</p>
                </div>
                <div>
                    <label for="setting-minimum-order" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Minimum Order (Rp)</label>
                    <input id="setting-minimum-order" type="number" name="minimum_order" value="{{ old('minimum_order', setting('minimum_order', 0)) }}"
                           min="0" step="1000"
                           class="nb-input w-full">
                    @error('minimum_order')
                        <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="setting-global-fee" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Set Biaya Fee Global (Rp)</label>
                    <input id="setting-global-fee" type="number" name="setbiaya_fee" value="{{ old('setbiaya_fee', setting('setbiaya_fee', 0)) }}"
                           min="0" step="500"
                           class="nb-input w-full">
                    @error('setbiaya_fee')
                        <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Fee layanan global per produk. Dikenakan ke semua order.</p>
                </div>
            </div>
        </x-card>

        {{-- QRIS Payment --}}
        <x-card id="settings-payment" class="scroll-mt-32 animate-fade-up" style="animation-delay: 260ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="credit-card" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Pembayaran QRIS
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="setting-qris-enabled" class="flex items-center gap-3 cursor-pointer min-h-[44px] p-2 -m-2 rounded-lg hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b]">
                        <input type="hidden" name="qris_enabled" value="0">
                        <input id="setting-qris-enabled" type="checkbox" name="qris_enabled" value="1"
                               @checked((string) old('qris_enabled', setting('qris_enabled', '1')) === '1')
                               class="w-5 h-5 accent-[#EAB308]">
                        <span class="text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9]">Aktifkan Pembayaran QRIS</span>
                    </label>
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Dimatikan = kode QRIS disembunyikan dari konfirmasi, halaman bayar, dan cetak invoice. Pelanggan tetap bisa pesan, konfirmasinya lewat WhatsApp.</p>
                </div>
                <div class="sm:col-span-2">
                    <label for="setting-qris-image" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Gambar QRIS (Static QR)</label>
                    <input id="setting-qris-image" type="file" name="qris_image" accept="image/png,image/jpeg,image/gif,image/webp"
                           class="nb-input w-full">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">PNG, JPG, GIF, atau WEBP. QRIS statis akan ditampilkan saat checkout. Maksimal 10 MB.</p>
                    @error('qris_image')
                        <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                    @if(setting('qris_image'))
                        @if($mediaStatus['qris_image']['public'] ?? false)
                            <div data-image-preview="qris_image" class="mt-2 flex items-center gap-3">
                                <img data-preview-image src="{{ media_url(setting('qris_image')) }}" class="h-24 w-auto rounded border border-[#E2E8F0] dark:border-[#404854]" alt="Pratinjau QRIS">
                                <p data-preview-label class="text-xs text-[#64748B] dark:text-[#cbd5e1]">QRIS saat ini</p>
                            </div>
                        @else
                            <p data-media-warning role="status" class="mt-2 text-sm text-[#991B1B] dark:text-red-300">
                                @if($mediaStatus['qris_image']['stored'] ?? false)
                                    File QRIS ada, tetapi belum bisa diakses publik. Jalankan perbaikan storage melalui deploy terbaru.
                                @else
                                    File QRIS tidak ditemukan di server. Upload ulang gambar QRIS.
                                @endif
                            </p>
                            <div data-image-preview="qris_image" class="mt-2 hidden items-center gap-3">
                                <img data-preview-image class="h-24 w-auto rounded border border-[#E2E8F0] dark:border-[#404854]" alt="Pratinjau QRIS yang dipilih">
                                <p data-preview-label class="text-xs text-[#64748B] dark:text-[#cbd5e1]"></p>
                            </div>
                        @endif
                    @else
                        <div data-image-preview="qris_image" class="mt-2 hidden items-center gap-3">
                            <img data-preview-image class="h-24 w-auto rounded border border-[#E2E8F0] dark:border-[#404854]" alt="Pratinjau QRIS yang dipilih">
                            <p data-preview-label class="text-xs text-[#64748B] dark:text-[#cbd5e1]"></p>
                        </div>
                    @endif
                </div>
                <div class="sm:col-span-2">
                    <label for="setting-qris-merchant" class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Nama Merchant (opsional)</label>
                    <input id="setting-qris-merchant" type="text" name="qris_merchant_name" value="{{ old('qris_merchant_name', setting('qris_merchant_name', setting('brand_name'))) }}"
                           placeholder="NITIP DI END"
                           class="nb-input w-full">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Teks merchant yang ditampilkan di bawah QRIS.</p>
                </div>
            </div>
        </x-card>

        {{-- Rekening Pembayaran --}}
        <x-card id="settings-bank" class="scroll-mt-32 animate-fade-up" style="animation-delay: 280ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                <x-icon name="banknotes" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Rekening Pembayaran
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1" for="bank_name">Nama Bank</label>
                    <input id="bank_name" type="text" name="bank_name" value="{{ old('bank_name', setting('bank_name')) }}"
                           placeholder="BCA / Mandiri / BRI"
                           class="nb-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1" for="bank_account_number">Nomor Rekening</label>
                    <input id="bank_account_number" type="text" name="bank_account_number" inputmode="numeric"
                           value="{{ old('bank_account_number', setting('bank_account_number')) }}" placeholder="1234567890"
                           class="nb-input w-full">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1" for="bank_account_name">Atas Nama</label>
                    <input id="bank_account_name" type="text" name="bank_account_name" value="{{ old('bank_account_name', setting('bank_account_name')) }}"
                           placeholder="Nama pemilik rekening"
                           class="nb-input w-full">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1" for="qris_max_amount">Batas Nominal QRIS (Rp)</label>
                    <input id="qris_max_amount" type="number" min="0" step="1000" name="qris_max_amount"
                           value="{{ old('qris_max_amount', setting('qris_max_amount', 500000)) }}"
                           class="nb-input w-full">
                    @error('qris_max_amount')
                        <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Invoice dengan total DI ATAS angka ini hanya menampilkan rekening (QRIS disembunyikan). Default Rp 500.000.</p>
                </div>
            </div>
            <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-3">
                Rekening ini muncul di invoice (cetak &amp; gambar yang dikirim ke WhatsApp) bersama QRIS.
            </p>
        </x-card>

        {{-- Gateway WhatsApp (kirim invoice sebagai gambar) --}}
        <x-card id="settings-whatsapp" class="scroll-mt-32 animate-fade-up" style="animation-delay: 320ms">
            <h2 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-1 flex items-center gap-2">
                <x-icon name="chat" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Kirim Invoice WhatsApp
            </h2>
            <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mb-4">
                Tombol Kirim WA mengirim gambar invoice lengkap ke nomor pelanggan pada pesanan, dengan rincian pembayaran di caption.
                Pengiriman langsung dari server membutuhkan gateway yang terhubung ke akun WhatsApp toko. Gateway adalah penghubung pengiriman,
                bukan metode pembayaran. OpenWA memakai WhatsApp Web dan bukan API resmi Meta.
            </p>

            @php
                $waStatus = $waGatewayStatus ?? null;
            @endphp
            <div class="mb-4 border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[8px] px-3 py-2 text-sm">
                @if (! $waStatus || ! $waStatus['configured'])
                    <span class="font-bold text-[#92400E]">Pengiriman langsung belum siap.</span>
                    <span class="text-[#475569] dark:text-[#cbd5e1]">Hubungkan gateway dan akun WhatsApp toko untuk mengirim tanpa memilih kontak.</span>
                @elseif ($waStatus['connected'])
                    <span class="font-bold text-[#166534]">Terhubung</span>
                    <span class="text-[#475569] dark:text-[#cbd5e1]">Sesi siap, nomor {{ $waStatus['phone'] ?: '(tidak diketahui)' }}.</span>
                @else
                    <span class="font-bold text-[#991B1B]">Tidak siap.</span>
                    <span class="text-[#475569] dark:text-[#cbd5e1]">{{ $waStatus['error'] }}</span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1" for="wa_gateway_url">Alamat server gateway</label>
                    <input id="wa_gateway_url" type="url" name="wa_gateway_url"
                           value="{{ old('wa_gateway_url', setting('wa_gateway_url')) }}"
                           placeholder="http://IP-VPS:2785"
                           class="nb-input w-full">
                    @error('wa_gateway_url')
                        <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1" for="wa_gateway_key">Kunci API gateway</label>
                    <input id="wa_gateway_key" type="password" name="wa_gateway_key" autocomplete="new-password"
                           placeholder="{{ setting('wa_gateway_key') ? 'Tersimpan, kosongkan bila tidak diubah' : 'API key dari gateway' }}"
                           class="nb-input w-full">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Kosongkan kalau tidak ingin mengubah key yang sudah tersimpan.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1" for="wa_gateway_session">ID sesi WhatsApp toko</label>
                    <input id="wa_gateway_session" type="text" name="wa_gateway_session"
                           value="{{ old('wa_gateway_session', setting('wa_gateway_session')) }}"
                           placeholder="UUID sesi WhatsApp"
                           class="nb-input w-full">
                </div>
            </div>
            <p class="mt-4 text-xs text-[#64748B] dark:text-[#cbd5e1]">
                Tanpa gateway, tombol berbagi manual tetap bisa dipakai, tetapi kamu perlu memilih WhatsApp dan kontak sendiri.
                Lihat <code class="font-mono">docs/WHATSAPP-GATEWAY.md</code> untuk panduan pemasangan dan risiko penggunaan WhatsApp Web.
            </p>
        </x-card>

        <div class="flex justify-end pt-4">
            <x-btn type="submit" class="settings-submit min-h-[44px]" icon="check">
                <span data-submit-label>Simpan Semua Pengaturan</span>
            </x-btn>
        </div>
    </form>
</div>
<script>
    (() => {
        const form = document.querySelector('[data-settings-form]');
        if (!form) return;

        const status = document.getElementById('settings-save-status');
        const submitButtons = form.querySelectorAll('.settings-submit');
        let dirty = false;

        const markDirty = () => {
            dirty = true;
            status.textContent = 'Ada perubahan yang belum disimpan.';
        };

        form.addEventListener('input', markDirty);
        form.addEventListener('change', (event) => {
            markDirty();

            const input = event.target;
            if (!(input instanceof HTMLInputElement) || input.type !== 'file') return;

            const file = input.files?.[0];
            input.setCustomValidity('');
            if (!file) return;

            if (file.size > 10 * 1024 * 1024) {
                input.setCustomValidity('Ukuran file maksimal 10 MB.');
                input.reportValidity();
                return;
            }

            if (!file.type.startsWith('image/')) return;

            const preview = form.querySelector(`[data-image-preview="${input.name}"]`);
            const image = preview?.querySelector('[data-preview-image]');
            if (!preview || !(image instanceof HTMLImageElement)) return;

            if (image.src.startsWith('blob:')) URL.revokeObjectURL(image.src);
            image.src = URL.createObjectURL(file);
            image.hidden = false;
            preview.classList.remove('hidden');
            preview.classList.add('flex');
            const label = preview.querySelector('[data-preview-label]');
            if (label) label.textContent = `File dipilih: ${file.name}`;
            input.closest('div')?.querySelector('[data-media-warning]')?.setAttribute('hidden', '');
        });

        form.querySelectorAll('[data-preview-image]').forEach((image) => {
            image.addEventListener('error', () => {
                image.hidden = true;
                const label = image.parentElement?.querySelector('[data-preview-label]');
                if (label) label.textContent = 'Pratinjau tidak dapat dimuat. Pilih file baru untuk menggantinya.';
            });
        });

        form.addEventListener('submit', (event) => {
            if (!form.reportValidity()) {
                event.preventDefault();
                return;
            }

            dirty = false;
            status.textContent = 'Menyimpan pengaturan...';
            submitButtons.forEach((button) => {
                button.disabled = true;
                const label = button.querySelector('[data-submit-label]');
                if (label) label.textContent = 'Menyimpan...';
            });
        });

        window.addEventListener('beforeunload', (event) => {
            if (!dirty) return;
            event.preventDefault();
            event.returnValue = '';
        });

        if (document.getElementById('settings-errors')) {
            status.textContent = 'Ada kesalahan. Periksa pesan di atas, lalu simpan lagi.';
        }
    })();
</script>
@endsection