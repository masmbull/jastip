@extends('layouts.app')

@section('title', 'Kebijakan Privasi & Pengembalian')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-2xl shadow-sm p-8 md:p-12">
        <h1 class="text-3xl font-bold text-[#1E293B] mb-2">Kebijakan Privasi & Pengembalian</h1>
        <p class="text-sm text-[#94A3B8] mb-8">Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}</p>

        <div class="space-y-8 text-[#64748B] leading-relaxed">
            <section>
                <h2 class="text-xl font-semibold text-[#1E293B] mb-3">A. Kebijakan Privasi</h2>
                <ul class="list-disc pl-5 space-y-2">
                    <li>Kami hanya mengumpulkan data yang diperlukan: nama, nomor WhatsApp, dan alamat pengiriman.</li>
                    <li>Data kamu digunakan semata untuk memproses titipan dan pengiriman — <strong>tidak dijual atau dibagikan</strong> ke pihak ketiga.</li>
                    <li>Detail pembayaran (QRIS/COD) diproses langsung melalui penyedia pembayaran resmi.</li>
                    <li>Kamu bisa meminta penghapusan data kapan saja dengan menghubungi admin.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-[#1E293B] mb-3">B. Kebijakan Pengembalian & Refund</h2>
                <ul class="list-disc pl-5 space-y-2">
                    <li><strong>Produk rusak/salah kirim:</strong> ajukan maksimal 3×24 jam sejak paket diterima, dengan foto/video unboxing. Kami ganti produk baru atau refund penuh.</li>
                    <li><strong>Produk tidak sesuai deskripsi:</strong> refund 100% termasuk ongkir.</li>
                    <li><strong>Stok habis setelah checkout:</strong> refund penuh atau penggantian produk setara (sesuai pilihan kamu).</li>
                    <li><strong>Refund diproses maksimal 3 hari kerja</strong> via transfer ke rekening kamu.</li>
                    <li>Pengembalian produk wajib dalam kondisi asli: kotak, segel, dan kelengkapan utuh.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-[#1E293B] mb-3">C. Yang Tidak Bisa Direfund</h2>
                <ul class="list-disc pl-5 space-y-2">
                    <li>Produk skincare/makeup yang segelnya sudah dibuka (alasan kebersihan).</li>
                    <li>Kerusakan akibat pemakaian tidak sesuai petunjuk.</li>
                    <li>Ajukan lebih dari 3×24 jam setelah paket diterima.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-[#1E293B] mb-3">D. Hubungi Kami</h2>
                <p>Untuk klaim refund atau pertanyaan privasi, chat admin via
                    <a href="{{ whatsapp_url(setting('whatsapp', '6285123456789')) }}" target="_blank" rel="noopener noreferrer" class="text-orange-600 font-medium hover:underline">WhatsApp</a>
                    dengan melampirkan nomor pesanan kamu.</p>
            </section>
        </div>
    </div>
</div>
@endsection
