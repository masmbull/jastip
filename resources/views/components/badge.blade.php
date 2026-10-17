{{--
  Badge status brutalist. Kotak tegas + border tinta, bukan pil ber-glow.
  Hanya untuk status nyata (LUNAS, BELUM BAYAR, dsb). Kontras semua lolos AA.
--}}
@props(['tone' => 'neutral'])

@php
    $tones = [
        'neutral' => 'bg-white text-[#334155] dark:bg-[#262626] dark:text-[#E7E5E4]',
        'success' => 'bg-[#DCFCE7] text-[#166534] dark:bg-[#14532D] dark:text-[#BBF7D0]',
        'warning' => 'bg-[#FEF3C7] text-[#92400E] dark:bg-[#78350F] dark:text-[#FDE68A]',
        'danger' => 'bg-[#FEE2E2] text-[#991B1B] dark:bg-[#7F1D1D] dark:text-[#FECACA]',
        'info' => 'bg-[#E0E7FF] text-[#3730A3] dark:bg-[#312E81] dark:text-[#C7D2FE]',
        'brand' => 'bg-[#FFC800] text-[#141414] dark:bg-[#FFC800] dark:text-[#141414]',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'nb-badge ' . ($tones[$tone] ?? $tones['neutral'])]) }}>
    {{ $slot }}
</span>