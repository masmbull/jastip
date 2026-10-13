{{--
  Badge status/dokumen. Varian warna tetap (bukan arbitrary) supaya kontras aman
  di light & dark.
  <x-badge tone="success">LUNAS</x-badge>
--}}
@props(['tone' => 'neutral'])

@php
    $tones = [
        'neutral' => 'bg-[#F1F5F9] text-[#475569] dark:bg-[#2e323b] dark:text-[#cbd5e1]',
        'success' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
        'warning' => 'bg-amber-100 text-amber-800 dark:bg-amber-500/15 dark:text-amber-300',
        'danger' => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
        'info' => 'bg-sky-100 text-sky-800 dark:bg-sky-500/15 dark:text-sky-300',
        'brand' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full ' . ($tones[$tone] ?? $tones['neutral'])]) }}>
    {{ $slot }}
</span>