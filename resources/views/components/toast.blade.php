@php
    $success = session('success');
    $error = session('error');
    $info = session('info');
    $hasMessage = $success || $error || $info;
@endphp

@if($hasMessage)
<div x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 4000)"
     x-transition:enter="transition ease-out duration-300 transform"
     x-transition:enter-start="translate-y-100 opacity-0"
     x-transition:enter-end="translate-y-0 opacity-100"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-end="opacity-0"
     x-cloak
     class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] max-w-md w-full mx-4">

    @if($success)
        <div class="bg-green-500 text-white px-4 py-3 rounded-xl shadow-lg flex items-start">
            <svg class="w-5 h-5 flex-shrink-0 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.93 11.93 0 0110 2.94a11.93 11.93 0 01-7.843 3.057M12 12v1m0 0v1m0-1l-2 2m2-2l2 2"></path>
            </svg>
            <span class="text-sm">{{ $success }}</span>
        </div>
    @elseif($error)
        <div class="bg-red-500 text-white px-4 py-3 rounded-xl shadow-lg flex items-start">
            <svg class="w-5 h-5 flex-shrink-0 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm">{{ $error }}</span>
        </div>
    @elseif($info)
        <div class="bg-blue-500 text-white px-4 py-3 rounded-xl shadow-lg flex items-start">
            <svg class="w-5 h-5 flex-shrink-0 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1V10h-2v6H9m3-8V5a3 3 0 016 0v1h1a1 1 0 110 2h-1v6h1a1 1 0 110 2h-1v4a1 1 0 11-2 0v-4H9v4a1 1 0 11-2 0v-4H5a1 1 0 110-2h1V7a1 1 0 012 0v1h2z"></path>
            </svg>
            <span class="text-sm">{{ $info }}</span>
        </div>
    @endif
</div>
</div>
@endif