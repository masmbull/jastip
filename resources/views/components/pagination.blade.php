@props(['paginator'])

@php
    $query = request()->except('page');
    $p = $paginator->appends($query);
    $current = $p->currentPage();
    $last = $p->lastPage();
@endphp

@if($p->hasPages())
<nav role="navigation" class="flex justify-center mt-12" aria-label="Pagination">
    <ul class="inline-flex items-center gap-1 bg-white rounded-xl shadow-sm border border-[#E2E8F0] p-1.5">
        {{-- Previous --}}
        @if ($p->onFirstPage())
            <li><span class="px-3 py-2 text-sm text-[#94A3B8] rounded-lg">‹</span></li>
        @else
            <li><a href="{{ $p->previousPageUrl() }}"
                    class="px-3 py-2 text-sm text-[#64748B] hover:text-orange-600 hover:bg-orange-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                ‹
            </a></li>
        @endif

        {{-- Page numbers --}}
        @foreach ($p->getUrlRange(1, $last) as $page => $url)
            @php($isCurrent = $page == $current)
            @php($isBoundary = $page == 1 || $page == $last)
            @php($isNear = abs($page - $current) < 2)
            @if ($isCurrent)
                <li><span class="px-3.5 py-2 text-sm font-medium text-white bg-orange-500 rounded-lg">{{ $page }}</span></li>
            @elseif ($isBoundary || $isNear)
                <li><a href="{{ $url }}"
                        class="px-3.5 py-2 text-sm text-[#64748B] hover:text-orange-600 hover:bg-orange-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">{{ $page }}</a></li>
            @else
                <li><span class="px-3.5 py-2 text-sm text-[#94A3B8]">…</span></li>
            @endif
        @endforeach

        {{-- Next --}}
        @if ($p->onLastPage())
            <li><span class="px-3 py-2 text-sm text-[#94A3B8] rounded-lg">›</span></li>
        @else
            <li><a href="{{ $p->nextPageUrl() }}"
                    class="px-3 py-2 text-sm text-[#64748B] hover:text-orange-600 hover:bg-orange-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                ›
            </a></li>
        @endif
    </ul>
</nav>
@endif