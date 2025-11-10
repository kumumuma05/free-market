@extends('layouts.auth')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
    <div class="login">

        <!-- タイトル -->
        <div class="login-form">
            <h2 class="login-form__heading">ログイン</h2>
        </div>

        <!-- 入力フォーム -->
        <div class="login-form__inner">
            <form class="login-form__form" action="/login" method="post" novalidate>
                @csrf

                <!-- メールアドレス -->
                <div class="login-form__group">
                    <label class="login-form__label" for="email">メールアドレス</label>
                    <input class="login-form__input" type="email" id="email" name="email" value="{{ old('email') }}"/>
                    <p class="login-form__error-message">
                        @error('email')
                        {{ $message }}
                        @enderror
                    </p>
                </div>

                <!-- パスワード -->
                <div class="login-form__group">
                    <label class="login-form__label" for="password">パスワード</label>
                    <input class="login-form__input" type="password" id="password" name="password" />
                    <p class="login-form__error-message">
                        @error('password')
                        {{ $message }}
                        @enderror
                    </p>
                </div>

                <!-- 送信ボタン -->
                <input class="login-form__button" type="submit" value="ログインする" />
            </form>

            <!-- 新規登録リンク -->
            <div class="login-link">
                <a class="login-link__register-link" href="/register">会員登録はこちら</a>
            </div>

        </div>
    </div>
@endsection
