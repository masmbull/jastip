@props(['paginator'])

@php
    $query = request()->except('page');
    $p = $paginator->appends($query);
    $current = $p->currentPage();
    $last = $p->lastPage();
@endphp

@if($p->hasPages())
<nav role="navigation" class="flex justify-center mt-12" aria-label="Navigasi halaman">
    <ul class="inline-flex items-center gap-1 bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] p-1.5">
        {{-- Previous --}}
        @if ($p->onFirstPage())
            <li><span aria-disabled="true" class="inline-flex items-center justify-center w-11 h-11 text-sm text-[#94A3B8] dark:text-[#64748B] rounded-lg">‹</span></li>
        @else
            <li><a href="{{ $p->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya"
                    class="inline-flex items-center justify-center w-11 h-11 text-sm text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-500/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                ‹
            </a></li>
        @endif

        {{-- Page numbers --}}
        @foreach ($p->getUrlRange(1, $last) as $page => $url)
            @php($isCurrent = $page == $current)
            @php($isBoundary = $page == 1 || $page == $last)
            @php($isNear = abs($page - $current) < 2)
            @if ($isCurrent)
                <li><span aria-current="page" class="inline-flex items-center justify-center min-w-11 h-11 px-3 text-sm font-medium text-white bg-orange-600 rounded-lg num">{{ $page }}</span></li>
            @elseif ($isBoundary || $isNear)
                <li><a href="{{ $url }}" aria-label="Halaman {{ $page }}"
                        class="inline-flex items-center justify-center min-w-11 h-11 px-3 text-sm text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-500/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 num">{{ $page }}</a></li>
            @else
                <li><span aria-hidden="true" class="inline-flex items-center justify-center min-w-11 h-11 px-3 text-sm text-[#94A3B8] dark:text-[#64748B]">...</span></li>
            @endif
        @endforeach

        {{-- Next --}}
        @if ($p->onLastPage())
            <li><span aria-disabled="true" class="inline-flex items-center justify-center w-11 h-11 text-sm text-[#94A3B8] dark:text-[#64748B] rounded-lg">›</span></li>
        @else
            <li><a href="{{ $p->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya"
                    class="inline-flex items-center justify-center w-11 h-11 text-sm text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-500/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                ›
            </a></li>
        @endif
    </ul>
</nav>
@endif