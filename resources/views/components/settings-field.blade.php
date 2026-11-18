{{--
  Field standar halaman Pengaturan: label + ikon brand + input (slot) + hint.
--}}
@props(['for' => null, 'icon' => null, 'label' => null, 'hint' => null])

<div>
    <label @if($for) for="{{ $for }}" @endif class="mb-1.5 flex items-center gap-1.5 text-sm font-bold text-[#334155] dark:text-[#D6D3D1]">
        @if($icon)
            <x-icon name="{{ $icon }}" class="h-4 w-4 shrink-0 text-[#141414] dark:text-[#FFC800]" stroke="2" />
        @endif
        <span>{{ $label }}</span>
    </label>
    {{ $slot }}
    @if($hint)
        <p class="mt-1.5 flex items-start gap-1 text-xs text-[#475569] dark:text-[#A8A29E]">
            <x-icon name="info" class="h-3.5 w-3.5 mt-0.5 shrink-0 opacity-70" stroke="2" />
            <span>{{ $hint }}</span>
        </p>
    @endif
</div>
