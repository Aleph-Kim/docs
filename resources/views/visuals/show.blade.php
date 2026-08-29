@extends('layouts.app')

@section('title', $visual->title)

@section('content')
    <div class="visual-meta">
        <h1>{{ $visual->title }}</h1>
        <div class="muted small">
            {{ $visual->category->name }} · {{ $visual->created_at->format('Y-m-d') }}
        </div>
        @if ($visual->description)
            <p class="small" style="margin-top:6px">{{ $visual->description }}</p>
        @endif
    </div>

    <div class="visual-toolbar">
        @if ($visual->file)
            <button type="button" class="btn" id="toggle-full">전체화면</button>
            <a href="{{ route('visuals.render', $visual) }}" target="_blank" rel="noopener" class="btn">새 탭에서 열기</a>
        @endif
        <a href="{{ route('visuals.index') }}" class="btn">목록</a>
    </div>

    @if ($visual->file)
        {{--
            저장된 HTML 문서 파일을 iframe src 로 직접 불러온다(웹서버가 정적 서빙).
            sandbox 에 allow-same-origin 을 넣지 않는다(넣으면 샌드박스가 무력화됨).
            운영에서는 Apache 가 같은 문서에 CSP sandbox 헤더도 걸어 새 탭 열기까지 격리한다.
            iframe 높이 자동 조절은 원본 HTML 수정이 필요하므로 고정 높이 + 전체화면 토글로 처리한다.
        --}}
        <div class="frame-wrap" id="frame-wrap">
            <iframe src="{{ route('visuals.render', $visual) }}"
                    sandbox="allow-scripts allow-popups"
                    title="{{ $visual->title }}"></iframe>
        </div>
        <button type="button" class="btn frame-exit" id="exit-full">전체화면 종료</button>

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
@endsection
