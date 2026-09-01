@extends('layouts.app')

@php
    $currentCategory = $categories->firstWhere('id', (int) $activeCategory);
    $pageTitle = $currentCategory ? "{$currentCategory->name} 문서 목록" : '문서 목록';
    $pageDesc = $currentCategory
        ? "{$currentCategory->name} 카테고리의 인터랙티브 시각화 및 기술 문서 목록입니다."
        : '기술 개념 설명(eli5), 인터랙티브 다이어그램, 아키텍처 가이드 등의 기술 문서 아카이브입니다.';
    $metaRobots = $keyword ? 'noindex, follow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

    $schemaItemList = $visuals->map(function ($visual, $index) {
        return [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'url' => route('visuals.show', $visual->slug),
            'name' => $visual->title,
        ];
    })->values()->all();

    $schemaGraph = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'WebSite',
                '@id' => route('visuals.index') . '#website',
                'url' => route('visuals.index'),
                'name' => 'Docs',
                'description' => '인터랙티브 시각화 및 기술 문서 아카이브 Docs',
                'inLanguage' => 'ko-KR',
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => route('visuals.index') . '?q={search_term_string}',
                    ],
                    'query-input' => 'required name=search_term_string',
                ],
            ],
            [
                '@type' => 'CollectionPage',
                '@id' => url()->current() . '#collection',
                'url' => url()->current(),
                'name' => $pageTitle,
                'description' => $pageDesc,
                'isPartOf' => [
                    '@id' => route('visuals.index') . '#website',
                ],
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'itemListElement' => $schemaItemList,
                ],
            ],
        ],
    ];
@endphp

@section('title', $pageTitle)
@section('meta_description', $pageDesc)
@section('meta_robots', $metaRobots)

@section('structured_data')
    <script type="application/ld+json">
    {!! json_encode($schemaGraph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

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
        @if ($keyword !== '' || $activeCategory)
            <div class="empty">검색 결과가 없습니다.</div>
        @else
            <div class="empty">등록된 문서가 없습니다.</div>
        @endif
    @else
        <div class="grid">
            @foreach ($visuals as $visual)
                <a href="{{ route('visuals.show', $visual->slug) }}" class="card">
                    <span class="card-cat">{{ $visual->category->name }}</span>
                    <h3>{{ $visual->title }}</h3>
                    @if ($visual->description)
                        <div class="card-desc">{{ $visual->description }}</div>
                    @endif
                    <time class="card-date" datetime="{{ $visual->created_at->toIso8601String() }}">{{ $visual->created_at->format('Y-m-d') }}</time>
                </a>
            @endforeach
        </div>

        <div class="pagination-wrap">
            {{ $visuals->links() }}
        </div>
    @endif
@endsection
