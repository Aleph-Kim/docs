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
</body>
</html>
