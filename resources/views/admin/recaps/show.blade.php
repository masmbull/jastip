@extends('layouts.admin')
@section('title', $recap->name . ' | ' . setting('brand_name'))

@section('content')
@php($totals = $recap->totals())
<div class="space-y-6">
    <x-page-header class="animate-fade-up">
        <x-slot:title>{{ $recap->name }}</x-slot:title>
        <x-slot:subtitle>{{ count($recap->rows ?? []) }} baris, {{ count($recap->columns ?? []) }} kolom, diperbarui {{ $recap->updated_at->diffForHumans() }}</x-slot:subtitle>

        <x-btn as="a" :href="route('admin.recaps.index')" variant="ghost">Semua Rekapan</x-btn>
        <x-btn as="a" :href="route('admin.recaps.edit', $recap)" icon="pencil">Ubah</x-btn>
    </x-page-header>

    <x-card padded="false" class="animate-fade-up" style="animation-delay: 80ms">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm min-w-[480px]">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-3 py-3 w-10">#</th>
                        @php($isNum = fn ($c) => in_array($c['type'] ?? 'text', ['number', 'currency'], true))
                        @foreach($recap->columns ?? [] as $col)
                            <th class="px-3 py-3 {{ $isNum($col) ? 'text-right' : '' }}">{{ $col['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854] stagger">
                    @forelse($recap->rows ?? [] as $i => $row)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-3 py-3 text-xs text-[#64748B] dark:text-[#cbd5e1] num">{{ $i + 1 }}</td>
                            @foreach($recap->columns ?? [] as $col)
                                @php($val = $row['cells'][$col['key']] ?? '')
                                <td class="px-3 py-3 {{ $isNum($col) ? 'text-right num text-[#1E293B] dark:text-[#f1f5f9]' : 'text-[#1E293B] dark:text-[#f1f5f9]' }}">
                                    {{ $isNum($col) ? (($col['type'] ?? '') === 'currency' ? format_price((int) $val) : number_format((float) $val, 0, ',', '.')) : $val }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($recap->columns ?? []) + 1 }}">
                                <x-empty-state icon="table" title="Belum ada baris.">
                                    Ubah rekapan untuk menambah data.
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(! empty($totals['perColumn']))
                    <tfoot>
                        <tr class="bg-[#F8FAFC] dark:bg-[#2e323b] font-semibold">
                            <td class="px-3 py-3"></td>
                            @foreach($recap->columns ?? [] as $col)
                                <td class="px-3 py-3 text-right text-[#1E293B] dark:text-[#f1f5f9] num">
                                    @if($isNum($col) && ! empty($col['total']))
                                        {{ ($col['type'] ?? '') === 'currency' ? format_price((int) ($totals['perColumn'][$col['key']] ?? 0)) : number_format((float) ($totals['perColumn'][$col['key']] ?? 0), 0, ',', '.') }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        <tr class="bg-orange-50 dark:bg-orange-500/10">
                            <td colspan="{{ count($recap->columns ?? []) }}" class="px-3 py-3 text-right font-bold text-[#1E293B] dark:text-[#f1f5f9]">Grand Total</td>
                            <td class="px-3 py-3 text-right font-bold text-orange-600 dark:text-orange-400 num">{{ format_price((int) $totals['grand']) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </x-card>

    <form method="POST" action="{{ route('admin.recaps.destroy', $recap) }}"
          data-confirm="Hapus rekapan &quot;{{ $recap->name }}&quot;? Tindakan ini tidak bisa dibatalkan."
          data-async class="flex justify-end">
        @csrf @method('DELETE')
        <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-red-300">
            <x-icon name="trash" class="w-4 h-4" /> Hapus Rekapan
        </button>
    </form>
</div>
@endsection