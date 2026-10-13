{{--
  Tombol standar. `as` = 'a' untuk link (pakai href).
  <x-btn variant="brand" icon="plus">Tambah</x-btn>
  <x-btn as="a" :href="route(...)" variant="ghost">Kembali</x-btn>
--}}
@props(['as' => 'button', 'variant' => 'brand', 'icon' => null, 'type' => 'button'])

@php
    $base = 'inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300 disabled:opacity-70 disabled:cursor-not-allowed';
    $variants = [
        'brand' => 'bg-orange-500 hover:bg-orange-600 text-white shadow-sm',
        'ghost' => 'bg-[#E2E8F0] dark:bg-[#2e323b] text-[#64748B] dark:text-[#cbd5e1] hover:bg-[#CBD5E1] dark:hover:bg-[#404854]',
        'outline' => 'bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#404854] text-[#1E293B] dark:text-[#f1f5f9] hover:border-orange-300',
        'danger' => 'bg-red-500 hover:bg-red-600 text-white shadow-sm',
    ];
    $class = $base . ' ' . ($variants[$variant] ?? $variants['brand']);
@endphp

@if($as === 'a')
    <a {{ $attributes->merge(['class' => $class]) }}>
        @if($icon)<x-icon :name="$icon" class="w-4 h-4" />@endif{{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>
        @if($icon)<x-icon :name="$icon" class="w-4 h-4" />@endif{{ $slot }}
    </button>
@endif