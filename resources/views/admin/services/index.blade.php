@extends('layouts.admin')

@section('title', 'Service Monitoring | Admin')

@section('content')
<div class="space-y-6">

    <x-page-header title="Service Monitoring" subtitle="Monitor status semua services dan sistem" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.services.checkAll')" icon="arrow-path">Check All</x-btn>
    </x-page-header>

    @if ($message = Session::get('success'))
        <div class="bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/30 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg text-sm">
            {{ $message }}
        </div>
    @endif

    {{-- System Info Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <x-card>
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] inline-flex items-center gap-2"><x-icon name="table" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Database</h3>
                <span class="inline-flex h-2.5 w-2.5 rounded-full @if($systemInfo['database']['status'] === 'online') bg-green-500 @else bg-red-500 @endif" aria-hidden="true"></span>
            </div>
            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">{{ $systemInfo['database']['message'] }}</p>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] inline-flex items-center gap-2"><x-icon name="bolt" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Cache</h3>
                <span class="inline-flex h-2.5 w-2.5 rounded-full @if($systemInfo['cache']['status'] === 'online') bg-green-500 @else bg-red-500 @endif" aria-hidden="true"></span>
            </div>
            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">{{ $systemInfo['cache']['message'] }}</p>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] inline-flex items-center gap-2"><x-icon name="cube" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Storage</h3>
                <span class="inline-flex h-2.5 w-2.5 rounded-full @if($systemInfo['storage']['status'] === 'online') bg-green-500 @else bg-red-500 @endif" aria-hidden="true"></span>
            </div>
            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">{{ $systemInfo['storage']['message'] }}</p>
        </x-card>
    </div>

    {{-- Disk & Memory Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-card>
            <h3 class="font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4 inline-flex items-center gap-2"><x-icon name="table" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Disk Usage</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between text-[#64748B] dark:text-[#cbd5e1]">
                    <span>Total:</span>
                    <span class="text-[#1E293B] dark:text-[#f1f5f9] font-semibold num">{{ $systemInfo['disk_usage']['total'] }}</span>
                </div>
                <div class="flex justify-between text-[#64748B] dark:text-[#cbd5e1]">
                    <span>Used:</span>
                    <span class="text-[#1E293B] dark:text-[#f1f5f9] font-semibold num">{{ $systemInfo['disk_usage']['used'] }}</span>
                </div>
                <div class="w-full bg-[#E2E8F0] dark:bg-[#404854] rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $systemInfo['disk_usage']['percent'] }}%"></div>
                </div>
                <p class="text-[#64748B] dark:text-[#cbd5e1] num">{{ $systemInfo['disk_usage']['percent'] }}% Used</p>
            </div>
        </x-card>

        <x-card>
            <h3 class="font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4 inline-flex items-center gap-2"><x-icon name="bolt" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Memory Usage</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-[#64748B] dark:text-[#cbd5e1]">
                    <span>Current:</span>
                    <span class="text-[#1E293B] dark:text-[#f1f5f9] font-semibold num">{{ $systemInfo['memory_usage']['current'] }}</span>
                </div>
                <div class="flex justify-between text-[#64748B] dark:text-[#cbd5e1]">
                    <span>Peak:</span>
                    <span class="text-[#1E293B] dark:text-[#f1f5f9] font-semibold num">{{ $systemInfo['memory_usage']['peak'] }}</span>
                </div>
                <div class="flex justify-between text-[#64748B] dark:text-[#cbd5e1]">
                    <span>Limit:</span>
                    <span class="text-[#1E293B] dark:text-[#f1f5f9] font-semibold num">{{ $systemInfo['memory_usage']['limit'] }}</span>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Services Table --}}
    <x-card padded="false">
        <div class="px-6 py-4 border-b border-[#E2E8F0] dark:border-[#404854]">
            <h2 class="font-bold text-lg text-[#1E293B] dark:text-[#f1f5f9] inline-flex items-center gap-2"><x-icon name="server" class="w-5 h-5 text-orange-500 dark:text-orange-400" /> Services</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-sm">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 md:px-6 py-3">Service</th>
                        <th class="px-4 md:px-6 py-3">Status</th>
                        <th class="px-4 md:px-6 py-3 text-right">Response</th>
                        <th class="px-4 md:px-6 py-3">Last Check</th>
                        <th class="px-4 md:px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                    @forelse ($services as $service)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-4 md:px-6 py-4">
                                <div class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">{{ $service->name }}</div>
                                @if ($service->description)
                                    <p class="text-xs text-[#94A3B8] dark:text-[#64748B]">{{ $service->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                @if ($service->status === 'online')
                                    <x-badge tone="success">Online</x-badge>
                                @elseif ($service->status === 'degraded')
                                    <x-badge tone="warning">Degraded</x-badge>
                                @else
                                    <x-badge tone="danger">{{ $service->status }}</x-badge>
                                @endif
                            </td>
                            <td class="px-4 md:px-6 py-4 text-[#64748B] dark:text-[#cbd5e1] text-xs text-right num">
                                @if ($service->response_time)
                                    <span class="font-mono">{{ number_format($service->response_time, 2) }}ms</span>
                                @else
                                    <span>-</span>
                                @endif
                            </td>
                            <td class="px-4 md:px-6 py-4 text-xs text-[#64748B] dark:text-[#cbd5e1]">
                                @if ($service->last_checked)
                                    <span title="{{ $service->last_checked }}">{{ $service->last_checked->diffForHumans() }}</span>
                                @else
                                    <span>Never</span>
                                @endif
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                <form action="{{ route('admin.services.check', $service) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-blue-100 dark:bg-blue-500/15 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-500/25 transition text-xs font-medium">
                                        Check
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @if ($service->error_message)
                            <tr class="bg-red-50 dark:bg-red-500/10 border-b border-[#E2E8F0] dark:border-[#404854]">
                                <td colspan="5" class="px-4 md:px-6 py-3 text-xs text-red-700 dark:text-red-400">
                                    <strong>Error:</strong> {{ $service->error_message }}
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state icon="server" title="Belum ada service terdaftar" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

</div>
@endsection