<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtech-flea-market</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <div class="app">
        <header class="header">
            <img class="header__logo" src="{{ asset('images/logo.svg') }}" alt="ロゴ">
            <form class="header__search" action="/item/search" method="get">
                <input class="header__search-input" type="text" name="keyword" value="{{ old('keyword') }}"  placeholder="何をお探しですか？" />
            </form>
            <nav class="header__nav">
                <ul class="header__nav-list">
                    @if(Auth::check())
                    <li class="header__nav-item">
                        <form class="header__logout-form" action="/logout" method="post">
                            @csrf
                            <button class="header__logout-button" type="submit">ログアウト</button>
                        </form>
                    </li>
                    @else
                    <li class="header__nav-item">
                        <a href="/login">ログイン</a>
                    </li>
                    @endif
                    <li class="header__nav-item"><a href="/mypage">マイページ</a></li>
                    <li class="header__nav-item--listing"><a href="/sell">出品</a></li>
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