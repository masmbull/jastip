@extends('layouts.app')

@section('title', 'Kontak')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-3">Kontak</p>
        <p class="text-[#64748B] dark:text-[#cbd5e1] text-lg">Kirim pesan atau hubungi kami langsung melalui WhatsApp.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Contact Info --}}
        <div class="nb-card space-y-6">
            <h2 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Cara Menghubungi Kami</h2>

            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/20 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-300" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.52 3.48A11.94 11.94 0 0012 0a11.96 11.96 0 00-8.44 20.73l-2.53 7.55 7.67-2.08A11.88 11.88 0 0012 24c6.62 0 12-5.38 12-12 0-3.21-1.25-6.21-3.48-8.42z" /></svg>
                </div>
                <div class="min-w-0">
                    <h3 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-1">WhatsApp (Paling Cepat)</h3>
                    <p class="text-[#64748B] dark:text-[#cbd51] text-sm">Hubungi kami langsung via WhatsApp. Admin akan membalas dalam beberapa menit.</p>
                    <a href="{{ whatsapp_url(setting('whatsapp', '6285123456789'), 'Halo, saya ingin bertanya tentang jastip') }}"
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-4 py-2.5 min-h-[44px] bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-full mt-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.52 3.48A11.94 11.94 0 0012 0a11.96 11.96 0 00-8.44 20.73l-2.53 7.55 7.67-2.08A11.88 11.88 0 0012 24c6.62 0 12-5.38 12-12 0-3.21-1.25-6.21-3.48-8.42z" /></svg>
                        Chat via WhatsApp
                    </a>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-[#1E293B] dark:bg-[#2e323b] rounded-full flex items-center justify-center shrink-0">
                    <x-icon name="envelope" class="w-6 h-6 text-white dark:text-[#cbd5e1]" />
                </div>
                <div class="min-w-0">
                    <h3 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-1">Email</h3>
                    <a href="mailto:{{ setting('email', 'hello@nitipdiend.com') }}"
                       class="text-[#0E7490] dark:text-[#67e8f9] hover:underline text-sm break-all">{{ setting('email', 'hello@nitipdiend.com') }}</a>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-500/20 rounded-flex items-center justify-center shrink-0">
                    <x-icon name="map-pin" class="w-6 h-6 text-[#0E7490] dark:text-[#67e8f9]" />
                </div>
                <div class="min-w-0">
                    <h3 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-1">Alamat</h3>
                    <p class="text-[#64748B] dark:text-[#cbd5e1] text-sm">{{ setting('address', 'Jl. Contoh No. 1, Jakarta, Indonesia') }}</p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-500/20 rounded-full flex items-center justify-center shrink-0">
                    <x-icon name="clock" class="w-6 h-6 text-[#0E7490] dark:text-[#67e8f9]" />
                </div>
                <div class="min-w-0">
                    <h3 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-1">Jam Operasional</h3>
                    <p class="text-[#64748B] dark:text-[#cbd5e1] text-sm">Senin - Sabtu, 09:00 - 21:00 WIB</p>
                    <p class="text-[#64748B] dark:text-[#cbd5e1] text-sm mt-1">Minggu: Libur (kecuali hari besar)</p>
                </div>
            </div>
        </div>

        {{-- Quick Inquiry Form (WhatsApp Direct) --}}
        <div class="nb-card space-y-4">
            <h2 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Kirim Pesan Cepat</h2>
            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Isi form di bawah dan kami akan menghubungi Anda via WhatsApp.</form>

            <form onsubmit="sendToWhatsApp(event)" class="space-y-4">
                <div>
                    <label for="inq-name" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-1">Nama</label>
                    <input type="text" id="inq-name" required
                           class="w-full px-4 py-3 bg-white dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
                <div>
                    <label for="inq-whatsapp" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-1">No. WhatsApp</label>
                    <input type="text" id="inq-whatsapp" required
                           placeholder="Contoh: 08123456789" inputmode="tel"
                           class="w-full px-4 py-3 bg-white dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
                <div>
                    <label for="inq-message" class="block text-sm font-medium text-[#1E293B] dark:text-[#cbd5e1] mb-1">Pesan</label>
                    <textarea id="inq-message" rows="3" required
                              class="w-full px-4 py-3 bg-white dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300"></textarea>
                </div>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 min-h-[44px] px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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
const target = '{{ wa_phone(setting('whatsapp', '6285123456789')) }}';
        window.open('https://wa.me/' + target + '?text=' + encodeURIComponent(text), '_blank');
            }
            </script>
        </div>
    </div>
</div>
@endsection