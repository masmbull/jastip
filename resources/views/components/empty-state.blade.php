{{--
  Empty state seragam.
  <x-empty-state icon="bag" title="Belum ada produk.">...</x-empty-state>
--}}
@props(['icon' => 'info', 'title' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-12 px-4']) }}>
    <div class="w-16 h-16 bg-[#F1F5F9] dark:bg-[#2e323b] rounded-full flex items-center justify-center mb-4">
        <x-icon :name="$icon" class="w-8 h-8 text-[#94A3B8]" />
    </div>
    @if($title)
        <p class="text-[#64748B] dark:text-[#cbd5e1] font-medium">{{ $title }}</p>
    @endif
    @if(! $slot->isEmpty())
        <div class="mt-2 text-sm text-[#64748B] dark:text-[#cbd5e1] max-w-sm">{{ $slot }}</div>
    @endif
</div>