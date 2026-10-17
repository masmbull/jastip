@extends('layouts.admin')
@section('title', 'Status Sistem | ' . setting('brand_name'))

@php
    // Ikon dipetakan ke set <x-icon> (bukan emoji) supaya seragam dengan UI lain.
    $iconFor = [
        'Database' => 'server',
        'File Storage' => 'cube',
        'Cache' => 'bolt',
        'Session' => 'lock',
        'WhatsApp API' => 'chat',
    ];
    $statusTone = fn (string $s) => $s === 'online' ? 'success' : ($s === 'offline' ? 'danger' : 'warning');
    $statusLabel = fn (string $s) => $s === 'online' ? 'Normal' : ($s === 'offline' ? 'Gangguan' : 'Perlu dicek');

    $online = collect($services)->where('status', 'online')->count();
    $total = count($services);
    $allOk = $online === $total;
@endphp

@section('content')
<div class="space-y-6">

    <x-page-header title="Status Sistem" subtitle="Kesehatan aplikasi dan layanan yang terhubung">
        @if($monitorServices->isNotEmpty())
            <form method="POST" action="{{ route('admin.services.checkAll') }}">
                @csrf
                <x-btn type="submit" variant="outline" icon="arrow-path">Cek Semua</x-btn>
            </form>
        @endif
    </x-page-header>

    {{-- Ringkasan besar: satu angka, satu status --}}
    <div class="nb-card p-5 sm:p-6 animate-fade-up">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
            <div class="flex items-center gap-3">
                <span class="h-12 w-12 shrink-0 rounded-[10px] border-2 border-[#141414] dark:border-[#FAF6EF] flex items-center justify-center {{ $allOk ? 'bg-[#05E17A]' : 'bg-[#FF4D50]' }}">
                    <x-icon name="{{ $allOk ? 'check-circle' : 'warning' }}" class="w-6 h-6 text-[#141414]" />
                </span>
                <div>
                    <p class="text-2xl font-black leading-none">{{ $online }}/{{ $total }}</p>
                    <p class="text-sm font-bold text-[#475569] dark:text-[#D6D3D1]">layanan normal</p>
                </div>
            </div>
            <p class="sm:ml-auto max-w-md text-sm font-medium text-[#475569] dark:text-[#D6D3D1]">
                {{ $allOk
                    ? 'Semua sistem berjalan normal. Tidak ada tindakan yang diperlukan.'
                    : 'Ada layanan yang tidak normal. Periksa daftar di bawah.' }}
            </p>
        </div>
    </div>

    {{-- Kartu per layanan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 stagger">
        @foreach($services as $service)
            <div class="nb-card p-5 flex items-start gap-3.5">
                <span class="h-11 w-11 shrink-0 rounded-[10px] border-2 border-[#141414] dark:border-[#FAF6EF] bg-white dark:bg-[#121212] flex items-center justify-center">
                    <x-icon :name="$iconFor[$service['name']] ?? 'server'" class="w-5 h-5" />
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <h2 class="font-black text-[15px] leading-tight">{{ $service['name'] }}</h2>
                        <x-badge :tone="$statusTone($service['status'])">{{ $statusLabel($service['status']) }}</x-badge>
                    </div>
                    @if(!empty($service['message']))
                        <p class="text-xs font-medium text-[#475569] dark:text-[#D6D3D1] mt-1">{{ $service['message'] }}</p>
                    @endif
                    @if(!empty($service['last_update']))
                        <p class="text-xs font-medium text-[#64748B] dark:text-[#A8A29E] mt-1">Terakhir: {{ $service['last_update'] }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Kapasitas: disk & memori (bar, biar cepat terbaca) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="nb-card p-5">
            <div class="flex items-baseline justify-between mb-2">
                <h2 class="font-black">Penyimpanan (Disk)</h2>
                <span class="text-sm font-bold num">{{ $systemInfo['disk']['percent'] }}%</span>
            </div>
            <div class="h-4 w-full rounded-[6px] border-2 border-[#141414] dark:border-[#FAF6EF] overflow-hidden bg-white dark:bg-[#121212]">
                <div class="h-full {{ $systemInfo['disk']['percent'] > 85 ? 'bg-[#FF4D50]' : 'bg-[#05E17A]' }}"
                     style="width: {{ min(100, $systemInfo['disk']['percent']) }}%"></div>
            </div>
            <p class="text-xs font-medium text-[#475569] dark:text-[#D6D3D1] mt-2 num">
                Terpakai {{ $systemInfo['disk']['used'] }} dari {{ $systemInfo['disk']['total'] }}
            </p>
        </div>

        <div class="nb-card p-5">
            <h2 class="font-black mb-2">Memori (PHP)</h2>
            <dl class="text-sm space-y-1">
                <div class="flex justify-between"><dt class="text-[#475569] dark:text-[#D6D3D1]">Dipakai sekarang</dt><dd class="font-bold num">{{ $systemInfo['memory']['current'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-[#475569] dark:text-[#D6D3D1]">Puncak</dt><dd class="font-bold num">{{ $systemInfo['memory']['peak'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-[#475569] dark:text-[#D6D3D1]">Batas</dt><dd class="font-bold num">{{ $systemInfo['memory']['limit'] }}</dd></div>
            </dl>
        </div>
    </div>

    {{-- Layanan Terpantau (dulu halaman terpisah "Daftar Layanan") --}}
    @if($monitorServices->isNotEmpty())
        <div class="nb-table-shell">
            <div class="px-5 py-4 border-b-2 border-[#141414] dark:border-[#FAF6EF] flex items-center justify-between gap-3">
                <h2 class="font-black">Layanan Terpantau</h2>
                <span class="text-xs font-bold text-[#475569] dark:text-[#D6D3D1]">Cek manual menyimpan waktu & galat terakhir</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-sm">
                    <thead>
                        <tr class="nb-table-head text-left text-xs uppercase tracking-wide">
                            <th scope="col" class="px-4 py-3 font-black">Layanan</th>
                            <th scope="col" class="px-4 py-3 font-black">Status</th>
                            <th scope="col" class="px-4 py-3 font-black">Respons</th>
                            <th scope="col" class="px-4 py-3 font-black">Terakhir Dicek</th>
                            <th scope="col" class="px-4 py-3 font-black">Catatan</th>
                            <th scope="col" class="px-4 py-3 font-black text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-[#141414] dark:divide-[#FAF6EF]">
                        @foreach($monitorServices as $service)
                            <tr class="bg-white dark:bg-[#1E1E1E]">
                                <td class="px-4 py-3 font-black">{{ $service->name }}</td>
                                <td class="px-4 py-3">
                                    <x-badge :tone="$statusTone($service->status)">{{ $statusLabel($service->status) }}</x-badge>
                                </td>
                                <td class="px-4 py-3 num">{{ $service->response_time !== null ? round($service->response_time, 2) . ' ms' : '-' }}</td>
                                <td class="px-4 py-3 text-[#475569] dark:text-[#D6D3D1] num">{{ $service->last_checked?->diffForHumans() ?? 'Belum pernah' }}</td>
                                <td class="px-4 py-3 text-xs text-[#475569] dark:text-[#D6D3D1] max-w-[240px] truncate" title="{{ $service->error_message }}">{{ $service->error_message ?: '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('admin.services.check', $service) }}">
                                        @csrf
                                        <x-btn type="submit" variant="ghost" icon="arrow-path">Cek</x-btn>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Info sistem ringkas --}}
    <div class="nb-card p-5">
        <h2 class="font-black mb-3">Informasi Sistem</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div><dt class="text-[#475569] dark:text-[#D6D3D1]">PHP</dt><dd class="font-bold num">{{ $systemInfo['php_version'] }}</dd></div>
            <div><dt class="text-[#475569] dark:text-[#D6D3D1]">Laravel</dt><dd class="font-bold num">{{ $systemInfo['laravel_version'] }}</dd></div>
            <div><dt class="text-[#475569] dark:text-[#D6D3D1]">Sistem Operasi</dt><dd class="font-bold">{{ $systemInfo['os'] }}</dd></div>
        </dl>
    </div>
</div>
@endsection