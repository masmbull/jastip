{{--
  Tombol brutalist. Border 2px tinta + hard shadow sebagai motif identitas.
  Kontras: teks putih di #0E7490 (5.36:1, lolos AA), bukan #0891B2 (3.68:1).
--}}
@props(['as' => 'button', 'variant' => 'brand', 'icon' => null, 'type' => 'button'])

@php
    $base = 'nb-btn px-4 py-2 text-sm';
    $variants = [
        'brand' => 'bg-[#0E7490] hover:bg-[#155E75] text-white',
        'accent' => 'bg-[#FFC800] hover:bg-[#F5B800] text-[#141414]',
        'ghost' => 'bg-white dark:bg-[#1E1E1E] text-[#141414] dark:text-[#FAF6EF]',
        'outline' => 'bg-white dark:bg-[#1E1E1E] text-[#141414] dark:text-[#FAF6EF]',
        'danger' => 'bg-[#DC2626] hover:bg-[#B91C1C] text-white',
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