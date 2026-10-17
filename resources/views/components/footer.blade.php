@php
    $brandName = setting('brand_name', 'NITIP DI END');
    $brandTagline = setting('brand_tagline', 'EH, NITIP DONG!');
    $brandOwner = setting('brand_owner', 'Nabila Adriyana');
    $brandLogo = media_url(setting('brand_logo'));
    $whatsapp = setting('whatsapp', '6285123456789');
    $instagram = setting('instagram', 'nitipdiend');
    $tiktok = setting('tiktok', 'nitipdiend');
    $email = setting('email', 'hello@nitipdiend.com');
    $address = setting('address', 'Jl. Contoh No. 1, Jakarta, Indonesia');
    $footerText = setting('footer_text', '© 2025 ' . $brandName . '. Didesain khusus untuk jastip lokal Indonesia.');
@endphp

<footer class="bg-[#141414] text-[#FAF6EF]">
    {{-- Garis aksen tunggal (dulu strip pelangi 7 warna: terlalu ramai). --}}
    <div class="h-2 bg-[#FFC800]" aria-hidden="true"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    @if($brandLogo)
                        <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-10 w-10 object-contain">
                    @else
                        <div class="h-10 w-10 rounded-[10px] bg-[#FFC800] border-2 border-white/40 flex items-center justify-center shrink-0">
                            <span class="text-[#141414] font-black text-lg">N</span>
                        </div>
                    @endif
                    <div>
                        <span class="font-bold text-xl text-white">{{ $brandName }}</span>
                        <span class="block text-xs text-orange-300">{{ $brandTagline }}</span>
                    </div>
                </div>
                <p class="text-sm text-[#CBD5E1]">by {{ $brandOwner }}</p>
                                <p class="text-sm text-[#CBD5E1]">Nitip lokal sebandar, cepat, dan terpercaya.</p>
            </div>

            {{-- Contact --}}
            <div class="space-y-4">
                <p class="text-lg font-semibold text-white">Hubungi Kami</p>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start space-x-3">
                        <x-icon name="phone" class="w-5 h-5 text-orange-400 flex-shrink-0 mt-0.5" />
                        <a href="{{ whatsapp_url($whatsapp) }}" target="_blank" rel="noopener noreferrer" class="hover:text-orange-400 transition-colors">
                            WA: {{ $whatsapp }}
                        </a>
                    </li>
                    <li class="flex items-start space-x-3">
                        <x-icon name="envelope" class="w-5 h-5 text-orange-400 flex-shrink-0 mt-0.5" />
                        <span>{{ $email }}</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <x-icon name="map-pin" class="w-5 h-5 text-orange-400 flex-shrink-0 mt-0.5" />
                        <span>{{ $address }}</span>
                    </li>
                                </ul>
            </div>

            {{-- Social Media --}}
            <div class="space-y-4">
                <p class="text-lg font-semibold text-white">Ikuti Kami</p>
                <div class="flex space-x-4">
                    <a href="https://instagram.com/{{ $instagram }}" target="_blank" rel="noopener"
                       aria-label="Instagram {{ $instagram }}" title="Instagram"
                       class="w-11 h-11 border-2 border-white/30 rounded-[10px] flex items-center justify-center text-[#FFC800] hover:bg-[#FFC800] hover:text-[#141414] hover:border-[#FFC800] transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.16c3.2 0 3.58.01 4.84.07 1.17.05 1.8.25 2.23.41.56.22.96.47 1.4.9.45.45.7 1.04.9 1.6.18.53.37 1.21.42 2.62.06 1.26.07 1.64.07 4.84s-.01 3.58-.07 4.84c-.05 1.17-.25 1.8-.41 2.23-.22.56-.47.96-.9 1.4-.45.45-1.04.7-1.6.9-.53.18-1.21.37-2.62.42-1.26.06-1.64.07-4.84.07s-3.58-.01-4.84-.07c-1.17-.05-1.8-.25-2.23-.41-.56-.22-.96-.47-1.4-.9-.45-.45-.7-1.04-.9-1.6-.18-.53-.37-1.21-.42-2.62-.06-1.26-.07-1.64-.07-4.84s.01-3.58.07-4.84c.05-1.17.25-1.8.41-2.23.22-.56.47-.96.9-1.4.45-.45 1.04-.7 1.6-.9.53-.18 1.21-.37 2.62-.42 1.26-.06 1.64-.07 4.84-.07M12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.82.33 3.99.65c-.88.33-1.67.75-2.45 1.53S.36 3.11.06 4.99C.01 5.78 0 5.37 0 6.65V12v5.35c0 1.28.01 1.69.07 2.97.06 1.27.26 2.23.58 3.06.33.88.75 1.67 1.53 2.45s1.57 1.2 2.45 1.53c.83.32 1.79.52 3.06.58 1.28.06 1.69.07 2.97.07s1.69-.01 2.97-.07c1.27-.06 2.23-.26 3.06-.58.88-.33 1.67-.75 2.45-1.53s1.2-1.57 1.53-2.45c.32-.83.52-1.79.58-3.06.06-1.28.07-1.69.07-2.97s-.01-1.69-.07-2.97c-.06-1.27-.26-2.23-.58-3.06-.33-.88-.75-1.67-1.53-2.45s-1.57-1.2-2.45-1.53c-.83-.32-1.79-.52-3.06-.58C15.31.01 14.9 0 12 0z" />
                            <path fill-rule="evenodd" d="M12 5.84A4.16 4.16 0 1016.16 10 4.16 4.16 0 0112 5.84zM12 16.16a5.16 5.16 0 115.16-5.16A5.16 5.16 0 0112 16.16z" />
                            <circle cx="12" cy="12" r="1.5" />
                        </svg>
                    </a>
                    <a href="https://tiktok.com/@{{ $tiktok }}" target="_blank" rel="noopener"
                       aria-label="TikTok {{ $tiktok }}" title="TikTok"
                       class="w-11 h-11 border-2 border-white/30 rounded-[10px] flex items-center justify-center text-[#FFC800] hover:bg-[#FFC800] hover:text-[#141414] hover:border-[#FFC800] transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.59 6.51c-1.33-.56-2.75-.84-4.21-.84-3.31 0-6.14 2.36-6.86 5.55 1.93-.38 3.68-.16 5.14.71.28.16.53.36.76.57.43-.13 1.15-.23 1.68-.05.18-.43.35-1.08.44-1.62.13-.65-.18-1.48-.76-2.1-.89-1.04-2.11-1.53-3.3-1.43-.03.38-.14.98.12 1.94.26.96.78 1.98 1.47 2.72.88 1 .77 1.9.74 2.23.02.27-.13.95-.49 1.88-1.51.04-2.8-.62-4.03-1.85-1.22-1.23-1.88-2.99-1.88-4.83 0-3.08 2.23-5.71 5.31-6.34.52-.09 1.09-.14 1.66-.14.68 0 1.32.09 1.91.25.49-.47 1.04-.86 1.68-1.15.64-.3 1.34-.44 2.06-.48-.18.46-.35.94-.49 1.43z" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="space-y-4">
                <p class="text-lg font-semibold text-white">Quick Links</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="text-[#CBD5E1] hover:text-orange-400 transition-colors">Beranda</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-[#CBD5E1] hover:text-orange-400 transition-colors">Semua Produk</a></li>
                    <li><a href="{{ route('products.viral') }}" class="text-[#CBD5E1] hover:text-orange-400 transition-colors">Produk Viral</a></li>
                    <li><a href="{{ route('shipping.index') }}" class="text-[#CBD5E1] hover:text-orange-400 transition-colors">Cek Ongkir & Resi</a></li>
                    <li><a href="{{ route('categories.index') }}" class="text-[#CBD5E1] hover:text-orange-400 transition-colors">Kategori</a></li>
                    <li><a href="{{ route('about') }}" class="text-[#CBD5E1] hover:text-orange-400 transition-colors">Tentang</a></li>
                    <li><a href="{{ route('how-to') }}" class="text-[#CBD5E1] hover:text-orange-400 transition-colors">Cara Nitip</a></li>
                    <li><a href="{{ route('contact') }}" class="text-[#CBD5E1] hover:text-orange-400 transition-colors">Kontak</a></li>
                    <li><a href="{{ route('terms') }}" class="text-[#CBD5E1] hover:text-orange-400 transition-colors">Syarat & Ketentuan</a></li>
                    <li><a href="{{ route('privacy') }}" class="text-[#CBD5E1] hover:text-orange-400 transition-colors">Kebijakan Privasi & Pengembalian</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-[#FAF6EF]/20 pt-6">
            <p class="text-center text-sm text-[#141414] dark:text-[#FAF6EF]">{!! $footerText !!}</p>
        </div>
    </div>
</footer>