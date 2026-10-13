@extends('layouts.admin')
@section('title', ($editing ? 'Ubah' : 'Buat') . ' Rekapan | ' . setting('brand_name'))

@section('content')
<div x-data="recapSheet({{ Js::from($recap->columns ?? []) }}, {{ Js::from(collect($recap->rows ?? [])->map(fn ($r) => ['key' => $r['key'] ?? '', 'cells' => (array) ($r['cells'] ?? [])])->values()) }})"
     class="space-y-4">

    {{-- Bar judul + grand total --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex items-center gap-2.5 min-w-0 flex-1">
            <a href="{{ $editing ? route('admin.recaps.show', $recap) : route('admin.recaps.index') }}"
               class="shrink-0 text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 p-2 rounded-lg hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] transition-colors" aria-label="Kembali">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <input type="text" x-model="name" maxlength="255" placeholder="Judul rekapan"
                   aria-label="Judul rekapan"
                   class="min-w-0 flex-1 text-xl font-bold bg-transparent border-0 text-[#1E293B] dark:text-[#f1f5f9] px-0 py-1 focus:outline-none placeholder-[#94A3B8]">
        </div>
        <div class="shrink-0 flex items-baseline gap-2 rounded-lg bg-orange-50 dark:bg-orange-500/10 px-4 py-2">
            <span class="text-xs font-medium uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1]">Grand Total</span>
            <span class="text-lg font-bold text-orange-600 dark:text-orange-400 num" x-text="formatRp(grandTotal())"></span>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-300 text-sm rounded-lg p-4">
            <p class="font-medium mb-1">Periksa kembali isian berikut:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $editing ? route('admin.recaps.update', $recap) : route('admin.recaps.store') }}"
          @submit="saving = true"
          class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] overflow-hidden flex flex-col">
        @csrf
        @if($editing) @method('PUT') @endif
        <input type="hidden" name="name" :value="name">

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-2 px-3 sm:px-4 py-3 border-b border-[#E2E8F0] dark:border-[#404854] bg-[#F8FAFC] dark:bg-[#2e323b]">
            <button type="button" @click="addRow()"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                <x-icon name="plus" class="w-4 h-4" /> Baris
            </button>
            <button type="button" @click="addColumn()"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg font-medium hover:border-orange-300 transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                <x-icon name="plus" class="w-4 h-4" /> Kolom
            </button>

            {{-- Template (parent relative supaya dropdown tidak meleset) --}}
            <div class="relative" x-data="{ tplToggle: false }" @keydown.escape="tplToggle = false">
                <button type="button" @click="tplToggle = !tplToggle" :aria-expanded="tplToggle ? 'true' : 'false'"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg font-medium hover:border-orange-300 transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <x-icon name="table" class="w-4 h-4" /> Template
                    <x-icon name="chevron-down" class="w-3.5 h-3.5" />
                </button>
                <div x-show="tplToggle" x-cloak @click.away="tplToggle = false"
                     class="absolute left-0 top-full mt-1 w-60 z-30 bg-white dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg shadow-lg overflow-hidden">
                    <button type="button" @click="applyTemplate('pesanan'); tplToggle = false"
                            class="w-full text-left px-4 py-2.5 text-sm hover:bg-orange-50 dark:hover:bg-orange-500/10 text-[#1E293B] dark:text-[#f1f5f9]">Rekap Pesanan</button>
                    <button type="button" @click="applyTemplate('po'); tplToggle = false"
                            class="w-full text-left px-4 py-2.5 text-sm hover:bg-orange-50 dark:hover:bg-orange-500/10 text-[#1E293B] dark:text-[#f1f5f9]">Rekap PO (Modal / Jual)</button>
                    <button type="button" @click="applyTemplate('kosong'); tplToggle = false"
                            class="w-full text-left px-4 py-2.5 text-sm hover:bg-orange-50 dark:hover:bg-orange-500/10 text-[#1E293B] dark:text-[#f1f5f9]">Mulai Kosong</button>
                </div>
            </div>

            <span class="hidden lg:inline text-xs text-[#94A3B8] dark:text-[#64748B] ml-auto">Ubah nama di header kolom. Enter turun, Tab ke kanan.</span>
        </div>

        {{-- Grid pivot --}}
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
                        <th class="sticky top-0 left-0 z-30 w-12 bg-[#EEF2F6] dark:bg-[#23252b] border-b-2 border-r border-[#CBD5E1] dark:border-[#404854] text-xs font-semibold text-[#64748B] dark:text-[#cbd5e1] py-2.5 text-center">#</th>
                        <template x-for="(col, ci) in columns" :key="col.key">
                            <th class="sticky top-0 z-20 align-top bg-[#EEF2F6] dark:bg-[#23252b] border-b-2 border-r border-[#CBD5E1] dark:border-[#404854] p-0 min-w-[190px]">
                                <input type="hidden" :name="`columns[${ci}][key]`" :value="col.key">
                                <input type="text" :name="`columns[${ci}][label]`" x-model="col.label"
                                       :data-collabel="col.key" placeholder="Nama kolom"
                                       :class="isNum(col) ? 'num-head' : ''"
                                       class="w-full px-3 pt-2.5 pb-1 text-sm font-semibold bg-transparent focus:outline-none text-[#1E293B] dark:text-[#f1f5f9] placeholder-[#94A3B8]">
                                <div class="flex items-center gap-1.5 px-2.5 pb-2">
                                    <select :name="`columns[${ci}][type]`" x-model="col.type"
                                            class="text-[11px] border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#64748B] dark:text-[#cbd5e1] rounded px-1.5 py-0.5 focus:outline-none focus:ring-2 focus:ring-orange-300">
                                        <option value="text">Teks</option>
                                        <option value="number">Angka</option>
                                        <option value="currency">Rupiah</option>
                                    </select>
                                    <label class="inline-flex items-center gap-1 text-[11px] text-[#64748B] dark:text-[#cbd5e1] cursor-pointer" title="Ikut grand total">
                                        <input type="checkbox" value="1" :name="`columns[${ci}][total]`" x-model="col.total"
                                               class="w-3.5 h-3.5 rounded border-[#CBD5E1] text-orange-500 focus:ring-orange-300">
                                        Total
                                    </label>
                                    <button type="button" @click="removeColumn(ci)" x-show="columns.length > 1"
                                            class="ml-auto text-[#94A3B8] hover:text-red-600 p-0.5 rounded focus:outline-none focus:ring-2 focus:ring-red-300" aria-label="Hapus kolom" title="Hapus kolom">
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </th>
                        </template>
                        <th class="sticky top-0 z-20 w-10 bg-[#EEF2F6] dark:bg-[#23252b] border-b-2 border-[#CBD5E1] dark:border-[#404854]"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, ri) in rows" :key="row.key">
                        <tr class="group">
                            <td class="sticky left-0 z-10 bg-[#F8FAFC] dark:bg-[#23252b] group-hover:bg-[#E9EEF3] dark:group-hover:bg-[#2e323b] border-b border-r border-[#E2E8F0] dark:border-[#404854] text-center text-xs text-[#94A3B8] select-none num" x-text="ri + 1"></td>
                            <input type="hidden" :name="`rows[${ri}][key]`" :value="row.key">
                            <template x-for="(col, ci) in columns" :key="col.key">
                                <td class="border-b border-r border-[#E2E8F0] dark:border-[#404854] p-0">
                                    <input :type="isNum(col) ? 'number' : 'text'"
                                           step="any"
                                           :name="`rows[${ri}][cells][${col.key}]`"
                                           x-model="row.cells[col.key]"
                                           :data-cell="`r${ri}-c${ci}`"
                                           @keydown.enter.prevent="move($event, ri, ci, 'down')"
                                           @keydown.arrow-down.prevent="move($event, ri, ci, 'down')"
                                           @keydown.arrow-up.prevent="move($event, ri, ci, 'up')"
                                           :class="isNum(col) ? 'text-right num' : ''"
                                           class="w-full h-10 px-3 bg-transparent focus:outline-none focus:bg-orange-50/60 dark:focus:bg-orange-500/10 focus:ring-2 focus:ring-inset focus:ring-orange-400 text-[#1E293B] dark:text-[#f1f5f9]">
                                </td>
                            </template>
                            <td class="border-b border-[#E2E8F0] dark:border-[#404854] text-center">
                                <button type="button" @click="removeRow(ri)"
                                        class="text-[#94A3B8] hover:text-red-600 p-1 rounded opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity focus:outline-none focus:ring-2 focus:ring-red-300" aria-label="Hapus baris" title="Hapus baris">
                                    <x-icon name="trash" class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="rows.length === 0">
                        <td class="border-b border-r border-[#E2E8F0] dark:border-[#404854]"></td>
                        <td :colspan="columns.length + 1" class="border-b border-[#E2E8F0] dark:border-[#404854] px-4 py-10 text-center text-[#64748B] dark:text-[#cbd5e1]">
                            Belum ada baris. Klik <span class="font-medium">Baris</span> untuk mulai mengisi.
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="sticky left-0 z-10 bg-[#EEF2F6] dark:bg-[#23252b] border-t-2 border-r border-[#CBD5E1] dark:border-[#404854] text-center text-[11px] font-semibold uppercase text-[#64748B] dark:text-[#cbd5e1] py-2.5">Total</td>
                        <template x-for="col in columns" :key="col.key">
                            <td class="border-t-2 border-r border-[#CBD5E1] dark:border-[#404854] px-3 py-2.5 text-right font-semibold num text-[#1E293B] dark:text-[#f1f5f9]"
                                x-text="(isNum(col) && col.total) ? display(col, colTotal(col.key)) : ''"></td>
                        </template>
                        <td class="border-t-2 border-[#CBD5E1] dark:border-[#404854]"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Bar bawah: ringkasan + aksi --}}
        <div class="flex items-center justify-between gap-3 px-3 sm:px-4 py-3 border-t border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b]">
            <div class="text-sm text-[#64748B] dark:text-[#cbd5e1] num">
                <span x-text="rows.length"></span> baris, <span x-text="columns.length"></span> kolom
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ $editing ? route('admin.recaps.show', $recap) : route('admin.recaps.index') }}"
                   class="px-4 py-2 text-sm text-[#64748B] dark:text-[#cbd5e1] hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Batal
                </a>
                <button type="submit" :disabled="saving"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 disabled:opacity-70 disabled:cursor-not-allowed text-white font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <x-spinner x-show="saving" x-cloak />
                    <span x-text="saving ? 'Menyimpan...' : 'Simpan'">Simpan</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@include('admin.recaps._script')