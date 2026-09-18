@extends('layouts.app')

@php
    $description = $visual->description ?: "{$visual->category->name} 카테고리의 기술 문서: {$visual->title}";

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
                        'item' => route('visuals.index', ['category' => $visual->category->slug]),
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
    <div style="{{ $visual->category?->color ? "--accent: {$visual->category->color};" : '' }}">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('visuals.index') }}">홈</a>
        <span class="sep">/</span>
        <a href="{{ route('visuals.index', ['category' => $visual->category->slug]) }}">{{ $visual->category->name }}</a>
        <span class="sep">/</span>
        <span class="current" aria-current="page">{{ $visual->title }}</span>
    </nav>

    <article class="visual-article" itemscope itemtype="https://schema.org/TechArticle">
        <header class="visual-meta">
            <h1 itemprop="headline">{{ $visual->title }}</h1>
            <div class="muted small">
                <a href="{{ route('visuals.index', ['category' => $visual->category->slug]) }}" class="cat-link"
                   itemprop="articleSection">{{ $visual->category->name }}</a>
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
                <button type="button" class="btn" id="toggle-full" aria-keyshortcuts="F">전체화면 <kbd class="kbd">F</kbd></button>
                <a href="{{ route('visuals.render', $visual) }}" target="_blank" rel="noopener" class="btn">새 탭에서 열기</a>
            @endif
            <a href="{{ route('visuals.index') }}" class="btn" id="back-list" aria-keyshortcuts="Escape">목록 <kbd class="kbd">Esc</kbd></a>
        </div>

        @if ($visual->file)
            <div class="full-hint">전체화면으로 보면 더 편해요!</div>

            <section class="visual-content" aria-label="문서 뷰어">
                <div class="frame-wrap" id="frame-wrap">
                    <div class="frame-loader" id="frame-loader">
                        <div class="frame-loader-bar-wrap">
                            <div class="frame-loader-bar" id="frame-loader-bar"></div>
                        </div>
                        <div class="frame-loader-text">문서를 불러오는 중...</div>
                    </div>
                    <iframe src="{{ route('visuals.render', $visual) }}"
                            id="visual-frame"
                            sandbox="allow-scripts allow-popups"
                            title="{{ $visual->title }}"></iframe>
                </div>
                <button type="button" class="btn frame-exit" id="exit-full" aria-keyshortcuts="Escape">전체화면 종료 <kbd class="kbd">Esc</kbd></button>
            </section>

            <script>
                (function () {
                    var wrap = document.getElementById('frame-wrap');
                    var toggle = document.getElementById('toggle-full');
                    var exit = document.getElementById('exit-full');
                    var loader = document.getElementById('frame-loader');
                    var bar = document.getElementById('frame-loader-bar');
                    var iframe = document.getElementById('visual-frame');

                    function setFull(on) {
                        wrap.classList.toggle('is-full', on);
                        document.body.style.overflow = on ? 'hidden' : '';
                    }

                    if (toggle) {
                        toggle.addEventListener('click', function () {
                            setFull(true);
                        });
                    }
                    if (exit) {
                        exit.addEventListener('click', function () {
                            setFull(false);
                        });
                    }
                    if (iframe && loader && bar) {
                        var progress = 12;
                        bar.style.width = progress + '%';

                        var progressTimer = setInterval(function () {
                            if (progress < 70) {
                                progress += Math.random() * 12 + 6;
                            } else if (progress < 90) {
                                progress += Math.random() * 4 + 1;
                            }
                            if (progress > 90) progress = 90;
                            bar.style.width = progress + '%';
                        }, 120);

                        function finishLoading() {
                            if (!loader) return;
                            clearInterval(progressTimer);
                            bar.style.width = '100%';
                            setTimeout(function () {
                                loader.classList.add('is-hidden');
                                setTimeout(function () {
                                    if (loader && loader.parentNode) {
                                        loader.style.display = 'none';
                                    }
                                }, 300);
                            }, 150);
                        }

                        iframe.addEventListener('load', finishLoading);
                        iframe.addEventListener('error', finishLoading);

                        setTimeout(function () {
                            if (loader && !loader.classList.contains('is-hidden')) {
                                finishLoading();
                            }
                        }, 10000);
                    }
                })();
            </script>
        @else
            <p class="muted small">저장된 HTML 문서가 없습니다.</p>
        @endif

        <script>
            (function () {
                function handleShortcut(key, code) {
                    // 다이얼로그·모달이 열려 있으면 Esc 는 그쪽 닫기에 양보
                    if (document.querySelector('dialog[open], .is-open')) return false;

                    var wrap = document.getElementById('frame-wrap');
                    var isFull = wrap && wrap.classList.contains('is-full');

                    // 한글 입력 상태에선 e.key 가 'ㄹ' 이 되므로 물리 키 기준으로 판별
                    if (code === 'KeyF' && wrap && !isFull) {
                        document.getElementById('toggle-full').click();
                        return true;
                    }
                    if (key === 'Escape') {
                        document.getElementById(isFull ? 'exit-full' : 'back-list').click();
                        return true;
                    }
                    return false;
                }

                document.addEventListener('keydown', function (e) {
                    if (e.repeat || e.metaKey || e.ctrlKey || e.altKey) return;
                    if (e.target.closest('input, textarea, select, [contenteditable]')) return;
                    if (handleShortcut(e.key, e.code)) {
                        e.preventDefault();
                        return;
                    }

                    // 다이어그램 단계 이동(←/→)·재생(Space)은 iframe 안에서만 키를 받으므로 대신 넘겨줌
                    var iframe = document.getElementById('visual-frame');
                    if (!iframe || ['ArrowLeft', 'ArrowRight', ' '].indexOf(e.key) === -1) return;
                    // 포커스된 버튼·링크의 Space 는 브라우저 기본 클릭 동작 유지
                    if (e.key === ' ' && e.target.closest('button, a')) return;
                    e.preventDefault();
                    iframe.contentWindow.postMessage({ type: 'visual-key', key: e.key, code: e.code }, '*');
                });

                // 다이어그램 iframe 에 포커스가 있을 때 render 응답에 주입된 스크립트가 보낸 단축키
                window.addEventListener('message', function (e) {
                    var iframe = document.getElementById('visual-frame');
                    if (!iframe || e.source !== iframe.contentWindow) return;
                    if (!e.data || e.data.type !== 'visual-shortcut') return;
                    handleShortcut(e.data.key, e.data.code);
                });
            })();
        </script>
    </article>
    </div>
@endsection
