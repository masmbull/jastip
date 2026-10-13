@extends('layouts.admin')
@section('title', ($editing ? 'Ubah' : 'Buat') . ' Rekapan | ' . setting('brand_name'))

@section('content')
<div x-data="recapSheet({{ Js::from($recap->columns ?? []) }}, {{ Js::from(collect($recap->rows ?? [])->map(fn ($r) => ['key' => $r['key'] ?? '', 'cells' => (array) ($r['cells'] ?? [])])->values()) }})"
     class="p-4 sm:p-6 space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-up">
        <div>
            <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">{{ $editing ? 'Ubah Rekapan' : 'Buat Rekapan' }}</h1>
            <p class="text-sm text-[#94A3B8] dark:text-[#cbd5e1] mt-1">Tambah kolom & baris bebas. Kolom angka bisa dicentang supaya ikut grand total.</p>
        </div>
        <a href="{{ $editing ? route('admin.recaps.show', $recap) : route('admin.recaps.index') }}"
           class="px-4 py-2 text-sm bg-[#E2E8F0] dark:bg-[#2e323b] text-[#64748B] dark:text-[#cbd5e1] rounded-lg hover:bg-[#CBD5E1] dark:hover:bg-[#404854] font-medium text-center transition-colors">
            Batal
        </a>
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
          @submit="saving = true" class="space-y-4">
        @csrf
        @if($editing) @method('PUT') @endif

        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] p-4">
            <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Judul Rekapan *</label>
            <input type="text" name="name" x-model="name" required maxlength="255"
                   placeholder="mis. Rekapan PO 12 Okt"
                   class="w-full max-w-lg px-3 py-2 text-sm border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
        </div>

        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] overflow-hidden">
            <div class="flex flex-wrap items-center gap-2 p-4 border-b border-[#E2E8F0] dark:border-[#404854] bg-[#F8FAFC] dark:bg-[#2e323b]">
                <button type="button" @click="addColumn()"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
                    <x-icon name="plus" class="w-4 h-4" /> Tambah Kolom
                </button>
                <button type="button" @click="addRow()"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm bg-[#E2E8F0] dark:bg-[#404854] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg font-medium transition-colors">
                    <x-icon name="plus" class="w-4 h-4" /> Tambah Baris
                </button>
                <span class="ml-auto text-sm text-[#64748B] dark:text-[#cbd5e1]">
                    Grand total: <span class="font-semibold text-orange-600 dark:text-orange-400" x-text="formatRp(grandTotal())"></span>
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] align-top">
                            <th class="px-3 py-2 text-left text-xs font-semibold text-[#64748B] dark:text-[#cbd5e1] w-10">#</th>
                            <template x-for="(col, ci) in columns" :key="col.key">
                                <th class="px-2 py-2 min-w-[160px]">
                                    <input type="hidden" :name="`columns[${ci}][key]`" :value="col.key">
                                    <input type="text" :name="`columns[${ci}][label]`" x-model="col.label"
                                           placeholder="Nama kolom"
                                           class="w-full px-2 py-1.5 text-sm font-medium border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] text-[#1E293B] dark:text-[#f1f5f9] rounded focus:outline-none focus:ring-2 focus:ring-orange-300">
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <select :name="`columns[${ci}][type]`" x-model="col.type"
                                                class="text-xs border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] text-[#64748B] dark:text-[#cbd5e1] rounded px-1.5 py-1">
                                            <option value="text">Teks</option>
                                            <option value="number">Angka</option>
                                        </select>
                                        <label class="inline-flex items-center gap-1 text-xs text-[#64748B] dark:text-[#cbd5e1] cursor-pointer">
                                            <input type="checkbox" value="1" :name="`columns[${ci}][total]`" x-model="col.total"
                                                   class="w-3.5 h-3.5 rounded border-[#E2E8F0]">
                                            Total
                                        </label>
                                        <button type="button" @click="removeColumn(ci)"
                                                class="ml-auto text-red-500 hover:text-red-700 p-0.5" aria-label="Hapus kolom">
                                            <x-icon name="trash" class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                </th>
                            </template>
                            <th class="px-2 py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, ri) in rows" :key="row.key">
                            <tr class="border-b border-[#E2E8F0] dark:border-[#404854]">
                                <td class="px-3 py-2 text-xs text-[#94A3B8]" x-text="ri + 1"></td>
                                <input type="hidden" :name="`rows[${ri}][key]`" :value="row.key">
                                <template x-for="col in columns" :key="col.key">
                                    <td class="px-2 py-2">
                                        <input :type="isNum(col) ? 'number' : 'text'"
                                               :name="`rows[${ri}][cells][${col.key}]`"
                                               x-model="row.cells[col.key]"
                                               :class="isNum(col) ? 'text-right' : ''"
                                               class="w-full px-2 py-1.5 text-sm border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] text-[#1E293B] dark:text-[#f1f5f9] rounded focus:outline-none focus:ring-2 focus:ring-orange-300">
                                    </td>
                                </template>
                                <td class="px-2 py-2 text-right">
                                    <button type="button" @click="removeRow(ri)"
                                            class="text-red-500 hover:text-red-700 p-0.5" aria-label="Hapus baris">
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="rows.length === 0">
                            <td :colspan="columns.length + 2" class="px-4 py-10 text-center text-[#64748B] dark:text-[#cbd5e1]">
                                Belum ada baris. Klik <span class="font-medium">Tambah Baris</span>.
                            </td>
                        </tr>
                    </tbody>
                    <tfoot x-show="columns.length > 0">
                        <tr class="bg-[#F8FAFC] dark:bg-[#2e323b] font-semibold">
                            <td class="px-3 py-2 text-xs uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1]" colspan="2">Total</td>
                            <template x-for="col in columns" :key="col.key">
                                <td class="px-2 py-2 text-right text-[#1E293B] dark:text-[#f1f5f9]"
                                    x-text="(isNum(col) && col.total) ? formatRp(colTotal(col.key)) : (isNum(col) ? formatRp(colTotal(col.key)) : '')"></td>
                            </template>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
            <button type="submit" :disabled="saving"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-orange-500 hover:bg-orange-600 disabled:opacity-70 disabled:cursor-not-allowed text-white font-medium rounded-lg shadow-sm transition-colors">
                <x-spinner x-show="saving" x-cloak />
                <span x-text="saving ? 'Menyimpan…' : 'Simpan Rekapan'">Simpan Rekapan</span>
            </button>
        </div>
    </form>
</div>
@endsection

@include('admin.recaps._script')