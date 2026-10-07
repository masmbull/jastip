@extends('admin.layouts.app')

@section('title', 'Service Monitoring')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 p-4 md:p-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                    Service Monitoring
                </h1>
                <p class="text-slate-600 dark:text-slate-300">
                    Monitor all services, APIs, and system status
                </p>
            </div>
            <a href="{{ route('admin.services.checkAll') }}" 
               class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition">
                🔄 Check All Services
            </a>
        </div>

        @if ($message = Session::get('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg">
                {{ $message }}
            </div>
        @endif

        <!-- System Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <!-- Database -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-4 border-l-4 @if($systemInfo['database']['status'] === 'online') border-green-500 @else border-red-500 @endif">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-slate-900 dark:text-white">Database</h3>
                    <span class="inline-flex h-3 w-3 rounded-full @if($systemInfo['database']['status'] === 'online') bg-green-500 @else bg-red-500 @endif"></span>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $systemInfo['database']['message'] }}</p>
            </div>

            <!-- Cache -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-4 border-l-4 @if($systemInfo['cache']['status'] === 'online') border-green-500 @else border-red-500 @endif">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-slate-900 dark:text-white">Cache</h3>
                    <span class="inline-flex h-3 w-3 rounded-full @if($systemInfo['cache']['status'] === 'online') bg-green-500 @else bg-red-500 @endif"></span>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $systemInfo['cache']['message'] }}</p>
            </div>

            <!-- Storage -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-4 border-l-4 @if($systemInfo['storage']['status'] === 'online') border-green-500 @else border-red-500 @endif">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-slate-900 dark:text-white">Storage</h3>
                    <span class="inline-flex h-3 w-3 rounded-full @if($systemInfo['storage']['status'] === 'online') bg-green-500 @else bg-red-500 @endif"></span>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $systemInfo['storage']['message'] }}</p>
            </div>
        </div>

        <!-- Disk & Memory Usage -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Disk Usage -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">💾 Disk Usage</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Total:</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $systemInfo['disk_usage']['total'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Used:</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $systemInfo['disk_usage']['used'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Free:</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $systemInfo['disk_usage']['free'] }}</span>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full" style="width: {{ $systemInfo['disk_usage']['percent'] }}%"></div>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-2">
                        {{ $systemInfo['disk_usage']['percent'] }}% Used
                    </p>
                </div>
            </div>

            <!-- Memory Usage -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">🧠 Memory Usage</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Current:</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $systemInfo['memory_usage']['current'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Peak:</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $systemInfo['memory_usage']['peak'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Limit:</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $systemInfo['memory_usage']['limit'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services List -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">📡 Services</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900 dark:text-white">Service</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900 dark:text-white">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900 dark:text-white">Response Time</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900 dark:text-white">Last Checked</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900 dark:text-white">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse ($services as $service)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900 dark:text-white">{{ $service->name }}</div>
                                    @if ($service->description)
                                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $service->description }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-2.5 w-2.5 rounded-full
                                            @if ($service->status === 'online') bg-green-500
                                            @elseif ($service->status === 'degraded') bg-yellow-500
                                            @else bg-red-500 @endif">
                                        </span>
                                        <span class="capitalize font-medium 
                                            @if ($service->status === 'online') text-green-700 dark:text-green-400
                                            @elseif ($service->status === 'degraded') text-yellow-700 dark:text-yellow-400
                                            @else text-red-700 dark:text-red-400 @endif">
                                            {{ $service->status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                    @if ($service->response_time)
                                        <span class="font-mono">{{ number_format($service->response_time, 2) }}ms</span>
                                    @else
                                        <span class="text-slate-500">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                    @if ($service->last_checked)
                                        <span title="{{ $service->last_checked }}">
                                            {{ $service->last_checked->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-slate-500">Never</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('admin.services.check', $service) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded hover:bg-blue-200 dark:hover:bg-blue-900/50 transition text-sm font-medium">
                                            Check
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @if ($service->error_message)
                                <tr class="bg-red-50 dark:bg-red-900/10">
                                    <td colspan="5" class="px-6 py-3 text-sm text-red-700 dark:text-red-400">
                                        <strong>Error:</strong> {{ $service->error_message }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                                    No services configured yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    @media (prefers-color-scheme: dark) {
        :root {
            color-scheme: dark;
        }
    }
</style>
@endsection
