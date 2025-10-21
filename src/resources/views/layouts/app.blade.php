<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtech-flea-market</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gorditas:wght@400;700&family=Inika:wght@400;700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Noto+Serif+JP:wght@200..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <div class="app">
        <header class="header">
            <img class="header__logo" src="{{ asset('images/logo.svg') }}" alt="ロゴ">
            <form class="header__search" action="/item/search" method="get">
                <input class="header__search-input" type="text" name="keyword" value="{{ request('keyword', '') }}"  placeholder="何をお探しですか？" />
                <input type="hidden" name="tab" value="{{ $activeTab ?? '' }}">
            </form>
            <nav class="header__nav">
                <ul class="header__nav-list">
                    @if(Auth::check())
                    <li class="header__nav-item">
                        <form class="header__nav-form" action="/logout" method="post">
                            @csrf
                            <button class="nav__pill nav__pill--button" type="submit">ログアウト</button>
                        </form>
                    </li>
                    @else
                    <li class="header__nav-item">
                        <a class="nav__pill" href="/login">ログイン</a>
                    </li>
                    @endif
                    <li class="header__nav-item"><a class="nav__pill" href="/mypage">マイページ</a></li>
                    <li class="header__nav-item--listing"><a class="nav__pill" href="/sell">出品</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <div class="content">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>