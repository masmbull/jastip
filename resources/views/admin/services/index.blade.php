@extends('layouts.admin')

@section('title', 'Service Monitoring | Admin')

@section('content')
<div class="p-4 md:p-6 lg:p-8 space-y-4 md:space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">📡 Service Monitoring</h1>
            <p class="text-xs md:text-sm text-[#94A3B8] dark:text-[#cbd5e1] mt-1">Monitor status semua services dan sistem</p>
        </div>
        <a href="{{ route('admin.services.checkAll') }}"
           class="inline-flex items-center px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors text-sm md:text-base whitespace-nowrap">
            <svg class="w-4 md:w-5 h-4 md:h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            🔄 Check All
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg text-sm">
            {{ $message }}
        </div>
    @endif

    {{-- System Info Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
        <!-- Database -->
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm p-4 md:p-5 border border-[#E2E8F0] dark:border-[#404854]">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">🗄️ Database</h3>
                <span class="inline-flex h-2.5 w-2.5 rounded-full @if($systemInfo['database']['status'] === 'online') bg-green-500 @else bg-red-500 @endif"></span>
            </div>
            <p class="text-xs md:text-sm text-[#94A3B8] dark:text-[#cbd5e1]">{{ $systemInfo['database']['message'] }}</p>
        </div>

        <!-- Cache -->
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm p-4 md:p-5 border border-[#E2E8F0] dark:border-[#404854]">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">⚡ Cache</h3>
                <span class="inline-flex h-2.5 w-2.5 rounded-full @if($systemInfo['cache']['status'] === 'online') bg-green-500 @else bg-red-500 @endif"></span>
            </div>
            <p class="text-xs md:text-sm text-[#94A3B8] dark:text-[#cbd5e1]">{{ $systemInfo['cache']['message'] }}</p>
        </div>

        <!-- Storage -->
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm p-4 md:p-5 border border-[#E2E8F0] dark:border-[#404854]">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">📂 Storage</h3>
                <span class="inline-flex h-2.5 w-2.5 rounded-full @if($systemInfo['storage']['status'] === 'online') bg-green-500 @else bg-red-500 @endif"></span>
            </div>
            <p class="text-xs md:text-sm text-[#94A3B8] dark:text-[#cbd5e1]">{{ $systemInfo['storage']['message'] }}</p>
        </div>
    </div>

    {{-- Disk & Memory Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Disk Usage -->
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm p-4 md:p-5 border border-[#E2E8F0] dark:border-[#404854]">
            <h3 class="font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4">💾 Disk Usage</h3>
            <div class="space-y-3 text-xs md:text-sm">
                <div class="flex justify-between text-[#94A3B8] dark:text-[#cbd5e1]">
                    <span>Total:</span>
                    <span class="text-[#1E293B] dark:text-[#f1f5f9] font-semibold">{{ $systemInfo['disk_usage']['total'] }}</span>
                </div>
                <div class="flex justify-between text-[#94A3B8] dark:text-[#cbd5e1]">
                    <span>Used:</span>
                    <span class="text-[#1E293B] dark:text-[#f1f5f9] font-semibold">{{ $systemInfo['disk_usage']['used'] }}</span>
                </div>
                <div class="w-full bg-[#E2E8F0] dark:bg-[#404854] rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $systemInfo['disk_usage']['percent'] }}%"></div>
                </div>
                <p class="text-[#94A3B8] dark:text-[#cbd5e1]">{{ $systemInfo['disk_usage']['percent'] }}% Used</p>
            </div>
        </div>

        <!-- Memory Usage -->
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm p-4 md:p-5 border border-[#E2E8F0] dark:border-[#404854]">
            <h3 class="font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4">🧠 Memory Usage</h3>
            <div class="space-y-2 text-xs md:text-sm">
                <div class="flex justify-between text-[#94A3B8] dark:text-[#cbd5e1]">
                    <span>Current:</span>
                    <span class="text-[#1E293B] dark:text-[#f1f5f9] font-semibold">{{ $systemInfo['memory_usage']['current'] }}</span>
                </div>
                <div class="flex justify-between text-[#94A3B8] dark:text-[#cbd5e1]">
                    <span>Peak:</span>
                    <span class="text-[#1E293B] dark:text-[#f1f5f9] font-semibold">{{ $systemInfo['memory_usage']['peak'] }}</span>
                </div>
                <div class="flex justify-between text-[#94A3B8] dark:text-[#cbd5e1]">
                    <span>Limit:</span>
                    <span class="text-[#1E293B] dark:text-[#f1f5f9] font-semibold">{{ $systemInfo['memory_usage']['limit'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Services Table --}}
    <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] overflow-hidden">
        <div class="px-6 py-4 border-b border-[#E2E8F0] dark:border-[#404854]">
            <h2 class="font-bold text-lg text-[#1E293B] dark:text-[#f1f5f9]">📋 Services</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F8FAFC] dark:bg-[#1a1a1a] border-b border-[#E2E8F0] dark:border-[#404854]">
                    <tr>
                        <th class="px-4 md:px-6 py-3 text-left font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Service</th>
                        <th class="px-4 md:px-6 py-3 text-left font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Status</th>
                        <th class="px-4 md:px-6 py-3 text-left font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Response</th>
                        <th class="px-4 md:px-6 py-3 text-left font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Last Check</th>
                        <th class="px-4 md:px-6 py-3 text-left font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                    @forelse ($services as $service)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#1a1a1a] transition-colors">
                            <td class="px-4 md:px-6 py-4">
                                <div class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">{{ $service->name }}</div>
                                @if ($service->description)
                                    <p class="text-xs text-[#94A3B8] dark:text-[#cbd5e1]">{{ $service->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex h-2 w-2 rounded-full
                                        @if ($service->status === 'online') bg-green-500
                                        @elseif ($service->status === 'degraded') bg-yellow-500
                                        @else bg-red-500 @endif">
                                    </span>
                                    <span class="capitalize font-medium text-xs
                                        @if ($service->status === 'online') text-green-700 dark:text-green-400
                                        @elseif ($service->status === 'degraded') text-yellow-700 dark:text-yellow-400
                                        @else text-red-700 dark:text-red-400 @endif">
                                        {{ $service->status }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-4 text-[#94A3B8] dark:text-[#cbd5e1] text-xs">
                                @if ($service->response_time)
                                    <span class="font-mono">{{ number_format($service->response_time, 2) }}ms</span>
                                @else
                                    <span>—</span>
                                @endif
                            </td>
                            <td class="px-4 md:px-6 py-4 text-xs text-[#94A3B8] dark:text-[#cbd5e1]">
                                @if ($service->last_checked)
                                    <span title="{{ $service->last_checked }}">{{ $service->last_checked->diffForHumans() }}</span>
                                @else
                                    <span>Never</span>
                                @endif
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                <form action="{{ route('admin.services.check', $service) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded hover:bg-blue-200 dark:hover:bg-blue-900/50 transition text-xs font-medium">
                                        Check
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @if ($service->error_message)
                            <tr class="bg-red-50 dark:bg-red-900/10 border-b border-[#E2E8F0] dark:border-[#404854]">
                                <td colspan="5" class="px-4 md:px-6 py-3 text-xs text-red-700 dark:text-red-400">
                                    <strong>Error:</strong> {{ $service->error_message }}
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 md:px-6 py-8 text-center text-[#94A3B8] dark:text-[#cbd5e1]">
                                No services configured
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
