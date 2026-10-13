@extends('layouts.admin')
@section('title', 'Rekapan | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">
    <x-page-header title="Rekapan" subtitle="Lembar rekap manual. Kolom dan baris bebas, grand total dihitung otomatis dari kolom bertanda Total.">
        <x-btn as="a" :href="route('admin.recaps.create')" icon="plus">Buat Rekapan</x-btn>
    </x-page-header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($recaps as $recap)
            @php($totals = $recap->totals())
            <a href="{{ route('admin.recaps.show', $recap) }}"
               class="nb-card group p-5 flex flex-col gap-3 hover:-translate-x-0.5 hover:-translate-y-0.5 transition-transform">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="font-black text-[16px] leading-snug text-[#141414] dark:text-[#FAF6EF] truncate">{{ $recap->name }}</h2>
                        <p class="text-xs font-bold text-[#475569] dark:text-[#D6D3D1] mt-1.5 num">
                            {{ count($recap->rows ?? []) }} baris, {{ count($recap->columns ?? []) }} kolom
                        </p>
                    </div>
                    <span class="shrink-0 w-9 h-9 border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[8px] flex items-center justify-center bg-white dark:bg-[#262626] text-[#141414] dark:text-[#FAF6EF] group-hover:bg-[#FFC800] group-hover:text-[#141414] transition-colors" aria-hidden="true">
                        <x-icon name="chevron-right" class="w-5 h-5" />
                    </span>
                </div>
                @if(! empty($totals['perColumn']))
                    <div class="mt-1 -mx-5 -mb-5 px-5 py-3 nb-money-strip border-x-0! border-b-0! rounded-b-[10px] flex items-baseline justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-[0.1em]">Grand total</span>
                        <span class="font-black num text-[15px]">{{ format_price((int) $totals['grand']) }}</span>
                    </div>
                @else
                    <p class="text-xs font-bold text-[#475569] dark:text-[#D6D3D1]">Belum ada kolom angka bertanda Total.</p>
                @endif
                <p class="text-[11px] font-medium text-[#475569] dark:text-[#D6D3D1]">Diperbarui {{ $recap->updated_at->diffForHumans() }}</p>
            </a>
        @empty
            <div class="sm:col-span-2 lg:col-span-3">
                <x-card>
                    <x-empty-state icon="table" title="Belum ada rekapan.">
                        Buat lembar pertama untuk mulai merekap pesanan manual.
                        <div class="mt-4">
                            <x-btn as="a" :href="route('admin.recaps.create')" icon="plus">Buat Rekapan</x-btn>
                        </div>
                    </x-empty-state>
                </x-card>
            </div>
        @endforelse
    </div>
</div>
@endsection
