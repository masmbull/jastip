@extends('layouts.admin')
@section('title', 'Rekapan | ' . setting('brand_name'))

@section('content')
<div class="p-4 sm:p-6 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-up">
        <div>
            <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Rekapan</h1>
            <p class="text-sm text-[#94A3B8] dark:text-[#cbd5e1] mt-1">Lembar rekap manual: kolom & baris bebas, grand total otomatis.</p>
        </div>
        <a href="{{ route('admin.recaps.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
            <x-icon name="plus" class="w-4 h-4" /> Buat Rekapan
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 stagger">
        @forelse($recaps as $recap)
            @php($totals = $recap->totals())
            <a href="{{ route('admin.recaps.show', $recap) }}"
               class="group bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] p-5 hover:border-orange-300 dark:hover:border-orange-500/50 hover:shadow-md transition-all">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] truncate">{{ $recap->name }}</h2>
                        <p class="text-xs text-[#94A3B8] dark:text-[#64748B] mt-1">
                            {{ count($recap->rows ?? []) }} baris · {{ count($recap->columns ?? []) }} kolom
                        </p>
                    </div>
                    <span class="shrink-0 text-[#94A3B8] group-hover:text-orange-500 transition-colors">
                        <x-icon name="chevron-right" class="w-5 h-5" />
                    </span>
                </div>
                @if(! empty($totals['perColumn']))
                    <div class="mt-4 pt-3 border-t border-[#E2E8F0] dark:border-[#404854] flex items-baseline justify-between">
                        <span class="text-xs text-[#64748B] dark:text-[#cbd5e1]">Grand total</span>
                        <span class="font-semibold text-orange-600 dark:text-orange-400 tabular-nums">{{ format_price((int) $totals['grand']) }}</span>
                    </div>
                @endif
                <p class="mt-3 text-[11px] text-[#94A3B8] dark:text-[#64748B]">Diperbarui {{ $recap->updated_at->diffForHumans() }}</p>
            </a>
        @empty
            <div class="sm:col-span-2 lg:col-span-3 bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] p-12 text-center">
                <div class="w-16 h-16 mx-auto bg-[#F1F5F9] dark:bg-[#2e323b] rounded-full mb-4 flex items-center justify-center">
                    <x-icon name="table" class="w-8 h-8 text-[#94A3B8]" />
                </div>
                <p class="text-[#64748B] dark:text-[#cbd5e1] font-medium">Belum ada rekapan.</p>
                <p class="text-sm text-[#94A3B8] dark:text-[#64748b] mt-1">Buat lembar pertama untuk mulai merekap pesanan manual.</p>
                <a href="{{ route('admin.recaps.create') }}"
                   class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
                    <x-icon name="plus" class="w-4 h-4" /> Buat Rekapan
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection