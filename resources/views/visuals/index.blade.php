@extends('layouts.app')

@section('title', '문서 목록')

@section('content')
    <div class="page-head">
        <h1>문서 목록</h1>
        <span class="muted small">{{ $visuals->total() }}개</span>
    </div>

    <div class="chips">
        <a href="{{ route('visuals.index', array_filter(['q' => $keyword])) }}"
           class="chip {{ $activeCategory ? '' : 'is-active' }}">전체</a>
        @foreach ($categories as $category)
            <a href="{{ route('visuals.index', array_filter(['category' => $category->id, 'q' => $keyword])) }}"
               class="chip {{ (int) $activeCategory === $category->id ? 'is-active' : '' }}">{{ $category->name }}</a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('visuals.index') }}" class="search">
        @if ($activeCategory)
            <input type="hidden" name="category" value="{{ $activeCategory }}">
        @endif
        <input type="text" name="q" value="{{ $keyword }}" placeholder="제목·설명 검색">
        <button type="submit" class="btn">검색</button>
    </form>

    @if ($visuals->isEmpty())
        <div class="empty">등록된 문서가 없습니다.</div>
    @else
        <div class="grid">
            @foreach ($visuals as $visual)
                <a href="{{ route('visuals.show', $visual->slug) }}" class="card">
                    <span class="card-cat">{{ $visual->category->name }}</span>
                    <h3>{{ $visual->title }}</h3>
                    @if ($visual->description)
                        <div class="card-desc">{{ $visual->description }}</div>
                    @endif
                    <div class="card-date">{{ $visual->created_at->format('Y-m-d') }}</div>
                </a>
            @endforeach
        </div>

        <div class="pagination-wrap">
            {{ $visuals->links() }}
        </div>
    @endif
@endsection
