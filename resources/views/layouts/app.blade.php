<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="@yield('meta_robots', request()->is('admin*') ? 'noindex, nofollow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')">
    <title>{{ View::hasSection('title') ? View::yieldContent('title') . ' - Docs' : 'Docs - 인터랙티브 시각화 & 기술 문서 아카이브' }}</title>
    <meta name="description" content="@yield('meta_description', '인터랙티브 시각화 및 기술 문서를 저장하고 열람하는 아카이브 Docs')">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

    {{-- Favicon & App Icons --}}
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#0f766e">

    {{-- Open Graph --}}
    <meta property="og:site_name" content="Docs">
    <meta property="og:title" content="@yield('og_title', View::hasSection('title') ? View::yieldContent('title') . ' - Docs' : 'Docs - 인터랙티브 시각화 & 기술 문서 아카이브')">
    <meta property="og:description" content="@yield('og_description', View::hasSection('meta_description') ? View::yieldContent('meta_description') : '인터랙티브 시각화 및 기술 문서를 저장하고 열람하는 아카이브 Docs')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('canonical_url', url()->current())">
    <meta property="og:locale" content="ko_KR">
    <meta property="og:image" content="@yield('og_image', asset('og-image.png'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Docs - 인터랙티브 시각화 & 기술 문서 아카이브">
    @yield('og_extra')

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', View::hasSection('title') ? View::yieldContent('title') . ' - Docs' : 'Docs - 인터랙티브 시각화 & 기술 문서 아카이브')">
    <meta name="twitter:description" content="@yield('og_description', View::hasSection('meta_description') ? View::yieldContent('meta_description') : '인터랙티브 시각화 및 기술 문서를 저장하고 열람하는 아카이브 Docs')">
    <meta name="twitter:image" content="@yield('og_image', asset('og-image.png'))">

    {{-- Schema.org / JSON-LD Structured Data --}}
    @yield('structured_data')

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
