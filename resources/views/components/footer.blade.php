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
    $footerText = setting('footer_text', '© ' . date('Y') . ' ' . $brandName . '. Jastip lokal Indonesia.');
@endphp

<footer class="bg-[#141414] text-[#FAF6EF]">
    <div class="h-2 bg-[#FFC800]" aria-hidden="true"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 mb-10">
            <div class="space-y-4 sm:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-3">
                    @if($brandLogo)
                        <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-10 w-10 object-contain">
                    @else
                        <div class="h-10 w-10 rounded-[10px] bg-[#FFC800] border-2 border-[#FAF6EF]/40 flex items-center justify-center shrink-0">
                            <span class="text-[#141414] font-black text-lg">{{ strtoupper(substr($brandName, 0, 1)) }}</span>
                        </div>
                    @endif
                    <div>
                        <span class="font-display font-extrabold text-xl text-[#FAF6EF]">{{ $brandName }}</span>
                        <span class="block text-xs font-bold text-[#FFC800]">{{ $brandTagline }}</span>
                    </div>
                </div>
                <p class="text-sm text-[#D6D3D1]">oleh {{ $brandOwner }}</p>
                <p class="text-sm text-[#A8A29E] max-w-xs">Nitip lokal sebandar: cepat, jelas, dan diurus sampai rumah.</p>
            </div>

            <div class="space-y-4">
                <p class="font-display text-lg font-extrabold text-[#FAF6EF]">Hubungi Kami</p>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-3">
                        <x-icon name="phone" class="w-5 h-5 text-[#FFC800] shrink-0 mt-0.5" stroke="2" />
                        <a href="{{ whatsapp_url($whatsapp) }}" target="_blank" rel="noopener noreferrer"
                           class="text-[#D6D3D1] hover:text-[#FFC800] transition-colors min-h-[44px] inline-flex items-center">
                            WA: {{ $whatsapp }}
                        </a>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-icon name="envelope" class="w-5 h-5 text-[#FFC800] shrink-0 mt-0.5" stroke="2" />
                        <a href="mailto:{{ $email }}" class="text-[#D6D3D1] hover:text-[#FFC800] transition-colors break-all">{{ $email }}</a>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-icon name="map-pin" class="w-5 h-5 text-[#FFC800] shrink-0 mt-0.5" stroke="2" />
                        <span class="text-[#D6D3D1]">{{ $address }}</span>
                    </li>
                </ul>

                <div class="flex gap-3 pt-1">
                    <a href="https://instagram.com/{{ $instagram }}" target="_blank" rel="noopener"
                       aria-label="Instagram {{ $instagram }}" title="Instagram"
                       class="w-11 h-11 border-2 border-[#FAF6EF]/30 rounded-[10px] flex items-center justify-center text-[#FFC800] hover:bg-[#FFC800] hover:text-[#141414] hover:border-[#FFC800] transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2.16c3.2 0 3.58.01 4.84.07 1.17.05 1.8.25 2.23.41.56.22.96.47 1.4.9.45.45.7 1.04.9 1.6.18.53.37 1.21.42 2.62.06 1.26.07 1.64.07 4.84s-.01 3.58-.07 4.84c-.05 1.17-.25 1.8-.41 2.23-.22.56-.47.96-.9 1.4-.45.45-1.04.7-1.6.9-.53.18-1.21.37-2.62.42-1.26.06-1.64.07-4.84.07s-3.58-.01-4.84-.07c-1.17-.05-1.8-.25-2.23-.41-.56-.22-.96-.47-1.4-.9-.45-.45-.7-1.04-.9-1.6-.18-.53-.37-1.21-.42-2.62-.06-1.26-.07-1.64-.07-4.84s.01-3.58.07-4.84c.05-1.17.25-1.8.41-2.23.22-.56.47-.96.9-1.4.45-.45 1.04-.7 1.6-.9.53-.18 1.21-.37 2.62-.42 1.26-.06 1.64-.07 4.84-.07M12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.82.33 3.99.65c-.88.33-1.67.75-2.45 1.53S.36 3.11.06 4.99C.01 5.78 0 5.37 0 6.65V12v5.35c0 1.28.01 1.69.07 2.97.06 1.27.26 2.23.58 3.06.33.88.75 1.67 1.53 2.45s1.57 1.2 2.45 1.53c.83.32 1.79.52 3.06.58 1.28.06 1.69.07 2.97.07s1.69-.01 2.97-.07c1.27-.06 2.23-.26 3.06-.58.88-.33 1.67-.75 2.45-1.53s1.2-1.57 1.53-2.45c.32-.83.52-1.79.58-3.06.06-1.28.07-1.69.07-2.97s-.01-1.69-.07-2.97c-.06-1.27-.26-2.23-.58-3.06-.33-.88-.75-1.67-1.53-2.45s-1.57-1.2-2.45-1.53c-.83-.32-1.79-.52-3.06-.58C15.31.01 14.9 0 12 0z" />
                            <path fill-rule="evenodd" d="M12 5.84A4.16 4.16 0 1016.16 10 4.16 4.16 0 0112 5.84zM12 16.16a5.16 5.16 0 115.16-5.16A5.16 5.16 0 0112 16.16z" />
                            <circle cx="18.4" cy="5.6" r="1.2" />
                        </svg>
                    </a>
                    <a href="https://tiktok.com/@{{ $tiktok }}" target="_blank" rel="noopener"
                       aria-label="TikTok {{ $tiktok }}" title="TikTok"
                       class="w-11 h-11 border-2 border-[#FAF6EF]/30 rounded-[10px] flex items-center justify-center text-[#FFC800] hover:bg-[#FFC800] hover:text-[#141414] hover:border-[#FFC800] transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M19.59 6.51c-.7-.32-1.33-.74-1.85-1.24A5.03 5.03 0 0 1 16.5 2h-3.1v13.2a2.9 2.9 0 1 1-2.06-2.77V9.2a6 6 0 1 0 5.16 5.94V8.4a8.1 8.1 0 0 0 4.7 1.5V6.8a5.1 5.1 0 0 1-1.61-.29Z" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="space-y-4">
                <p class="font-display text-lg font-extrabold text-[#FAF6EF]">Tautan</p>
                <ul class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="text-[#D6D3D1] hover:text-[#FFC800] transition-colors inline-flex min-h-[40px] items-center">Beranda</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-[#D6D3D1] hover:text-[#FFC800] transition-colors inline-flex min-h-[40px] items-center">Produk</a></li>
                    <li><a href="{{ route('products.viral') }}" class="text-[#D6D3D1] hover:text-[#FFC800] transition-colors inline-flex min-h-[40px] items-center">Viral</a></li>
                    <li><a href="{{ route('categories.index') }}" class="text-[#D6D3D1] hover:text-[#FFC800] transition-colors inline-flex min-h-[40px] items-center">Kategori</a></li>
                    <li><a href="{{ route('shipping.index') }}" class="text-[#D6D3D1] hover:text-[#FFC800] transition-colors inline-flex min-h-[40px] items-center">Ongkir</a></li>
                    <li><a href="{{ route('how-to') }}" class="text-[#D6D3D1] hover:text-[#FFC800] transition-colors inline-flex min-h-[40px] items-center">Cara Nitip</a></li>
                    <li><a href="{{ route('about') }}" class="text-[#D6D3D1] hover:text-[#FFC800] transition-colors inline-flex min-h-[40px] items-center">Tentang</a></li>
                    <li><a href="{{ route('contact') }}" class="text-[#D6D3D1] hover:text-[#FFC800] transition-colors inline-flex min-h-[40px] items-center">Kontak</a></li>
                    <li><a href="{{ route('terms') }}" class="text-[#D6D3D1] hover:text-[#FFC800] transition-colors inline-flex min-h-[40px] items-center">Syarat</a></li>
                    <li><a href="{{ route('privacy') }}" class="text-[#D6D3D1] hover:text-[#FFC800] transition-colors inline-flex min-h-[40px] items-center">Privasi</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-[#FAF6EF]/20 pt-6">
            <p class="text-center text-sm text-[#A8A29E]">{{ strip_tags($footerText) }}</p>
        </div>
    </div>
</footer>
