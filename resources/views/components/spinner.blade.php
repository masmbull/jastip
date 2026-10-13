{{--
  <x-spinner />                  inline, currentColor
  <x-spinner class="w-5 h-5" />  sized by caller
  <x-spinner label="Memuat..." /> adds an accessible + visible label
--}}
@props(['label' => null, 'variant' => 'current'])

@php($tone = ['current' => '', 'brand' => 'text-orange-500 dark:text-orange-400'][$variant] ?? '')

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <svg class="animate-spin h-4 w-4 {{ $tone }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.37 0 0 5.37 0 12h4z"></path>
    </svg>
    @if($label)
        <span>{{ $label }}</span>
    @else
        <span class="sr-only">Memuat…</span>
    @endif
</span>