@extends('layouts.app')

@section('title', 'Kontak')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-[#1E293B] mb-3">Kontak</h1>
        <p class="text-[#94A3B8] text-lg">Kirim pesan atau hubungi kami langsung melalui WhatsApp.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Contact Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-[#E2E8F0] p-8 space-y-6">
            <h2 class="text-xl font-bold text-[#1E293B]">Cara Menghubungi Kami</h2>

            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79a15.05 15.05 0 016.59-6.59l2.2-.73a1 1 0 011.14.43l2 3.46a1 1 0 01-.22 1.32l-2.09 1.63a1 1 0 01-.42.18l-1.42.07a11.19 11.19 0 01-.74-.18 1 1 0 00-.72 1.7l.27 1.04a1 1 0 01-.21.94h-2.67a1 1 0 01-.22-.18l-2.09-1.63a1 1 0 01-.22-1.32l.88-2.22a1 1 0 00.03-.82z" /></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-[#1E293B] mb-1">WhatsApp (Paling Cepat)</h3>
                    <p class="text-[#64748B] text-sm">Hubungi kami langsung via WhatsApp. Admin akan membalas dalam beberapa menit.</p>
                    <a href="{{ whatsapp_url(setting('whatsapp', '6285123456789'), 'Halo, saya ingin bertanya tentang jastip') }}"
                       class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-full mt-3 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M20.52 3.48A11.94 11.94 0 0012 0a11.96 11.96 0 00-8.44 20.73l-2.53 7.55 7.67-2.08A11.88 11.88 0 0012 24c6.62 0 12-5.38 12-12 0-3.21-1.25-6.21-3.48-8.42z" /></svg>
                        Chat via WhatsApp
                    </a>
                </div>
            </div>

            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-[#1E293B] rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-[#1E293B] mb-1">Email</h3>
                    <a href="mailto:{{ setting('email', 'hello@nitipdiend.com') }}"
                       class="text-orange-600 hover:underline text-sm">{{ setting('email', 'hello@nitipdiend.com') }}</a>
                </div>
            </div>

            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16L12 20l-5.657-4L5 13.5V8.5l6-3 6 3v5.5z"></path></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-[#1E293B] mb-1">Alamat</h3>
                    <p class="text-[#64748B] text-sm">{{ setting('address', 'Jl. Contoh No. 1, Jakarta, Indonesia') }}</p>
                </div>
            </div>

            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-[#1E293B] mb-1">Jam Operasional</h3>
                    <p class="text-[#64748B] text-sm">Senin - Sabtu, 09:00 - 21:00 WIB</p>
                    <p class="text-[#94A3B8] text-sm mt-1">Minggu: Libur (kecuali hari besar)</p>
                </div>
            </div>

            {{-- Quick Inquiry Form (WhatsApp Direct) --}}
            <div class="bg-white rounded-xl shadow-sm border border-[#E2E8F0] p-8 space-y-4 mt-8">
                <h2 class="text-xl font-bold text-[#1E293B]">Kirim Pesan Cepat</h2>
                <p class="text-sm text-[#94A3B8]">Isi form di bawah dan kami akan menghubungi Anda via WhatsApp.</p>

                <form onsubmit="sendToWhatsApp(event)" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] mb-1">Nama</label>
                        <input type="text" id="inq-name" required
                               class="w-full px-4 py-3 border border-[#E2E8F0] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] mb-1">No. WhatsApp</label>
                        <input type="text" id="inq-whatsapp" required
                               placeholder="Contoh: 08123456789"
                               class="w-full px-4 py-3 border border-[#E2E8F0] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] mb-1">Pesan</label>
                        <textarea id="inq-message" rows="3" required
                                  class="w-full px-4 py-3 border border-[#E2E8F0] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300"></textarea>
                    </div>
                    <button type="submit"
                            class="w-full px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4 mr-2 inline" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.52 3.48A11.94 11.94 0 0012 0a11.96 11.96 0 00-8.44 20.73l-2.53 7.55 7.67-2.08A11.88 11.88 0 0012 24c6.62 0 12-5.38 12-12 0-3.21-1.25-6.21-3.48-8.42z" />
                        </svg>
                        Kirim ke WhatsApp Admin
                    </button>
                </form>

                <script>
                function sendToWhatsApp(e) {
                    e.preventDefault();
                    const name = document.getElementById('inq-name').value.trim();
                    const wa = document.getElementById('inq-whatsapp').value.trim();
                    const msg = document.getElementById('inq-message').value.trim();
                    const text = 'Halo, saya ' + name + ' (' + wa + ').\n\n' + msg;
                    const target = '{{ setting('whatsapp', '6285123456789') }}'.replace(/\D/g, '');
                    window.open('https://wa.me/' + target + '?text=' + encodeURIComponent(text), '_blank');
                }
                </script>
            </div>
        </div>
    </div>
</div>
@endsection