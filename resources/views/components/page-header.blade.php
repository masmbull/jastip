{{--
  Judul halaman brutalist. Marker kuning di judul sebagai satu aksen yang disengaja.
--}}
@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4']) }}>
    <div class="min-w-0">
        <h1 class="text-2xl sm:text-[28px] font-black tracking-tight text-[#141414] dark:text-[#FAF6EF] truncate"><span class="nb-marker">{{ $title }}</span></h1>
        @if($subtitle)
            <p class="text-sm text-[#475569] dark:text-[#D6D3D1] mt-2 max-w-[60ch]">{{ $subtitle }}</p>
        @endif
    </div>
    @if(! $slot->isEmpty())
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 shrink-0">
            {{ $slot }}
        </div>
    @endif
</div>