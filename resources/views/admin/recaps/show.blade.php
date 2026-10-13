@extends('layouts.admin')
@section('title', $recap->name . ' | ' . setting('brand_name'))

@section('content')
@php($totals = $recap->totals())
<div class="p-4 sm:p-6 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-up">
        <div>
            <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">{{ $recap->name }}</h1>
            <p class="text-sm text-[#94A3B8] dark:text-[#cbd5e1] mt-1">
                {{ count($recap->rows ?? []) }} baris · {{ count($recap->columns ?? []) }} kolom ·
                diperbarui {{ $recap->updated_at->diffForHumans() }}
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.recaps.index') }}"
               class="px-4 py-2 text-sm bg-[#E2E8F0] dark:bg-[#2e323b] text-[#64748B] dark:text-[#cbd5e1] rounded-lg hover:bg-[#CBD5E1] dark:hover:bg-[#404854] font-medium transition-colors">
                Semua Rekapan
            </a>
            <a href="{{ route('admin.recaps.edit', $recap) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
                <x-icon name="pencil" class="w-4 h-4" /> Ubah
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] overflow-hidden animate-fade-up transition-colors" style="animation-delay: 80ms">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-3 py-3 w-10">#</th>
                        @foreach($recap->columns ?? [] as $col)
                            <th class="px-3 py-3 {{ ($col['type'] ?? 'text') === 'number' ? 'text-right' : '' }}">{{ $col['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854] stagger">
                    @forelse($recap->rows ?? [] as $i => $row)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-3 py-3 text-xs text-[#94A3B8]">{{ $i + 1 }}</td>
                            @foreach($recap->columns ?? [] as $col)
                                @php($val = $row['cells'][$col['key']] ?? '')
                                <td class="px-3 py-3 {{ ($col['type'] ?? 'text') === 'number' ? 'text-right tabular-nums' : 'text-[#1E293B] dark:text-[#f1f5f9]' }}">
                                    {{ ($col['type'] ?? 'text') === 'number' ? format_price((int) $val) : $val }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($recap->columns ?? []) + 1 }}" class="px-4 py-12 text-center text-[#64748B] dark:text-[#cbd5e1]">
                                Belum ada baris. Ubah rekapan untuk menambah data.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(! empty($totals['perColumn']))
                    <tfoot>
                        <tr class="bg-[#F8FAFC] dark:bg-[#2e323b] font-semibold">
                            <td class="px-3 py-3"></td>
                            @foreach($recap->columns ?? [] as $col)
                                <td class="px-3 py-3 text-right text-[#1E293B] dark:text-[#f1f5f9] tabular-nums">
                                    @if(($col['type'] ?? 'text') === 'number' && ! empty($col['total']))
                                        {{ format_price((int) ($totals['perColumn'][$col['key']] ?? 0)) }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        <tr class="bg-orange-50 dark:bg-orange-500/10">
                            <td colspan="{{ count($recap->columns ?? []) }}" class="px-3 py-3 text-right font-bold text-[#1E293B] dark:text-[#f1f5f9]">Grand Total</td>
                            <td class="px-3 py-3 text-right font-bold text-orange-600 dark:text-orange-400 tabular-nums">{{ format_price((int) $totals['grand']) }}</td>
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
                class="inline-flex items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg font-medium transition-colors">
            <x-icon name="trash" class="w-4 h-4" /> Hapus Rekapan
        </button>
    </form>
</div>
@endsection