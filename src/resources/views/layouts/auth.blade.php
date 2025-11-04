<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtec-flea-market</title>
    <link rel="stylesheet" href="{{ asset('css/base/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}">
    @yield('css')
</head>

<body>
    <div class="auth">
        <header class="header">
            <a href="/">
                <img class="header__heading-logo" src="{{ asset('images/logo.svg') }}" alt="ロゴ">
            </a>
        </header>

        <main>
            <div class="content">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>