@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="페이지 네비게이션">
        {{-- 이전 페이지 링크 --}}
        @if ($paginator->onFirstPage())
            <span class="page-item is-disabled" aria-disabled="true" aria-label="이전 페이지">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-item" rel="prev" aria-label="이전 페이지">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
        @endif

        {{-- 페이지 번호 목록 --}}
        @foreach ($elements as $element)
            {{-- 생략 부호 (...) --}}
            @if (is_string($element))
                <span class="page-item is-dots" aria-disabled="true">{{ $element }}</span>
            @endif

            {{-- 페이지 번호 링크 --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-item is-active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-item" aria-label="{{ $page }}페이지로 이동">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- 다음 페이지 링크 --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-item" rel="next" aria-label="다음 페이지">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
            </a>
        @else
            <span class="page-item is-disabled" aria-disabled="true" aria-label="다음 페이지">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
            </span>
        @endif
    </nav>
@endif
