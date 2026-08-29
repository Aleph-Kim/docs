<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title', 'Docs')</title>
    @vite('resources/css/app.css')
</head>
<body>
    <header class="site-header">
        <div class="wrap">
            <a href="{{ route('visuals.index') }}" class="site-name">Docs</a>
            <nav class="site-nav">
                @if (session('is_admin'))
                    <a href="{{ route('admin.visuals.index') }}">문서 관리</a>
                    <a href="{{ route('admin.categories.index') }}">카테고리</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="btn">로그아웃</button>
                    </form>
                @else
                    <a href="{{ route('admin.login') }}">로그인</a>
                @endif
            </nav>
        </div>
    </header>

    <main class="wrap">
        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>

    <div class="responsive-modal-backdrop" id="responsive-modal">
        <div class="responsive-modal" role="dialog" aria-modal="true" aria-labelledby="responsive-modal-title">
            <h2 id="responsive-modal-title">알림</h2>
            <p>이 홈페이지는 모바일에 최적화 되어있지 않습니다.</p>
            <p>PC에서의 접근을 권장드립니다.</p>
            <button type="button" class="btn btn-accent" id="responsive-modal-ok">확인</button>
        </div>
    </div>

    <script>
        (function () {
            var KEY = 'visuals-mobile-notice-seen';
            var modal = document.getElementById('responsive-modal');
            var okBtn = document.getElementById('responsive-modal-ok');

            function isMobile() {
                return window.matchMedia('(max-width: 768px)').matches;
            }

            function hasSeenToday() {
                return document.cookie.split('; ').indexOf(KEY + '=1') !== -1;
            }

            function markSeen() {
                // 자정에 만료시켜 하루에 한 번만 노출
                var midnight = new Date();
                midnight.setHours(24, 0, 0, 0);
                document.cookie = KEY + '=1; expires=' + midnight.toUTCString() + '; path=/; SameSite=Lax';
            }

            function closeModal() {
                modal.classList.remove('is-open');
            }

            if (isMobile() && !hasSeenToday()) {
                modal.classList.add('is-open');
                markSeen();
            }

            okBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeModal();
            });
        })();
    </script>
</body>
</html>
