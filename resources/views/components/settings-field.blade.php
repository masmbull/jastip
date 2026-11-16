{{--
  Field standar halaman Pengaturan: label dengan ikon + input (slot) +
  hint opsional. Dipakai supaya typography & spacing seragam di semua card.
--}}
@props(['for' => null, 'icon' => null, 'label' => null, 'hint' => null])

<div>
    <label @if($for) for="{{ $for }}" @endif class="mb-1.5 flex items-center gap-1.5 text-sm font-bold text-[#334155] dark:text-[#CBD5E1]">
        @if($icon)
            <x-icon name="{{ $icon }}" class="h-4 w-4 shrink-0 text-orange-500 dark:text-orange-400" />
        @endif
        <span>{{ $label }}</span>
    </label>
    {{ $slot }}
    @if($hint)
        <p class="mt-1.5 flex items-start gap-1 text-xs text-[#64748B] dark:text-[#A9B4C4]">
            <x-icon name="info" class="h-3.5 w-3.5 mt-0.5 shrink-0 opacity-60" />
            <span>{{ $hint }}</span>
        </p>
    @endif
</div>
