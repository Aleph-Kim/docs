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
        <button type="button" class="btn" id="toggle-full">전체화면</button>
        <a href="{{ route('visuals.raw', $visual->slug) }}" target="_blank" rel="noopener" class="btn">새 탭에서 열기</a>
        <a href="{{ route('visuals.index') }}" class="btn">목록</a>
    </div>

    {{--
        저장된 HTML은 완결형 문서이므로 별도 raw 라우트를 iframe src 로 불러온다.
        sandbox 에 allow-same-origin 을 넣지 않는다(넣으면 샌드박스가 무력화됨).
        이 조합에서 인라인 <script> 와 외부 CDN 리소스는 정상 동작하지만
        localStorage / sessionStorage / cookie 접근은 차단된다.
        iframe 높이 자동 조절은 원본 HTML 수정이 필요하므로 고정 높이 + 전체화면 토글로 처리한다.
    --}}
    <div class="frame-wrap" id="frame-wrap">
        <iframe src="{{ route('visuals.raw', $visual->slug) }}"
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

            toggle.addEventListener('click', function () { setFull(true); });
            exit.addEventListener('click', function () { setFull(false); });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') setFull(false);
            });
        })();
    </script>
@endsection
