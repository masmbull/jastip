{{-- Modal: buat invoice manual (form identik dengan halaman create). --}}
<div x-show="open" x-cloak class="fixed inset-0 z-[400] flex items-start justify-center overflow-y-auto">
    <div class="absolute inset-0 bg-black/60" @click="open = false"></div>
    <div class="relative nb-card w-full max-w-3xl mx-4 my-8 p-0! overflow-hidden"
         @click.stop x-data="manualInvoice()">
        <div class="sticky top-0 z-10 px-5 py-4 border-b-2 border-[#141414] dark:border-[#FAF6EF] flex items-center justify-between gap-3 bg-white dark:bg-[#1E1E1E]">
            <div>
                <h3 class="text-lg font-black">Buat Invoice Manual</h3>
                <p class="text-xs font-medium text-[#475569] dark:text-[#D6D3D1]">Isi seperti invoice, nomor dibuat otomatis saat simpan.</p>
            </div>
            <button type="button" @click="open = false" aria-label="Tutup" title="Tutup"
                    class="shrink-0 inline-flex items-center justify-center w-11 h-11 border-2 border-transparent hover:border-[#141414] dark:hover:border-[#FAF6EF] rounded-[8px]">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>
        <div class="p-4 sm:p-5 bg-[#FAF6EF] dark:bg-[#121212]">
            @include('admin.orders._manual-form', ['cancel' => 'modal'])
        </div>
    </div>
</div>
