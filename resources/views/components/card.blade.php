{{--
  Kartu putih standar (surface + border + radius). Varian padding via `padded`.
  <x-card> ... </x-card>
  <x-card padded="false"> tabel </x-card>
--}}
@props(['padded' => true])

<div {{ $attributes->merge([
        'class' => 'bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] transition-colors'
            . ($padded ? ' p-5 sm:p-6' : ' overflow-hidden'),
    ]) }}>
    {{ $slot }}
</div>