@extends('layouts.admin')
@section('title', $recap->name . ' | ' . setting('brand_name'))

@section('content')
@php($totals = $recap->totals())
@php($rowDocs = collect($recap->rows ?? [])->map(fn ($r, $i) => $recap->invoiceForRow($r, $i + 1))->values())
<div class="space-y-5" x-data="{ busy: null }" data-recap-docs='@json($rowDocs)'>
    <x-page-header>
        <x-slot:title>{{ $recap->name }}</x-slot:title>
        <x-slot:subtitle>{{ count($recap->rows ?? []) }} baris, {{ count($recap->columns ?? []) }} kolom, diperbarui {{ $recap->updated_at->diffForHumans() }}</x-slot:subtitle>

        <x-btn as="a" :href="route('admin.recaps.index')" variant="ghost">Semua Rekapan</x-btn>
        <x-btn as="a" :href="route('admin.recaps.edit', $recap)" icon="pencil">Ubah</x-btn>
    </x-page-header>

    <div class="nb-table-shell">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm min-w-[560px]">
                <thead>
                    <tr class="nb-table-head text-left text-xs uppercase tracking-wide">
                        <th scope="col" class="px-3 py-3 w-12 text-center font-black">#</th>
                        @php($isNum = fn ($c) => in_array($c['type'] ?? 'text', ['number', 'currency'], true))
                        @foreach($recap->columns ?? [] as $col)
                            <th scope="col" class="px-3 py-3 font-black {{ $isNum($col) ? 'text-right' : '' }}">{{ $col['label'] }}</th>
                        @endforeach
                        <th scope="col" class="px-3 py-3 font-black text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-[#141414] dark:divide-[#FAF6EF]">
                    @forelse($recap->rows ?? [] as $i => $row)
                        <tr class="bg-white dark:bg-[#1E1E1E] hover:bg-[#FFF7D6] dark:hover:bg-[#262626] transition-colors">
                            <td class="px-3 py-3 text-xs font-black text-[#475569] dark:text-[#D6D3D1] num text-center">{{ $i + 1 }}</td>
                            @foreach($recap->columns ?? [] as $col)
                                @php($val = $row['cells'][$col['key']] ?? '')
                                <td class="px-3 py-3 {{ $isNum($col) ? 'text-right num font-bold' : 'font-medium' }} text-[#141414] dark:text-[#FAF6EF]">
                                    {{ $isNum($col) ? (($col['type'] ?? '') === 'currency' ? format_price((int) $val) : number_format((float) $val, 0, ',', '.')) : $val }}
                                </td>
                            @endforeach
                            <td class="px-3 py-3 text-right">
                                <button type="button"
                                        x-data
                                        :disabled="busy === {{ $i }}"
                                        @click="busy = {{ $i }}; await jdRecapInvoice($el, $el.closest('[data-recap-docs]').dataset.recapDocs, {{ $i }}); busy = null"
                                        class="nb-btn inline-flex items-center gap-1.5 px-3 py-2 text-xs bg-[#25D366] text-[#141414] min-h-[40px] disabled:opacity-60"
                                        title="Kirim invoice baris ini via WhatsApp"
                                        aria-label="Kirim invoice baris {{ $i + 1 }} via WhatsApp">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                                    </svg>
                                    <span x-text="busy === {{ $i }} ? 'Menyiapkan...' : 'Kirim'">Kirim</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($recap->columns ?? []) + 2 }}" class="bg-white dark:bg-[#1E1E1E]">
                                <x-empty-state icon="table" title="Belum ada baris.">
                                    Ubah rekapan untuk menambah data.
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(! empty($totals['perColumn']))
                    <tfoot>
                        <tr class="bg-[#FAF6EF] dark:bg-[#262626] border-t-2 border-[#141414] dark:border-[#FAF6EF] font-bold">
                            <td class="px-3 py-3 text-center text-[11px] font-black uppercase tracking-wider text-[#475569] dark:text-[#D6D3D1]">Total</td>
                            @foreach($recap->columns ?? [] as $col)
                                <td class="px-3 py-3 text-right num text-[#141414] dark:text-[#FAF6EF]">
                                    @if($isNum($col) && ! empty($col['total']))
                                        {{ ($col['type'] ?? '') === 'currency' ? format_price((int) ($totals['perColumn'][$col['key']] ?? 0)) : number_format((float) ($totals['perColumn'][$col['key']] ?? 0), 0, ',', '.') }}
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-3 py-3"></td>
                        </tr>
                        <tr class="nb-money-strip">
                            <td colspan="{{ count($recap->columns ?? []) }}" class="px-3 py-3 text-right font-black uppercase tracking-wide text-[13px]">Grand Total</td>
                            <td class="px-3 py-3 text-right font-black num text-[15px]">{{ format_price((int) $totals['grand']) }}</td>
                            <td class="px-3 py-3"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.recaps.destroy', $recap) }}"
          data-confirm="Hapus rekapan &quot;{{ $recap->name }}&quot;? Tindakan ini tidak bisa dibatalkan."
          data-async class="flex justify-end">
        @csrf @method('DELETE')
        <button type="submit"
                class="nb-btn px-4 py-2 text-sm bg-white dark:bg-[#1E1E1E] text-[#991B1B] dark:text-[#FECACA] min-h-[44px]">
            <x-icon name="trash" class="w-4 h-4" /> Hapus Rekapan
        </button>
    </form>
</div>
@endsection
