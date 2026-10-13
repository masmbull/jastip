{{--
  Empty state brutalist: kotak tegas, ikon relevan, satu aksi jelas.
--}}
@props(['icon' => 'info', 'title' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-12 px-4']) }}>
    <div class="w-16 h-16 bg-[#FFC800] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[12px] flex items-center justify-center mb-4" style="box-shadow: 3px 3px 0 #141414">
        <x-icon :name="$icon" class="w-8 h-8 text-[#141414]" />
    </div>
    @if($title)
        <p class="text-[#141414] dark:text-[#FAF6EF] font-extrabold">{{ $title }}</p>
    @endif
    @if(! $slot->isEmpty())
        <div class="mt-2 text-sm text-[#475569] dark:text-[#D6D3D1] max-w-sm">{{ $slot }}</div>
    @endif
</div>