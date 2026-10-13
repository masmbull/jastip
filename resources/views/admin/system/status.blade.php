@extends('layouts.admin')
@section('title', 'Status Sistem & API')

@section('content')
<div class="space-y-6">

    <x-page-header title="Status Sistem & API Services" subtitle="Monitor kesehatan aplikasi dan koneksi ke layanan eksternal" class="animate-fade-up" />

    {{-- Services Status Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($services as $service)
            <x-card class="border-l-4 {{ $service['status'] === 'online' ? 'border-l-emerald-500' : ($service['status'] === 'offline' ? 'border-l-red-500' : 'border-l-amber-500') }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-2xl" aria-hidden="true">{{ $service['icon'] }}</p>
                        <h3 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mt-2">{{ $service['name'] }}</h3>
                        @if (isset($service['message']))
                            <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">{{ $service['message'] }}</p>
                        @endif
                        @if (isset($service['last_update']))
                            <p class="text-xs text-[#94A3B8] dark:text-[#64748B] mt-1">Terakhir: {{ $service['last_update'] ?? 'Belum' }}</p>
                        @endif
                    </div>
                    <x-badge :tone="$service['status'] === 'online' ? 'success' : ($service['status'] === 'offline' ? 'danger' : 'warning')">
                        <span class="w-2 h-2 rounded-full {{ $service['status'] === 'online' ? 'bg-emerald-500' : ($service['status'] === 'offline' ? 'bg-red-500' : 'bg-amber-500') }}" aria-hidden="true"></span>
                        {{ ucfirst($service['status']) }}
                    </x-badge>
                </div>
            </x-card>
        @endforeach
    </div>

    {{-- System Information --}}
    <x-card padded="false">
        <div class="bg-[#F1F5F9] dark:bg-[#2e323b] border-b border-[#E2E8F0] dark:border-[#404854] px-6 py-4">
            <h2 class="text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9]">Informasi Sistem</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            <div>
                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] font-medium">PHP Version</p>
                <p class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mt-1 num">{{ $systemInfo['php_version'] }}</p>
            </div>
            <div>
                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] font-medium">Laravel Version</p>
                <p class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mt-1 num">{{ $systemInfo['laravel_version'] }}</p>
            </div>
            <div>
                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] font-medium">Operating System</p>
                <p class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mt-1">{{ $systemInfo['os'] }}</p>
            </div>
            <div>
                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] font-medium">Memory Usage</p>
                <p class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mt-1 num">{{ $systemInfo['memory_usage'] }}</p>
            </div>
            <div>
                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] font-medium">Disk Free</p>
                <p class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mt-1 num">{{ $systemInfo['disk_free'] }}</p>
            </div>
            <div>
                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] font-medium">Disk Total</p>
                <p class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mt-1 num">{{ $systemInfo['disk_total'] }}</p>
            </div>
        </div>
    </x-card>
</div>
@endsection