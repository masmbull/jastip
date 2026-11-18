{{--
  Kartu statistik brutalist (label + nilai + ikon + link opsional).
  <x-stat-card label="Total Produk" :value="$n" icon="cube" :href="route(...)" />

  Isi kotak ikon sengaja putih supaya kontrasnya tetap terbaca di atas kartu
  yang ikut siklus warna pelangi.
--}}
@props(['label', 'value', 'icon' => null, 'href' => null, 'tone' => null])

@php($tones = ['sun', 'sand', 'coral', 'bubble', 'grape', 'sky', 'mint'])

<div {{ $attributes->merge(['class' => 'nb-card flex h-full flex-col p-5' . (in_array($tone, $tones, true) ? ' nb-tint-' . $tone : '')]) }}>
    <div class="mb-3 flex min-h-10 items-center justify-between">
        @if($icon)
            <div class="w-10 h-10 bg-white border-2 border-[#141414] rounded-[10px] flex items-center justify-center">
                <x-icon :name="$icon" class="w-5 h-5 text-[#141414]" />
            </div>
        @endif
        @if($href)
            <a href="{{ $href }}" class="text-xs font-black text-[#334155] underline underline-offset-2 hover:text-[#141414]">Detail</a>
        @endif
    </div>
    <div class="mt-auto">
        <p class="font-display text-3xl font-extrabold text-[#141414] dark:text-[#FAF6EF] num">{{ $value }}</p>
        <p class="min-h-10 text-sm font-bold leading-5 text-[#334155] dark:text-[#D6D3D1]">{{ $label }}</p>
    </div>
</div>
