@extends('layouts.admin')
@section('title', ($editing ? 'Ubah' : 'Buat') . ' Rekapan | ' . setting('brand_name'))

@section('content')
<div x-data="recapSheet({{ Js::from($recap->columns ?? []) }}, {{ Js::from(collect($recap->rows ?? [])->map(fn ($r) => ['key' => $r['key'] ?? '', 'cells' => (array) ($r['cells'] ?? [])])->values()) }})"
     class="space-y-4">

    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex items-center gap-2.5 min-w-0 flex-1">
            <a href="{{ $editing ? route('admin.recaps.show', $recap) : route('admin.recaps.index') }}"
               class="nb-btn shrink-0 w-11 h-11 px-0! bg-white dark:bg-[#1E1E1E] text-[#141414] dark:text-[#FAF6EF]" aria-label="Kembali">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div class="min-w-0 flex-1">
                <label for="recap-title" class="block text-[11px] font-black uppercase tracking-[0.12em] text-[#475569] dark:text-[#D6D3D1] mb-1">Judul rekapan</label>
                <input id="recap-title" type="text" x-model="name" maxlength="255" placeholder="Judul rekapan, mis. Rekap PO 12 Okt"
                       class="nb-input w-full px-3 py-2 text-lg font-black text-[#141414] dark:text-[#FAF6EF] placeholder:font-bold placeholder:text-[#A8A29E]">
            </div>
        </div>
        <div class="shrink-0 flex items-baseline gap-2 rounded-[10px] bg-[#FFC800] border-2 border-[#141414] dark:border-[#FAF6EF] px-4 py-2.5" style="box-shadow: 3px 3px 0 #141414" aria-live="polite">
            <span class="text-[11px] font-black uppercase tracking-[0.1em] text-[#141414]">Grand Total</span>
            <span class="text-lg font-black text-[#141414] num" x-text="formatRp(grandTotal())"></span>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-[#FEE2E2] border-2 border-[#141414] rounded-[10px] text-[#991B1B] text-sm p-4" role="alert">
            <p class="font-black mb-1">Periksa kembali isian berikut:</p>
            <ul class="list-disc list-inside space-y-0.5 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $editing ? route('admin.recaps.update', $recap) : route('admin.recaps.store') }}"
          @submit="saving = true"
          class="nb-card p-0! overflow-hidden flex flex-col">
        @csrf
        @if($editing) @method('PUT') @endif
        <input type="hidden" name="name" :value="name">

        <div class="flex flex-wrap items-center gap-2 px-3 sm:px-4 py-3 border-b-2 border-[#141414] dark:border-[#FAF6EF] bg-white dark:bg-[#1E1E1E]">
            <button type="button" @click="addRow(); $nextTick(() => { const c = $el.closest('form').querySelector('[data-cell]'); })"
                    class="nb-btn px-3 py-2 text-sm bg-[#FFC800] text-[#141414]">
                <x-icon name="plus" class="w-4 h-4" /> Baris
            </button>
            <button type="button" @click="addColumn()"
                    class="nb-btn px-3 py-2 text-sm bg-white dark:bg-[#262626] text-[#141414] dark:text-[#FAF6EF]">
                <x-icon name="plus" class="w-4 h-4" /> Kolom
            </button>

            <div class="relative" x-data="{ tplToggle: false }" @keydown.escape="tplToggle = false">
                <button type="button" @click="tplToggle = !tplToggle" :aria-expanded="tplToggle ? 'true' : 'false'"
                        class="nb-btn px-3 py-2 text-sm bg-white dark:bg-[#262626] text-[#141414] dark:text-[#FAF6EF]">
                    <x-icon name="table" class="w-4 h-4" /> Template
                    <x-icon name="chevron-down" class="w-3.5 h-3.5" />
                </button>
                <div x-show="tplToggle" x-cloak @click.away="tplToggle = false"
                     class="absolute left-0 top-full mt-2 w-64 z-30 bg-white dark:bg-[#1E1E1E] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] overflow-hidden" style="box-shadow: 4px 4px 0 #141414" role="menu">
                    <button type="button" @click="applyTemplate('pesanan'); tplToggle = false"
                            class="w-full text-left px-4 py-3 text-sm font-bold hover:bg-[#FFF7D6] dark:hover:bg-[#262626] text-[#141414] dark:text-[#FAF6EF] border-b border-[#141414]/10 min-h-[44px]">Rekap Pesanan <span class="block text-xs font-medium text-[#475569]">Pelanggan, Produk, Qty, Harga</span></button>
                    <button type="button" @click="applyTemplate('po'); tplToggle = false"
                            class="w-full text-left px-4 py-3 text-sm font-bold hover:bg-[#FFF7D6] dark:hover:bg-[#262626] text-[#141414] dark:text-[#FAF6EF] border-b border-[#141414]/10 min-h-[44px]">Rekap PO <span class="block text-xs font-medium text-[#475569]">Supplier, Item, Qty, Modal, Jual</span></button>
                    <button type="button" @click="applyTemplate('kosong'); tplToggle = false"
                            class="w-full text-left px-4 py-3 text-sm font-bold hover:bg-[#FFF7D6] dark:hover:bg-[#262626] text-[#141414] dark:text-[#FAF6EF] min-h-[44px]">Mulai Kosong</button>
                </div>
            </div>

            <span class="hidden lg:inline text-xs font-bold text-[#475569] dark:text-[#D6D3D1] ml-auto">Klik header untuk ganti nama kolom. Enter turun, panah pindah sel.</span>
        </div>

        <div class="overflow-auto max-h-[68vh] min-h-[240px]"
             @keydown.tab="
                const el = document.activeElement;
                if (!el || !el.dataset || !el.dataset.cell || !$el.contains(el)) return;
                $event.preventDefault();
                const cells = [...$el.querySelectorAll('[data-cell]')];
                const i = cells.indexOf(el);
                const next = cells[i + ($event.shiftKey ? -1 : 1)];
                if (next) { next.focus(); next.select?.(); }
             ">
            <table class="border-collapse text-sm min-w-full">
                <thead>
                    <tr>
                        <th scope="col" class="sticky top-0 left-0 z-30 w-12 bg-[#141414] text-white border-b-2 border-r-2 border-[#141414] text-xs font-black py-2.5 text-center">#</th>
                        <template x-for="(col, ci) in columns" :key="col.key">
                            <th scope="col" class="sticky top-0 z-20 align-top bg-[#FAF6EF] dark:bg-[#262626] border-b-2 border-r border-[#141414]/25 dark:border-[#FAF6EF]/25 p-0 min-w-[200px]">
                                <input type="hidden" :name="`columns[${ci}][key]`" :value="col.key">
                                <input type="text" :name="`columns[${ci}][label]`" x-model="col.label"
                                       :data-collabel="col.key" placeholder="Nama kolom"
                                       :aria-label="'Nama kolom ' + (ci + 1)"
                                       :class="isNum(col) ? 'num-head' : ''"
                                       class="w-full px-3 pt-2.5 pb-1 text-sm font-black bg-transparent text-[#141414] dark:text-[#FAF6EF] placeholder:text-[#A8A29E] min-h-[44px]">
                                <div class="flex items-center gap-1.5 px-2.5 pb-2">
                                    <select :name="`columns[${ci}][type]`" x-model="col.type"
                                            :aria-label="'Tipe kolom ' + (col.label || (ci + 1))"
                                            class="nb-input min-h-[36px]! text-[12px] font-bold px-1.5 py-1">
                                        <option value="text">Teks</option>
                                        <option value="number">Angka</option>
                                        <option value="currency">Rupiah</option>
                                    </select>
                                    <label class="inline-flex items-center gap-1.5 text-[12px] font-bold text-[#475569] dark:text-[#D6D3D1] cursor-pointer min-h-[36px]" title="Ikut grand total">
                                        <input type="checkbox" value="1" :name="`columns[${ci}][total]`" x-model="col.total"
                                               class="w-4 h-4 accent-[#EAB308]">
                                        Total
                                    </label>
                                    <button type="button" @click="removeColumn(ci)" x-show="columns.length > 1"
                                            class="ml-auto p-2 min-w-[36px] min-h-[36px] flex items-center justify-center text-[#475569] hover:text-[#991B1B]" :aria-label="'Hapus kolom ' + (col.label || (ci + 1))" title="Hapus kolom">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </th>
                        </template>
                        <th scope="col" class="sticky top-0 z-20 w-14 bg-[#141414] border-b-2 border-[#141414]"><span class="sr-only">Aksi baris</span></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, ri) in rows" :key="row.key">
                        <tr class="group bg-white dark:bg-[#1E1E1E] hover:bg-[#FFFDF0] dark:hover:bg-[#262626]">
                            <td class="sticky left-0 z-10 bg-[#FAF6EF] dark:bg-[#262626] border-b border-r border-[#141414]/20 text-center text-xs font-black text-[#475569] num" x-text="ri + 1"></td>
                            <input type="hidden" :name="`rows[${ri}][key]`" :value="row.key">
                            <template x-for="(col, ci) in columns" :key="col.key">
                                <td class="border-b border-r border-[#141414]/20 dark:border-[#FAF6EF]/20 p-0">
                                    <input :type="isNum(col) ? 'number' : 'text'"
                                           step="any"
                                           :name="`rows[${ri}][cells][${col.key}]`"
                                           x-model="row.cells[col.key]"
                                           :data-cell="`r${ri}-c${ci}`"
                                           :aria-label="(col.label || 'Kolom ' + (ci + 1)) + ', baris ' + (ri + 1)"
                                           @keydown.enter.prevent="move($event, ri, ci, 'down')"
                                           @keydown.arrow-down.prevent="move($event, ri, ci, 'down')"
                                           @keydown.arrow-up.prevent="move($event, ri, ci, 'up')"
                                           :class="isNum(col) ? 'text-right num font-bold' : 'font-medium'"
                                           class="w-full h-11 px-3 bg-transparent text-[#141414] dark:text-[#FAF6EF] focus:bg-[#FFF7D6] dark:focus:bg-[#333] focus:outline-[3px] focus:outline-[#141414]">
                                </td>
                            </template>
                            <td class="border-b border-[#141414]/20 text-center px-1">
                                <button type="button" @click="removeRow(ri)"
                                        class="p-2 min-w-[44px] min-h-[44px] inline-flex items-center justify-center text-[#475569] hover:text-[#991B1B]" :aria-label="'Hapus baris ' + (ri + 1)" title="Hapus baris">
                                    <x-icon name="trash" class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="rows.length === 0">
                        <td class="border-b border-r border-[#141414]/20"></td>
                        <td :colspan="columns.length + 1" class="border-b border-[#141414]/20 px-4 py-10 text-center">
                            <p class="font-black text-[#141414] dark:text-[#FAF6EF]">Belum ada baris.</p>
                            <p class="text-sm font-medium text-[#475569] dark:text-[#D6D3D1] mt-1">Klik <span class="font-black">Baris</span> atau pilih Template di atas untuk mulai mengisi.</p>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="sticky left-0 z-10 bg-[#141414] text-white border-t-2 border-r-2 border-[#141414] text-center text-[11px] font-black uppercase tracking-wider py-2.5">Total</td>
                        <template x-for="col in columns" :key="col.key">
                            <td class="border-t-2 border-r border-[#141414] dark:border-[#FAF6EF] px-3 py-2.5 text-right font-black num text-[#141414] dark:text-[#FAF6EF] bg-[#FAF6EF] dark:bg-[#262626]"
                                x-text="(isNum(col) && col.total) ? display(col, colTotal(col.key)) : ''"></td>
                        </template>
                        <td class="border-t-2 border-[#141414] dark:border-[#FAF6EF] bg-[#FAF6EF] dark:bg-[#262626]"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-3 sm:px-4 py-3 border-t-2 border-[#141414] dark:border-[#FAF6EF] bg-white dark:bg-[#1E1E1E]">
            <div class="text-sm font-bold text-[#475569] dark:text-[#D6D3D1] num" aria-live="polite">
                <span x-text="rows.length"></span> baris, <span x-text="columns.length"></span> kolom
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ $editing ? route('admin.recaps.show', $recap) : route('admin.recaps.index') }}"
                   class="nb-btn px-4 py-2 text-sm bg-white dark:bg-[#262626] text-[#475569] dark:text-[#D6D3D1]">
                    Batal
                </a>
                <button type="submit" :disabled="saving"
                        class="nb-btn px-5 py-2.5 bg-[#FFC800] text-[#141414]">
                    <x-spinner x-show="saving" x-cloak />
                    <span x-text="saving ? 'Menyimpan...' : 'Simpan'">Simpan</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@include('admin.recaps._script')
