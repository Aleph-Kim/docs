@extends('layouts.app')

@php
    $description = $visual->description ?: "{$visual->category->name} 카테고리의 완결형 HTML 시각화 문서: {$visual->title}";

    $schemaGraph = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'TechArticle',
                '@id' => route('visuals.show', $visual->slug) . '#article',
                'isPartOf' => [
                    '@type' => 'WebSite',
                    '@id' => route('visuals.index') . '#website',
                    'name' => 'Docs',
                    'url' => route('visuals.index'),
                ],
                'headline' => $visual->title,
                'description' => $description,
                'inLanguage' => 'ko-KR',
                'mainEntityOfPage' => route('visuals.show', $visual->slug),
                'datePublished' => $visual->created_at->toIso8601String(),
                'dateModified' => $visual->updated_at->toIso8601String(),
                'articleSection' => $visual->category->name,
                'author' => [
                    '@type' => 'Organization',
                    'name' => 'Docs',
                    'url' => route('visuals.index'),
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'Docs',
                    'url' => route('visuals.index'),
                ],
            ],
            [
                '@type' => 'BreadcrumbList',
                '@id' => route('visuals.show', $visual->slug) . '#breadcrumb',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => '홈',
                        'item' => route('visuals.index'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $visual->category->name,
                        'item' => route('visuals.index', ['category' => $visual->category->id]),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $visual->title,
                        'item' => route('visuals.show', $visual->slug),
                    ],
                ],
            ],
        ],
    ];
@endphp

@section('title', $visual->title)
@section('meta_description', $description)
@section('og_type', 'article')

@section('og_extra')
    <meta property="article:published_time" content="{{ $visual->created_at->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $visual->updated_at->toIso8601String() }}">
    <meta property="article:section" content="{{ $visual->category->name }}">
@endsection

@section('structured_data')
    <script type="application/ld+json">
    {!! json_encode($schemaGraph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('content')
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('visuals.index') }}">홈</a>
        <span class="sep">/</span>
        <a href="{{ route('visuals.index', ['category' => $visual->category->id]) }}">{{ $visual->category->name }}</a>
        <span class="sep">/</span>
        <span class="current" aria-current="page">{{ $visual->title }}</span>
    </nav>

    <article class="visual-article" itemscope itemtype="https://schema.org/TechArticle">
        <header class="visual-meta">
            <h1 itemprop="headline">{{ $visual->title }}</h1>
            <div class="muted small">
                <a href="{{ route('visuals.index', ['category' => $visual->category->id]) }}" class="cat-link" itemprop="articleSection">{{ $visual->category->name }}</a>
                ·
                <time itemprop="datePublished" datetime="{{ $visual->created_at->toIso8601String() }}">
                    {{ $visual->created_at->format('Y-m-d') }}
                </time>
            </div>
            @if ($visual->description)
                <p class="visual-desc small" itemprop="description abstract">{{ $visual->description }}</p>
            @endif
        </header>

        <div class="visual-toolbar">
            @if ($visual->file)
                <button type="button" class="btn" id="toggle-full">전체화면</button>
                <a href="{{ route('visuals.render', $visual) }}" target="_blank" rel="noopener" class="btn">새 탭에서 열기</a>
            @endif
            <a href="{{ route('visuals.index') }}" class="btn">목록</a>
        </div>

        @if ($visual->file)
            <section class="visual-content" aria-label="문서 뷰어">
                <div class="frame-wrap" id="frame-wrap">
                    <iframe src="{{ route('visuals.render', $visual) }}"
                            sandbox="allow-scripts allow-popups"
                            title="{{ $visual->title }}"></iframe>
                </div>
                <button type="button" class="btn frame-exit" id="exit-full">전체화면 종료</button>
            </section>

            <script>
                (function () {
                    var wrap = document.getElementById('frame-wrap');
                    var toggle = document.getElementById('toggle-full');
                    var exit = document.getElementById('exit-full');

                    function setFull(on) {
                        wrap.classList.toggle('is-full', on);
                        document.body.style.overflow = on ? 'hidden' : '';
                    }

                    toggle.addEventListener('click', function () {
                        setFull(true);
                    });
                    exit.addEventListener('click', function () {
                        setFull(false);
                    });
                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape') setFull(false);
                    });
                })();
            </script>
        @else
            <p class="muted small">저장된 HTML 문서가 없습니다.</p>
        @endif
    </article>
@endsection
