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
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Warna Teks Utama</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="primary_color"
                               value="{{ old('primary_color', setting('primary_color', '#0891B2')) }}"
                               class="w-12 h-12 rounded border border-[#E2E8F0] dark:border-[#404854] cursor-pointer"
                               onchange="this.nextElementSibling.style.backgroundColor = this.value">
                        <span class="font-mono text-xs text-[#64748B] dark:text-[#cbd5e1]">{{ old('primary_color', setting('primary_color', '#0891B2')) }}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Warna Teks Sekunder</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="secondary_color"
                               value="{{ old('secondary_color', setting('secondary_color', '#1E293B')) }}"
                               class="w-12 h-12 rounded border border-[#E2E8F0] dark:border-[#404854] cursor-pointer"
                               onchange="this.nextElementSibling.style.backgroundColor = this.value">
                        <span class="font-mono text-xs text-[#64748B] dark:text-[#cbd5e1]">{{ old('secondary_color', setting('secondary_color', '#1E293B')) }}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Warna Aksen</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="accent_color"
                               value="{{ old('accent_color', setting('accent_color', '#f43f5e')) }}"
                               class="w-12 h-12 rounded border border-[#E2E8F0] dark:border-[#404854] cursor-pointer"
                               onchange="this.nextElementSibling.style.backgroundColor = this.value">
                        <span class="font-mono text-xs text-[#64748B] dark:text-[#cbd5e1]">{{ old('accent_color', setting('accent_color', '#f43f5e')) }}</span>
                    </div>
                </div>
            </div>
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
                    <label class="block text-sm font-medium text-[#64748B] dark:text-[#cbd5e1] mb-1">Biaya Ongkir (Rp)</label>
                    <input type="number" name="shipping_cost" value="{{ old('shipping_cost', setting('shipping_cost', 0)) }}"
                           min="0" step="1000"
                           class="w-full px-4 py-3 bg-[#F1F5F9] dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
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