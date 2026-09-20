@props(['type' => null, 'message' => null])

@php
    $flashes = [];
    if (session('success'))  $flashes[] = ['type' => 'success', 'message' => session('success')];
    if (session('error'))    $flashes[] = ['type' => 'error',   'message' => session('error')];
    if (session('info'))     $flashes[] = ['type' => 'info',    'message' => session('info')];
@endphp

@if(! empty($flashes))
<div id="jd-toasts" class="fixed bottom-6 left-6 z-[200] flex flex-col gap-2 w-full max-w-xs">
    @foreach($flashes as $index => $flash)
        @php($delay = $index * 250)
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 4500 + {{ $delay }})"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-end="opacity-0"
             x-cloak
             class="flex items-start gap-2.5 px-4 py-3 rounded-xl shadow-lg text-white text-sm
                    {{ ['success'=>'bg-green-600','error'=>'bg-red-600','info'=>'bg-blue-600'][$flash['type']] ?? 'bg-gray-700' }}">
            <span class="flex-1 break-words">{{ $flash['message'] }}</span>
            <button type="button" @click="show=false" class="flex-shrink-0 text-white/80 hover:text-white text-xl leading-none">×</button>
        </div>
    @endforeach
</div>
@endif