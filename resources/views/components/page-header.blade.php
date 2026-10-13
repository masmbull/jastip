{{--
  Judul halaman admin yang seragam.
  <x-page-header title="Produk" subtitle="Kelola produk">
      <a ...>Aksi</a>
  </x-page-header>
--}}
@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4']) }}>
    <div class="min-w-0">
        <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9] truncate">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    @if(! $slot->isEmpty())
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 shrink-0">
            {{ $slot }}
        </div>
    @endif
</div>