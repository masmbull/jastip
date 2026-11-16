@extends('layouts.admin')
@section('title', 'Pengaturan | ' . setting('brand_name'))

@section('content')
<div class="space-y-5">
    <x-page-header title="Pengaturan" subtitle="Kelola identitas brand, kontak, dan konfigurasi toko" class="animate-fade-up">
        <x-btn as="a" :href="route('home')" target="_blank" variant="outline" icon="external-link">Lihat di halaman depan</x-btn>
    </x-page-header>

    @if($errors->any())
        <div id="settings-errors" role="alert" tabindex="-1"
             class="rounded-[8px] border-2 border-red-800 bg-red-50 px-4 py-3 text-sm text-red-900 dark:bg-red-950 dark:text-red-100">
            <p class="font-bold flex items-center gap-2"><x-icon name="warning" class="h-5 w-5" /> Pengaturan belum disimpan. Periksa pesan berikut:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <nav aria-label="Bagian pengaturan" class="sticky top-0 z-20 -mx-4 overflow-x-auto border-y-2 border-[#141414] bg-[#FAF6EF]/95 backdrop-blur px-4 py-2 dark:border-[#FAF6EF] dark:bg-[#121212]/95 sm:mx-0 sm:rounded-[8px] sm:border-2">
        <div class="flex w-max min-w-full flex-wrap gap-2 sm:flex-nowrap">
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
            ] as [$id, $label])
                <a href="#settings-{{ $id }}"
                   class="inline-flex min-h-[44px] items-center whitespace-nowrap rounded-[6px] border-2 border-[#141414] bg-white px-3 text-sm font-bold text-[#141414] transition-colors hover:bg-[#FFC800] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#141414] active:translate-y-0.5 touch-manipulation dark:border-[#FAF6EF] dark:bg-[#1E1E1E] dark:text-[#FAF6EF] dark:hover:bg-[#262626] dark:focus-visible:ring-[#FFC800]">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </nav>

    <a href="{{ route('admin.admins.index') }}"
       class="flex items-center justify-between gap-3 rounded-[8px] border-2 border-[#141414] bg-white px-4 py-3 text-sm font-bold text-[#141414] shadow-[3px_3px_0_#141414] transition-[transform,background-color] duration-150 hover:-translate-y-0.5 hover:bg-[#FFC800] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#141414] active:translate-y-0.5 touch-manipulation dark:border-[#FAF6EF] dark:bg-[#1E1E1E] dark:text-[#FAF6EF] dark:shadow-[3px_3px_0_#FAF6EF] dark:hover:bg-[#262626] dark:focus-visible:ring-[#FFC800] animate-fade-up"
       style="animation-delay: 60ms">
        <span class="flex items-center gap-2 min-w-0">
            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[8px] border-2 border-[#141414] bg-[#F97316] text-white dark:border-[#FAF6EF]">
                <x-icon name="shield-check" class="w-5 h-5" />
            </span>
            <span class="min-w-0">
                Kelola Admin &amp; kata sandi
                <span class="block sm:inline font-medium text-[#475569] dark:text-[#CBD5E1]"> — akun yang bisa masuk panel ini</span>
            </span>
        </span>
        <x-icon name="arrow-right" class="w-5 h-5 shrink-0" />
    </a>

    <form id="settings-form" data-settings-form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <x-card class="sticky top-[60px] z-10 flex flex-col gap-3 border-2 border-[#141414] bg-white/95 backdrop-blur sm:flex-row sm:items-center sm:justify-between dark:border-[#FAF6EF] dark:bg-[#1E1E1E]/95 animate-fade-up" style="animation-delay: 80ms">
            <p id="settings-save-status" role="status" aria-live="polite" class="flex items-center gap-2 text-sm font-semibold text-[#475569] dark:text-[#CBD5E1]">
                <span id="settings-dirty-dot" class="settings-dirty-dot hidden" aria-hidden="true"></span>
                <span data-status-text>Semua perubahan sudah disimpan.</span>
            </p>
            <x-btn type="submit" class="settings-submit min-h-[44px]" icon="check">
                <span data-submit-label>Simpan Pengaturan</span>
            </x-btn>
        </x-card>

        <div class="grid grid-cols-1 items-start gap-5 lg:grid-cols-2">

            {{-- ═══ Branding ═══ --}}
            <x-card id="settings-branding" class="scroll-mt-32 animate-fade-up lg:col-span-2" style="animation-delay: 120ms">
                <h2 class="mb-4 flex items-center gap-2.5 text-lg font-black text-[#1E293B] dark:text-[#f1f5f9]">
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[8px] border-2 border-[#141414] bg-[#F97316] text-white shadow-[2px_2px_0_#141414] dark:border-[#FAF6EF] dark:shadow-[2px_2px_0_#FAF6EF]">
                        <x-icon name="store" class="h-5 w-5" />
                    </span>
                    Branding
                </h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <x-settings-field for="setting-brand-name" icon="store" label="Nama Brand">
                        <input id="setting-brand-name" type="text" name="brand_name" value="{{ old('brand_name', setting('brand_name')) }}"
                               class="nb-input w-full">
                    </x-settings-field>
                    <x-settings-field for="setting-brand-tagline" icon="tag" label="Tagline">
                        <input id="setting-brand-tagline" type="text" name="brand_tagline" value="{{ old('brand_tagline', setting('brand_tagline')) }}"
                               class="nb-input w-full">
                    </x-settings-field>
                    <x-settings-field for="setting-brand-owner" icon="user" label="Pemilik Brand">
                        <input id="setting-brand-owner" type="text" name="brand_owner" value="{{ old('brand_owner', setting('brand_owner')) }}"
                               class="nb-input w-full">
                    </x-settings-field>
                </div>
            </x-card>

            {{-- ═══ Logo & Favicon ═══ --}}
            <x-card id="settings-media" class="scroll-mt-32 animate-fade-up lg:col-span-2" style="animation-delay: 140ms">
                <h2 class="mb-4 flex items-center gap-2.5 text-lg font-black text-[#1E293B] dark:text-[#f1f5f9]">
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[8px] border-2 border-[#141414] bg-[#F97316] text-white shadow-[2px_2px_0_#141414] dark:border-[#FAF6EF] dark:shadow-[2px_2px_0_#FAF6EF]">
                        <x-icon name="photo" class="h-5 w-5" />
                    </span>
                    Logo &amp; Favicon
                </h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <x-settings-field for="setting-brand-logo" icon="photo" label="Logo"
                                          hint="PNG, JPG, SVG, GIF, WEBP, atau ICO. Disarankan PNG transparan 200×60px. Maks. 10 MB.">
                            <input id="setting-brand-logo" type="file" name="brand_logo" accept="image/png,image/jpeg,image/svg+xml,image/gif,image/webp,image/x-icon"
                                   class="nb-input w-full">
                            @error('brand_logo')
                                <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                            @enderror
                            @if(setting('brand_logo'))
                                @if($mediaStatus['brand_logo']['public'] ?? false)
                                    <div data-image-preview="brand_logo" class="mt-2 flex items-center gap-3 rounded-[8px] border-2 border-dashed border-[#E2E8F0] dark:border-[#404854] p-2">
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
                        </x-settings-field>
                    </div>
                    <div>
                        <x-settings-field for="setting-brand-favicon" icon="bolt" label="Favicon"
                                          hint="32×32px atau 16×16px, PNG. Maks. 10 MB.">
                            <input id="setting-brand-favicon" type="file" name="brand_favicon" accept="image/png,image/jpeg,image/svg+xml,image/gif,image/webp,image/x-icon"
                                   class="nb-input w-full">
                            @error('brand_favicon')
                                <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                            @enderror
                            @if(setting('brand_favicon'))
                                @if($mediaStatus['brand_favicon']['public'] ?? false)
                                    <div data-image-preview="brand_favicon" class="mt-2 flex items-center gap-3 rounded-[8px] border-2 border-dashed border-[#E2E8F0] dark:border-[#404854] p-2">
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
                        </x-settings-field>
                    </div>
                </div>
            </x-card>

            {{-- ═══ Warna ═══ --}}
            <x-card id="settings-colors" class="scroll-mt-32 animate-fade-up" style="animation-delay: 160ms">
                <h2 class="mb-4 flex items-center gap-2.5 text-lg font-black text-[#1E293B] dark:text-[#f1f5f9]">
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[8px] border-2 border-[#141414] bg-[#F97316] text-white shadow-[2px_2px_0_#141414] dark:border-[#FAF6EF] dark:shadow-[2px_2px_0_#FAF6EF]">
                        <x-icon name="sparkles" class="h-5 w-5" />
                    </span>
                    Warna Brand
                </h2>
                @php
                    $brandColors = [
                        ['key' => 'primary_color', 'label' => 'Warna Teks Utama', 'fallback' => '#141414'],
                        ['key' => 'secondary_color', 'label' => 'Warna Teks Sekunder', 'fallback' => '#64748B'],
                        ['key' => 'accent_color', 'label' => 'Warna Aksen', 'fallback' => '#FFC800'],
                    ];
                @endphp
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach ($brandColors as $color)
                        @php
                            $value = (string) old($color['key'], setting($color['key'], $color['fallback']));
                            if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) {
                                $value = $color['fallback'];
                            }
                        @endphp
                        <div>
                            <label for="{{ $color['key'] }}" class="mb-1.5 flex items-center gap-1.5 text-sm font-bold text-[#334155] dark:text-[#CBD5E1]">
                                <x-icon name="tag" class="h-4 w-4 text-orange-500 dark:text-orange-400" />
                                {{ $color['label'] }}
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="{{ $color['key'] }}" name="{{ $color['key'] }}"
                                       value="{{ $value }}"
                                       class="h-12 w-12 shrink-0 cursor-pointer rounded-[8px] border-2 border-[#141414] dark:border-[#FAF6EF]"
                                       onchange="this.nextElementSibling.style.borderLeftColor = this.value; this.nextElementSibling.textContent = this.value.toUpperCase()">
                                {{-- Chip pakai border kiri, bukan latar, biar teks hex tetap terbaca
                                     berapa pun warna yang dipilih. --}}
                                <span class="rounded border-2 border-[#E2E8F0] border-l-[10px] border-l-[#141414] px-2 py-2 font-mono text-xs text-[#1E293B] dark:border-[#404854] dark:text-[#f1f5f9]"
                                      style="border-left-color: {{ $value }}">{{ strtoupper($value) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="mt-3 text-xs text-[#64748B] dark:text-[#cbd5e1]">
                    Ketiga warna ini mengganti warna yang sama di seluruh situs. Teks utama dan sekunder
                    berlaku di mode terang karena mode gelap memakai palet kontrasnya sendiri; aksen
                    berlaku di kedua mode. Pilih warna yang kontras dengan latarnya supaya teks tetap
                    terbaca.
                </p>
            </x-card>

            {{-- ═══ Kontak ═══ --}}
            <x-card id="settings-contact" class="scroll-mt-32 animate-fade-up" style="animation-delay: 180ms">
                <h2 class="mb-4 flex items-center gap-2.5 text-lg font-black text-[#1E293B] dark:text-[#f1f5f9]">
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[8px] border-2 border-[#141414] bg-[#F97316] text-white shadow-[2px_2px_0_#141414] dark:border-[#FAF6EF] dark:shadow-[2px_2px_0_#FAF6EF]">
                        <x-icon name="phone" class="h-5 w-5" />
                    </span>
                    Kontak
                </h2>
                <div class="space-y-4">
                    <x-settings-field for="setting-whatsapp" icon="chat" label="Nomor WhatsApp">
                        <input id="setting-whatsapp" type="tel" name="whatsapp" value="{{ old('whatsapp', setting('whatsapp')) }}"
                               inputmode="tel" autocomplete="tel" maxlength="16"
                               placeholder="Contoh: 628123456789"
                               class="nb-input w-full">
                        @error('whatsapp')
                            <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-[#64748B] dark:text-[#cbd5e1]">Format negara+angka tanpa spasi atau dash. Nomor ini tampil sebagai kontak; pengirim pesan mengikuti akun WhatsApp yang sedang login di perangkat admin.</p>
                    </x-settings-field>
                    <x-settings-field for="setting-email" icon="envelope" label="Email">
                        <input id="setting-email" type="email" name="email" value="{{ old('email', setting('email')) }}"
                               class="nb-input w-full">
                        @error('email')
                            <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                        @enderror
                    </x-settings-field>
                    <x-settings-field for="setting-address" icon="map-pin" label="Alamat">
                        <textarea id="setting-address" name="address" rows="2"
                                  class="nb-input w-full">{{ old('address', setting('address')) }}</textarea>
                    </x-settings-field>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-settings-field for="setting-instagram" icon="photo" label="Instagram">
                            <input id="setting-instagram" type="text" name="instagram" value="{{ old('instagram', setting('instagram')) }}"
                                   placeholder="tanpa @"
                                   class="nb-input w-full">
                        </x-settings-field>
                        <x-settings-field for="setting-tiktok" icon="bolt" label="TikTok">
                            <input id="setting-tiktok" type="text" name="tiktok" value="{{ old('tiktok', setting('tiktok')) }}"
                                   placeholder="username"
                                   class="nb-input w-full">
                        </x-settings-field>
                    </div>
                </div>
            </x-card>

            {{-- ═══ Informasi Situs ═══ --}}
            <x-card id="settings-site" class="scroll-mt-32 animate-fade-up" style="animation-delay: 200ms">
                <h2 class="mb-4 flex items-center gap-2.5 text-lg font-black text-[#1E293B] dark:text-[#f1f5f9]">
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[8px] border-2 border-[#141414] bg-[#F97316] text-white shadow-[2px_2px_0_#141414] dark:border-[#FAF6EF] dark:shadow-[2px_2px_0_#FAF6EF]">
                        <x-icon name="info" class="h-5 w-5" />
                    </span>
                    Informasi Situs
                </h2>
                <x-settings-field for="setting-footer-text" icon="clipboard" label="Teks Footer"
                                  hint="Teks yang muncul di footer halaman depan.">
                    <textarea id="setting-footer-text" name="footer_text" rows="3"
                              class="nb-input w-full">{{ old('footer_text', setting('footer_text')) }}</textarea>
                </x-settings-field>
            </x-card>

            {{-- ═══ SEO ═══ --}}
            <x-card id="settings-seo" class="scroll-mt-32 animate-fade-up" style="animation-delay: 220ms">
                <h2 class="mb-4 flex items-center gap-2.5 text-lg font-black text-[#1E293B] dark:text-[#f1f5f9]">
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[8px] border-2 border-[#141414] bg-[#F97316] text-white shadow-[2px_2px_0_#141414] dark:border-[#FAF6EF] dark:shadow-[2px_2px_0_#FAF6EF]">
                        <x-icon name="search" class="h-5 w-5" />
                    </span>
                    SEO
                </h2>
                <div class="space-y-4">
                    <x-settings-field for="setting-meta-description" icon="clipboard" label="Meta Description"
                                      hint="Deskripsi untuk meta tag dan sosial media preview.">
                        <textarea id="setting-meta-description" name="meta_description" rows="3"
                                  class="nb-input w-full">{{ old('meta_description', setting('meta_description')) }}</textarea>
                    </x-settings-field>
                    <x-settings-field for="setting-meta-keywords" icon="tag" label="Meta Keywords">
                        <input id="setting-meta-keywords" type="text" name="meta_keywords" value="{{ old('meta_keywords', setting('meta_keywords')) }}"
                               placeholder="jastip, nitip, parfum, tumbler"
                               class="nb-input w-full">
                    </x-settings-field>
                </div>
            </x-card>

            {{-- ═══ Pengaturan Bisnis ═══ --}}
            <x-card id="settings-business" class="scroll-mt-32 animate-fade-up" style="animation-delay: 240ms">
                <h2 class="mb-4 flex items-center gap-2.5 text-lg font-black text-[#1E293B] dark:text-[#f1f5f9]">
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[8px] border-2 border-[#141414] bg-[#F97316] text-white shadow-[2px_2px_0_#141414] dark:border-[#FAF6EF] dark:shadow-[2px_2px_0_#FAF6EF]">
                        <x-icon name="wallet" class="h-5 w-5" />
                    </span>
                    Pengaturan Bisnis
                </h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-settings-field for="setting-shipping-cost" icon="truck" label="Biaya Ongkir Default (Rp)"
                                      hint="Ongkir perkiraan di halaman keranjang &amp; produk. Ongkir final dari ekspedisi saat checkout.">
                        <input id="setting-shipping-cost" type="number" name="shipping_cost" value="{{ old('shipping_cost', setting('shipping_cost', 0)) }}"
                               min="0" step="1000" inputmode="numeric"
                               class="nb-input w-full">
                        @error('shipping_cost')
                            <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                        @enderror
                    </x-settings-field>
                    <x-settings-field for="setting-minimum-order" icon="wallet" label="Minimum Order (Rp)">
                        <input id="setting-minimum-order" type="number" name="minimum_order" value="{{ old('minimum_order', setting('minimum_order', 0)) }}"
                               min="0" step="1000" inputmode="numeric"
                               class="nb-input w-full">
                        @error('minimum_order')
                            <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                        @enderror
                    </x-settings-field>
                    <div class="sm:col-span-2">
                        <x-settings-field for="setting-global-fee" icon="tag" label="Set Biaya Fee Global (Rp)"
                                          hint="Fee layanan global per produk. Dikenakan ke semua order.">
                            <input id="setting-global-fee" type="number" name="setbiaya_fee" value="{{ old('setbiaya_fee', setting('setbiaya_fee', 0)) }}"
                                   min="0" step="500" inputmode="numeric"
                                   class="nb-input w-full">
                            @error('setbiaya_fee')
                                <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                            @enderror
                        </x-settings-field>
                    </div>
                </div>
            </x-card>

            {{-- ═══ Pembayaran QRIS ═══ --}}
            <x-card id="settings-payment" class="scroll-mt-32 animate-fade-up" style="animation-delay: 260ms">
                <h2 class="mb-4 flex items-center gap-2.5 text-lg font-black text-[#1E293B] dark:text-[#f1f5f9]">
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[8px] border-2 border-[#141414] bg-[#F97316] text-white shadow-[2px_2px_0_#141414] dark:border-[#FAF6EF] dark:shadow-[2px_2px_0_#FAF6EF]">
                        <x-icon name="qr-code" class="h-5 w-5" />
                    </span>
                    Pembayaran QRIS
                </h2>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="setting-qris-enabled" class="-m-2 flex min-h-[52px] cursor-pointer items-center gap-3 rounded-lg p-2 touch-manipulation transition-colors hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b]">
                            <input type="hidden" name="qris_enabled" value="0">
                            <input id="setting-qris-enabled" type="checkbox" name="qris_enabled" value="1"
                                   @checked((string) old('qris_enabled', setting('qris_enabled', '1')) === '1')
                                   class="h-6 w-6 accent-[#EAB308]">
                            <span>
                                <span class="block text-sm font-bold text-[#1E293B] dark:text-[#f1f5f9]">Aktifkan Pembayaran QRIS</span>
                                <span class="block text-xs text-[#64748B] dark:text-[#A9B4C4]">Mati = QRIS disembunyikan dari konfirmasi, halaman bayar &amp; cetak invoice. Pelanggan konfirmasi via WhatsApp.</span>
                            </span>
                        </label>
                    </div>
                    <x-settings-field for="setting-qris-image" icon="photo" label="Gambar QRIS (Static QR)"
                                      hint="PNG, JPG, GIF, atau WEBP. Statis, tampil saat checkout. Maks. 10 MB.">
                        <input id="setting-qris-image" type="file" name="qris_image" accept="image/png,image/jpeg,image/gif,image/webp"
                               class="nb-input w-full">
                        @error('qris_image')
                            <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                        @enderror
                        @if(setting('qris_image'))
                            @if($mediaStatus['qris_image']['public'] ?? false)
                                <div data-image-preview="qris_image" class="mt-2 flex items-center gap-3 rounded-[8px] border-2 border-dashed border-[#E2E8F0] dark:border-[#404854] p-2">
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
                    </x-settings-field>
                    <x-settings-field for="setting-qris-merchant" icon="store" label="Nama Merchant (opsional)"
                                      hint="Teks merchant yang ditampilkan di bawah QRIS.">
                        <input id="setting-qris-merchant" type="text" name="qris_merchant_name" value="{{ old('qris_merchant_name', setting('qris_merchant_name', setting('brand_name'))) }}"
                               placeholder="NITIP DI END"
                               class="nb-input w-full">
                    </x-settings-field>
                </div>
            </x-card>

            {{-- ═══ Rekening Pembayaran ═══ --}}
            <x-card id="settings-bank" class="scroll-mt-32 animate-fade-up" style="animation-delay: 280ms">
                <h2 class="mb-4 flex items-center gap-2.5 text-lg font-black text-[#1E293B] dark:text-[#f1f5f9]">
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[8px] border-2 border-[#141414] bg-[#F97316] text-white shadow-[2px_2px_0_#141414] dark:border-[#FAF6EF] dark:shadow-[2px_2px_0_#FAF6EF]">
                        <x-icon name="banknotes" class="h-5 w-5" />
                    </span>
                    Rekening Pembayaran
                </h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-settings-field for="bank_name" icon="banknotes" label="Nama Bank">
                        <input id="bank_name" type="text" name="bank_name" value="{{ old('bank_name', setting('bank_name')) }}"
                               placeholder="BCA / Mandiri / BRI"
                               class="nb-input w-full">
                    </x-settings-field>
                    <x-settings-field for="bank_account_number" icon="clipboard" label="Nomor Rekening">
                        <input id="bank_account_number" type="text" name="bank_account_number" inputmode="numeric"
                               value="{{ old('bank_account_number', setting('bank_account_number')) }}" placeholder="1234567890"
                               class="nb-input w-full">
                    </x-settings-field>
                    <div class="sm:col-span-2">
                        <x-settings-field for="bank_account_name" icon="user" label="Atas Nama">
                            <input id="bank_account_name" type="text" name="bank_account_name" value="{{ old('bank_account_name', setting('bank_account_name')) }}"
                                   placeholder="Nama pemilik rekening"
                                   class="nb-input w-full">
                        </x-settings-field>
                    </div>
                    <div class="sm:col-span-2">
                        <x-settings-field for="qris_max_amount" icon="wallet" label="Batas Nominal QRIS (Rp)"
                                          hint="Invoice dengan total DI ATAS angka ini hanya menampilkan rekening (QRIS disembunyikan). Default Rp 500.000.">
                            <input id="qris_max_amount" type="number" min="0" step="1000" inputmode="numeric" name="qris_max_amount"
                                   value="{{ old('qris_max_amount', setting('qris_max_amount', 500000)) }}"
                                   class="nb-input w-full">
                            @error('qris_max_amount')
                                <p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                            @enderror
                        </x-settings-field>
                    </div>
                </div>
                <p class="mt-3 text-xs text-[#64748B] dark:text-[#cbd5e1]">Rekening ini muncul di invoice cetak dan gambar yang dibagikan lewat WhatsApp, bersama QRIS.</p>
            </x-card>

        </div>

        <div class="flex justify-end pt-1">
            <x-btn type="submit" class="settings-submit min-h-[48px] w-full sm:w-auto" icon="check">
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
        const statusText = status?.querySelector('[data-status-text]');
        const dirtyDot = document.getElementById('settings-dirty-dot');
        const submitButtons = form.querySelectorAll('.settings-submit');
        let dirty = false;

        const markDirty = () => {
            dirty = true;
            dirtyDot?.classList.remove('hidden');
            if (statusText) statusText.textContent = 'Ada perubahan yang belum disimpan.';
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
            dirtyDot?.classList.add('hidden');
            if (statusText) statusText.textContent = 'Menyimpan pengaturan...';
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
            document.getElementById('settings-errors')?.focus();
        }
    })();
</script>
@endsection
