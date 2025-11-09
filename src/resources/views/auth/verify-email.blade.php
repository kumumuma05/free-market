@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify.css') }}">
@endsection

@section('content')

    <!-- セッションメッセージ -->
    @if(session('status'))
        <div class="verify-email__alert">
            {{ session('status') }}
        </div>
    @endif

    <div class="verify-email">
        <p class="verify-email__text">登録していただいたメールアドレスに承認メールを送付しました。</p>
        <p class="verify-email__text">メール認証を完了してください。</p>

        <div class="verify-email__link">
            <a class="verify-email__link-button" href="http://localhost:8025">認証はこちらから</a>
        </div>

        <form class="verify-email__form" method="post" action="/email/verification-notification">
            @csrf
            <button class="verify-email__form-button" type="submit">認証メールを再送する</button>
        </form>
    </div>

@endsection