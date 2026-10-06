@extends('layouts.admin')
@section('title', 'Status Sistem & API')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#1E293B]">Status Sistem & API Services</h1>
        <p class="text-[#64748B] mt-2">Monitor kesehatan aplikasi dan koneksi ke layanan eksternal</p>
    </div>

    <!-- Services Status Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        @foreach ($services as $service)
            <div class="bg-white rounded-xl shadow p-6 border-l-4 {{ $service['status'] === 'online' ? 'border-emerald-500' : 'border-red-500' }}">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-2xl">{{ $service['icon'] }}</p>
                        <h3 class="text-lg font-semibold text-[#1E293B] mt-2">{{ $service['name'] }}</h3>
                        @if (isset($service['message']))
                            <p class="text-xs text-[#64748B] mt-1">{{ $service['message'] }}</p>
                        @endif
                        @if (isset($service['last_update']))
                            <p class="text-xs text-[#94A3B8] mt-1">Terakhir: {{ $service['last_update'] ?? 'Belum' }}</p>
                        @endif
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $service['status'] === 'online' ? 'bg-emerald-100 text-emerald-700' : ($service['status'] === 'offline' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                        <span class="w-2 h-2 rounded-full mr-2 {{ $service['status'] === 'online' ? 'bg-emerald-500' : ($service['status'] === 'offline' ? 'bg-red-500' : 'bg-amber-500') }}"></span>
                        {{ ucfirst($service['status']) }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- System Information -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="bg-[#F1F5F9] border-b border-[#E2E8F0] px-6 py-4">
            <h2 class="text-lg font-bold text-[#1E293B]">Informasi Sistem</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            <div>
                <p class="text-sm text-[#64748B] font-medium">PHP Version</p>
                <p class="text-lg font-semibold text-[#1E293B] mt-1">{{ $systemInfo['php_version'] }}</p>
            </div>
            <div>
                <p class="text-sm text-[#64748B] font-medium">Laravel Version</p>
                <p class="text-lg font-semibold text-[#1E293B] mt-1">{{ $systemInfo['laravel_version'] }}</p>
            </div>
            <div>
                <p class="text-sm text-[#64748B] font-medium">Operating System</p>
                <p class="text-lg font-semibold text-[#1E293B] mt-1">{{ $systemInfo['os'] }}</p>
            </div>
            <div>
                <p class="text-sm text-[#64748B] font-medium">Memory Usage</p>
                <p class="text-lg font-semibold text-[#1E293B] mt-1">{{ $systemInfo['memory_usage'] }}</p>
            </div>
            <div>
                <p class="text-sm text-[#64748B] font-medium">Disk Free</p>
                <p class="text-lg font-semibold text-[#1E293B] mt-1">{{ $systemInfo['disk_free'] }}</p>
            </div>
            <div>
                <p class="text-sm text-[#64748B] font-medium">Disk Total</p>
                <p class="text-lg font-semibold text-[#1E293B] mt-1">{{ $systemInfo['disk_total'] }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
