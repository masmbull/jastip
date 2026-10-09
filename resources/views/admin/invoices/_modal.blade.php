{{-- Modal: buat invoice manual (form identik dengan halaman create). --}}
<div x-show="open" x-cloak class="fixed inset-0 z-[400] flex items-start justify-center overflow-y-auto">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
    <div class="relative bg-[#F8FAFC] dark:bg-[#1a1a1a] rounded-xl shadow-xl w-full max-w-3xl mx-4 my-8 animate-scale-in"
         @click.stop x-data="manualInvoice()">
        <div class="sticky top-0 z-10 p-4 border-b border-[#E2E8F0] dark:border-[#404854] flex items-center justify-between bg-white dark:bg-[#23252b] rounded-t-xl">
            <div>
                <h3 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Buat Invoice Manual</h3>
                <p class="text-xs text-[#94A3B8]">Isi seperti invoice, nomor dibuat otomatis saat simpan.</p>
            </div>
            <button type="button" @click="open = false" aria-label="Tutup"
                    class="text-[#94A3B8] dark:text-[#cbd5e1] hover:text-[#1E293B] dark:hover:text-[#f1f5f9] p-1 rounded-lg hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] transition-colors">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>
        <div class="p-4 sm:p-6">
            @include('admin.orders._manual-form', ['cancel' => 'modal'])
        </div>
    </div>
</div>