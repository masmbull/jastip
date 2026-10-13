{{--
  Kartu statistik ringkas (label + nilai + ikon + link opsional).
  <x-stat-card label="Total Produk" :value="$n" icon="cube" :href="route(...)" />
--}}
@props(['label', 'value', 'icon' => null, 'href' => null])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] p-5 transition-colors']) }}>
    <div class="flex items-center justify-between mb-3">
        @if($icon)
            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-500/20 rounded-lg flex items-center justify-center">
                <x-icon :name="$icon" class="w-5 h-5 text-orange-600 dark:text-orange-400" />
            </div>
        @endif
        @if($href)
            <a href="{{ $href }}" class="text-xs text-orange-600 dark:text-orange-400 font-medium hover:underline">Detail</a>
        @endif
    </div>
    <p class="text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9] tabular-nums">{{ $value }}</p>
    <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">{{ $label }}</p>
</div>