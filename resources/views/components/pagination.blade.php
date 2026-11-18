@props(['paginator'])

@php
    $query = request()->except('page');
    $p = $paginator->appends($query);
    $current = $p->currentPage();
    $last = $p->lastPage();
@endphp

@if($p->hasPages())
<nav role="navigation" class="mt-8 flex justify-center" aria-label="Navigasi halaman">
    <ul class="inline-flex items-center gap-1 rounded-[10px] border-2 border-[#141414] bg-white p-1.5 dark:border-[#FAF6EF] dark:bg-[#1E1E1E]">
        @if ($p->onFirstPage())
            <li><span aria-disabled="true" class="inline-flex h-11 w-11 items-center justify-center rounded-[8px] text-sm text-[#94A3B8]">‹</span></li>
        @else
            <li><a href="{{ $p->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-[8px] text-sm font-bold text-[#475569] transition-colors hover:bg-[#FFF7D6] hover:text-[#141414] focus:outline-none focus:ring-2 focus:ring-[#FFC800]">
                ‹
            </a></li>
        @endif

        @foreach ($p->getUrlRange(1, $last) as $page => $url)
            @php($isCurrent = $page == $current)
            @php($isBoundary = $page == 1 || $page == $last)
            @php($isNear = abs($page - $current) < 2)
            @if ($isCurrent)
                <li><span aria-current="page" class="inline-flex min-w-11 h-11 items-center justify-center rounded-[8px] border-2 border-[#141414] bg-[#FFC800] px-3 text-sm font-black text-[#141414] num">{{ $page }}</span></li>
            @elseif ($isBoundary || $isNear)
                <li><a href="{{ $url }}" aria-label="Halaman {{ $page }}"
                        class="inline-flex min-w-11 h-11 items-center justify-center rounded-[8px] px-3 text-sm font-bold text-[#475569] transition-colors hover:bg-[#FFF7D6] hover:text-[#141414] focus:outline-none focus:ring-2 focus:ring-[#FFC800] num">{{ $page }}</a></li>
            @else
                <li><span aria-hidden="true" class="inline-flex min-w-11 h-11 items-center justify-center px-3 text-sm text-[#94A3B8]">...</span></li>
            @endif
        @endforeach

        @if ($p->onLastPage())
            <li><span aria-disabled="true" class="inline-flex h-11 w-11 items-center justify-center rounded-[8px] text-sm text-[#94A3B8]">›</span></li>
        @else
            <li><a href="{{ $p->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-[8px] text-sm font-bold text-[#475569] transition-colors hover:bg-[#FFF7D6] hover:text-[#141414] focus:outline-none focus:ring-2 focus:ring-[#FFC800]">
                ›
            </a></li>
        @endif
    </ul>
</nav>
@endif
