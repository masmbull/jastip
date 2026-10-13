@extends('layouts.admin')
@section('title', $recap->name . ' | ' . setting('brand_name'))

@section('content')
@php($totals = $recap->totals())
<div class="space-y-5">
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($recap->columns ?? []) + 1 }}" class="bg-white dark:bg-[#1E1E1E]">
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
                        </tr>
                        <tr class="nb-money-strip">
                            <td colspan="{{ count($recap->columns ?? []) }}" class="px-3 py-3 text-right font-black uppercase tracking-wide text-[13px]">Grand Total</td>
                            <td class="px-3 py-3 text-right font-black num text-[15px]">{{ format_price((int) $totals['grand']) }}</td>
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
