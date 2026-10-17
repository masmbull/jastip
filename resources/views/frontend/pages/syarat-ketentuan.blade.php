@extends('layouts.app')

@section('title', 'Syarat & Ketentuan')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <x-card>
        <h1 class="text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-2">Syarat & Ketentuan</h1>
        <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] mb-8">Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}</p>

        <div class="space-y-8 text-[#64748B] dark:text-[#cbd5e1] leading-relaxed">
            <section>
                <h2 class="text-xl font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-3">1. Tentang Layanan</h2>
                <p>{{ setting('brand_name', 'NITIP DI END') }} adalah layanan jastip (titip beli) yang membantu kamu membeli produk impian dengan harga terbaik. Dengan menggunakan layanan kami, kamu menyetujui seluruh syarat dan ketentuan di halaman ini.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-3">2. Pemesanan & Pembayaran</h2>
                <ul class="list-disc pl-5 space-y-2">
                    <li>Pesanan diproses setelah konfirmasi admin via WhatsApp.</li>
                    <li>Minimal titip: <span class="num">Rp {{ number_format((int) setting('minimum_order', 50000), 0, ',', '.') }}</span>.</li>
                    <li>Metode pembayaran: COD (bayar di tempat) dan QRIS.</li>
                    <li>Gratis ongkir untuk titipan di atas <span class="num">Rp150.000</span>.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-3">3. Stok & Harga</h2>
                <p>Harga dapat berubah mengikuti kurs/penjual asal. Jika stok habis setelah pesanan dibuat, admin akan menghubungi kamu untuk penggantian produk atau pengembalian dana penuh.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-3">4. Garansi Autentik</h2>
                <p>Semua produk yang dititipkan dijamin 100% autentik dari penjual resmi. Bila terbukti palsu, uang dikembalikan 100%.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-3">5. Batasan Tanggung Jawab</h2>
                <p>{{ setting('brand_name', 'NITIP DI END') }} tidak bertanggung jawab atas keterlambatan pengiriman yang disebabkan oleh ekspedisi, cuaca, atau keadaan force majeure di luar kendali kami.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-3">6. Kontak</h2>
                <p>Pertanyaan tentang syarat & ketentuan? Hubungi admin via
                    <a href="{{ whatsapp_url(setting('whatsapp', '6285123456789')) }}" target="_blank" rel="noopener noreferrer" class="text-[#141414] dark:text-[#FAF6EF] dark:text-[#141414] dark:text-[#FAF6EF] font-medium hover:underline">WhatsApp</a>
                    atau email {{ setting('email', 'hello@nitipdiend.com') }}.</p>
            </section>
        </div>
    </x-card>
</div>
@endsection