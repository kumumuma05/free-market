@extends('layouts.auth')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
    <div class="register">
        <!-- タイトル -->
        <div class="register-form">
            <h1 class="register-form__heading">会員登録</h1>
        </div>

        <!-- 入力フォーム -->
        <form class="register-form__form" action="/register" method="post" novalidate>
            @csrf

            <!-- ユーザー名 -->
            <div class="register-form__group">
                <label class="register-form__label" for="name">ユーザー名</label>
                <input class="register-form__input" type="text" id="name" name="name" value="{{ old('name') }}">
                <p class="register-form__error-message">
                    @error('name')
                        {{ $message }}
                    @enderror
                </p>
            </div>

            <!-- メールアドレス -->
            <div class="register-form__group">
                <label class="register-form__label" for="email">メールアドレス</label>
                <input class="register-form__input" type="email" id="email" name="email" value="{{ old('email') }}">
                <p class="register-form__error-message">
                    @error('email')
                        {{ $message }}
                    @enderror
                </p>
            </div>

            <!-- パスワード -->
            <div class="register-form__group">
                <label class="register-form__label" for="password">パスワード</label>
                <input class="register-form__input" type="password" id="password" name="password">
                <p class="register-form__error-message">
                    @error('password')
                        {{ $message }}
                    @enderror
                </p>
            </div>

            <!-- 確認用パスワード -->
            <div class="register-form__group">
                <label class="register-form__label" for="password_confirmation">確認用パスワード</label>
                <input class="register-form__input" type="password" id="password_confirmation" name="password_confirmation">
                <p class="register-form__error-message">
                    @error('password_confirmation')
                        {{ $message }}
                    @enderror
                </p>
            </div>

            <!-- 送信ボタン -->
            <input class="register-form__button" type="submit" value="登録する">
        </form>

        <!-- ログインリンク -->
        <div class="register-link">
            <a class="register-link__login-link" href="/login">ログインはこちら</a>
        </div>
    </div>
@endsection
