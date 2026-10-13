{{--
  Modal "Pratinjau Cetak Sampel Invoice" (preset COMPACT).
  Isi invoice dimuat dalam <iframe> ke route invoices.sample supaya preview dan
  hasil cetak/PDF identik: yang tercetak hanya isi iframe, bukan modal/toolbar.
  State di root halaman: sampleOpen, sampleLoaded, sampleError.
--}}
<template x-if="sampleOpen">
    <div class="fixed inset-0 z-[420] flex items-stretch sm:items-center justify-center sm:p-6" role="dialog" aria-modal="true" aria-labelledby="sample-modal-title">
        <div class="absolute inset-0 bg-black/50" @click="sampleOpen = false"></div>

        <div class="relative w-full h-full sm:h-auto sm:max-w-3xl sm:max-h-[92vh] bg-white dark:bg-[#23252b] sm:rounded-2xl shadow-2xl overflow-hidden flex flex-col animate-scale-in"
             @click.stop>

            {{-- Header --}}
            <div class="shrink-0 px-4 sm:px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#404854] flex items-center justify-between gap-3">
                <h3 id="sample-modal-title" class="text-base sm:text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Pratinjau Cetak Sampel Invoice</h3>
                <button type="button" @click="sampleOpen = false" aria-label="Tutup pratinjau" title="Tutup"
                        class="shrink-0 text-[#64748B] dark:text-[#cbd5e1] hover:text-[#1E293B] dark:hover:text-[#f1f5f9] p-2 rounded-lg hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            {{-- Toolbar --}}
            <div class="shrink-0 px-4 sm:px-5 py-2.5 border-b border-[#E2E8F0] dark:border-[#404854] bg-[#F1F5F9] dark:bg-[#2e323b] flex items-center justify-between gap-3">
                <span class="text-xs sm:text-sm text-[#64748B] dark:text-[#cbd5e1]">Preset Aktif: <span class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">COMPACT</span></span>
                <div class="flex items-center gap-2">
                    <button type="button" @click="sampleOpen = false"
                            class="px-3 sm:px-4 py-2 text-sm bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg font-medium hover:bg-[#F1F5F9] dark:hover:bg-[#404854] transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                        Tutup
                    </button>
                    <button type="button"
                            :disabled="!sampleLoaded || sampleError"
                            @click="$refs.sampleFrame?.contentWindow?.focus(); $refs.sampleFrame?.contentWindow?.print()"
                            class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 text-sm bg-[#0F172A] hover:bg-black text-white rounded-lg font-medium shadow-sm transition-colors disabled:opacity-70 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-orange-300">
                        <x-icon name="printer" class="w-4 h-4" /> Cetak PDF
                    </button>
                </div>
            </div>

            {{-- Area preview --}}
            <div class="flex-1 relative bg-[#E2E8F0] dark:bg-[#121212]"
                 x-effect="if (sampleOpen) { clearTimeout(window.__sampleT); window.__sampleT = setTimeout(() => { if (!sampleLoaded) sampleError = true }, 10000) }">
                <div x-show="!sampleLoaded && !sampleError" class="absolute inset-0 flex items-center justify-center text-sm text-[#64748B] dark:text-[#cbd5e1]">
                    <x-spinner label="Memuat pratinjau..." />
                </div>
                <div x-show="sampleError" x-cloak class="absolute inset-0 flex flex-col items-center justify-center gap-2 px-6 text-center">
                    <x-icon name="warning" class="w-8 h-8 text-red-500" />
                    <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Gagal memuat pratinjau invoice.</p>
                    <button type="button" @click="sampleError = false; sampleLoaded = false; $refs.sampleFrame.src = 'about:blank'; $nextTick(() => $refs.sampleFrame.src = '{{ route('admin.invoices.sample') }}')"
                            class="text-sm text-orange-600 dark:text-orange-400 font-medium hover:underline">Coba lagi</button>
                </div>
                <iframe x-ref="sampleFrame"
                        :src="sampleOpen && !sampleError ? '{{ route('admin.invoices.sample') }}' : 'about:blank'"
                        x-show="sampleLoaded"
                        @load="sampleLoaded = true"
                        class="w-full h-full min-h-[60vh] border-0"
                        title="Pratinjau sampel invoice"></iframe>
            </div>
        </div>
    </div>
</template>