@extends('layouts.admin')
@section('title', ($editing ? 'Ubah' : 'Buat') . ' Rekapan | ' . setting('brand_name'))

@section('content')
<div x-data="recapSheet({{ Js::from($recap->columns ?? []) }}, {{ Js::from(collect($recap->rows ?? [])->map(fn ($r) => ['key' => $r['key'] ?? '', 'cells' => (array) ($r['cells'] ?? [])])->values()) }})"
     class="flex flex-col h-full min-h-0">

    {{-- Bar atas: judul + aksi --}}
    <div class="shrink-0 px-4 sm:px-6 py-4 border-b border-[#E2E8F0] dark:border-[#404854] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white dark:bg-[#23252b]">
        <div class="flex items-center gap-3 min-w-0 flex-1">
            <a href="{{ $editing ? route('admin.recaps.show', $recap) : route('admin.recaps.index') }}"
               class="shrink-0 text-[#94A3B8] hover:text-[#1E293B] dark:hover:text-[#f1f5f9] p-1.5 rounded-lg hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] transition-colors" aria-label="Kembali">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <input type="text" x-model="name" maxlength="255" x-ref="title"
                   placeholder="Judul rekapan"
                   class="min-w-0 flex-1 text-lg font-semibold bg-transparent border-0 border-b-2 border-transparent focus:border-orange-400 focus:outline-none text-[#1E293B] dark:text-[#f1f5f9] px-0 py-1 placeholder-[#94A3B8]">
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <span class="text-sm text-[#64748B] dark:text-[#cbd5e1] whitespace-nowrap">
                Grand total <span class="font-semibold text-orange-600 dark:text-orange-400 tabular-nums" x-text="formatRp(grandTotal())"></span>
            </span>
        </div>
    </div>

    @if($errors->any())
        <div class="shrink-0 mx-4 sm:mx-6 mt-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-300 text-sm rounded-lg p-4">
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
          @submit="saving = true" class="flex-1 flex flex-col min-h-0">
        @csrf
        @if($editing) @method('PUT') @endif
        <input type="hidden" name="name" :value="name">

        {{-- Toolbar grid --}}
        <div class="shrink-0 px-4 sm:px-6 py-2.5 border-b border-[#E2E8F0] dark:border-[#404854] bg-[#F8FAFC] dark:bg-[#2e323b] flex flex-wrap items-center gap-2">
            <button type="button" @click="addRow()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
                <x-icon name="plus" class="w-4 h-4" /> Baris
            </button>
            <button type="button" @click="addColumn()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg font-medium hover:border-orange-300 transition-colors">
                <x-icon name="plus" class="w-4 h-4" /> Kolom
            </button>
            <span class="hidden sm:inline text-xs text-[#94A3B8] ml-1">Klik header kolom untuk ubah tipe (Teks / Angka / Rupiah) &amp; total.</span>
        </div>

        {{-- Grid --}}
        <div class="flex-1 min-h-0 overflow-auto">
            <table class="border-separate border-spacing-0 text-sm w-max min-w-full">
                <thead class="sticky top-0 z-20">
                    <tr>
                        <th class="sticky left-0 z-30 w-11 bg-[#F1F5F9] dark:bg-[#2e323b] border-b border-r border-[#E2E8F0] dark:border-[#404854] text-xs text-[#94A3B8] font-medium"></th>
                        <template x-for="(col, ci) in columns" :key="col.key">
                            <th class="min-w-[180px] align-top bg-[#F1F5F9] dark:bg-[#2e323b] border-b border-r border-[#E2E8F0] dark:border-[#404854] p-0">
                                <input type="hidden" :name="`columns[${ci}][key]`" :value="col.key">
                                <input type="text" :name="`columns[${ci}][label]`" x-model="col.label"
                                       :data-collabel="col.key" placeholder="Nama kolom"
                                       class="w-full px-3 py-2 text-sm font-semibold bg-transparent focus:outline-none focus:bg-white dark:focus:bg-[#1a1a1a] text-[#1E293B] dark:text-[#f1f5f9] focus:ring-2 focus:ring-inset focus:ring-orange-300">
                                <div class="flex items-center gap-1.5 px-2 pb-2">
                                    <select :name="`columns[${ci}][type]`" x-model="col.type"
                                            class="text-xs border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] text-[#64748B] dark:text-[#cbd5e1] rounded px-1.5 py-1 focus:outline-none focus:ring-2 focus:ring-orange-300">
                                        <option value="text">Teks</option>
                                        <option value="number">Angka</option>
                                        <option value="currency">Rupiah</option>
                                    </select>
                                    <label class="inline-flex items-center gap-1 text-xs text-[#64748B] dark:text-[#cbd5e1] cursor-pointer" title="Ikut grand total">
                                        <input type="checkbox" value="1" :name="`columns[${ci}][total]`" x-model="col.total"
                                               class="w-3.5 h-3.5 rounded border-[#E2E8F0] text-orange-500 focus:ring-orange-300">
                                        Total
                                    </label>
                                    <button type="button" @click="removeColumn(ci)" x-show="columns.length > 1"
                                            class="ml-auto text-[#94A3B8] hover:text-red-600 p-0.5" aria-label="Hapus kolom">
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </th>
                        </template>
                        <th class="w-10 bg-[#F1F5F9] dark:bg-[#2e323b] border-b border-[#E2E8F0] dark:border-[#404854]"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, ri) in rows" :key="row.key">
                        <tr class="group">
                            <td class="sticky left-0 z-10 bg-[#F8FAFC] dark:bg-[#2e323b] group-hover:bg-[#F1F5F9] dark:group-hover:bg-[#404854] border-b border-r border-[#E2E8F0] dark:border-[#404854] text-center text-xs text-[#94A3B8] select-none" x-text="ri + 1"></td>
                            <input type="hidden" :name="`rows[${ri}][key]`" :value="row.key">
                            <template x-for="(col, ci) in columns" :key="col.key">
                                <td class="p-0 border-b border-r border-[#E2E8F0] dark:border-[#404854]">
                                    <input :type="isNum(col) ? 'number' : 'text'"
                                           :name="`rows[${ri}][cells][${col.key}]`"
                                           x-model="row.cells[col.key]"
                                           :data-cell="`r${ri}-c${ci}`"
                                           @keydown.enter="move($event, ri, ci, 'down')"
                                           @keydown.arrow-down="move($event, ri, ci, 'down')"
                                           @keydown.arrow-up="move($event, ri, ci, 'up')"
                                           :class="isNum(col) ? 'text-right tabular-nums' : ''"
                                           class="w-full h-10 px-3 bg-transparent focus:outline-none focus:bg-white dark:focus:bg-[#1a1a1a] text-[#1E293B] dark:text-[#f1f5f9] focus:ring-2 focus:ring-inset focus:ring-orange-300">
                                </td>
                            </template>
                            <td class="border-b border-[#E2E8F0] dark:border-[#404854] text-center">
                                <button type="button" @click="removeRow(ri)"
                                        class="text-[#CBD5E1] hover:text-red-600 p-1 opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity" aria-label="Hapus baris">
                                    <x-icon name="trash" class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="rows.length === 0">
                        <td></td>
                        <td :colspan="columns.length + 1" class="px-4 py-10 text-center text-[#64748B] dark:text-[#cbd5e1]">
                            Belum ada baris. Klik <span class="font-medium">+ Baris</span> untuk mulai.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Bar bawah: total + simpan --}}
        <div class="shrink-0 px-4 sm:px-6 py-3 border-t border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] flex items-center justify-between gap-3">
            <div class="text-sm text-[#64748B] dark:text-[#cbd5e1]">
                <span x-text="rows.length"></span> baris · <span x-text="columns.length"></span> kolom
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ $editing ? route('admin.recaps.show', $recap) : route('admin.recaps.index') }}"
                   class="px-4 py-2 text-sm text-[#64748B] dark:text-[#cbd5e1] hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] rounded-lg font-medium transition-colors">
                    Batal
                </a>
                <button type="button" @click="addRow()"
                        class="hidden sm:inline-flex px-4 py-2 text-sm bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg font-medium hover:border-orange-300 transition-colors">
                    Tambah Baris
                </button>
                <button type="submit" :disabled="saving"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 disabled:opacity-70 disabled:cursor-not-allowed text-white font-medium rounded-lg shadow-sm transition-colors">
                    <x-spinner x-show="saving" x-cloak />
                    <span x-text="saving ? 'Menyimpan…' : 'Simpan'">Simpan</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@include('admin.recaps._script')