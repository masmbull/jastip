{{--
  Judul blok di panel admin. Ikon di kotak aksen kuning (bukan oranye
  generik) supaya aksen brand tetap satu: CTA/uang/fokus.
--}}
@props(['icon' => 'info', 'title' => ''])

<h2 {{ $attributes->merge(['class' => 'mb-4 flex items-center gap-2.5 font-display text-lg font-extrabold tracking-tight text-[#141414] dark:text-[#FAF6EF]']) }}>
    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[8px] border-2 border-[#141414] bg-[#FFC800] text-[#141414] shadow-[2px_2px_0_#141414] dark:border-[#FAF6EF] dark:shadow-[2px_2px_0_#000]">
        <x-icon :name="$icon" class="h-5 w-5" stroke="2" />
    </span>
    <span>{{ $title }}</span>
</h2>
