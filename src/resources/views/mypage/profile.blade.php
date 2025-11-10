@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage/profile.css') }}">
@endsection

@section('content')

    <!-- セッションメッセージ表示 -->
    @if(session('status'))
        <div class="session-alert">
            {{ session('status') }}
        </div>
    @endif

    <div class="profile">

        <!-- タイトル -->
        <div class="profile__title">
            <h2>プロフィール設定</h2>
        </div>

        <!-- 画像選択 -->
        <div class="profile__image-wrap">
            <img class="profile__image" src="{{ $user->profile_image_url }}" alt="">
            <label class="profile__image-label" for="profile-image">画像を選択する</label>
        </div>
        <div class="profile__error">
            @error('profile_image')
                {{ $message}}
            @enderror
        </div>

        <form method="post" action="/mypage/profile/session" enctype="multipart/form-data">
            @csrf
            <input type="file" id="profile-image" name="profile_image" accept="image/*" hidden onchange="this.form.submit()" />
        </form>

        <!-- ユーザー情報入力フォーム -->
        <form class="profile__form" action="/mypage/profile" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <!-- ユーザー名 -->
            <div class="profile__form-group">
                <label>ユーザー名</label>
                <input type="text" name="name" value="{{ old('name',  $user->name) }}" />
                <div class="profile__error">
                    @error('name')
                        {{ $message}}
                    @enderror
                </div>
            </div>

            <!-- 郵便番号 -->
            <div class="profile__form-group">
                <label>郵便番号</label>
                <input type="text" name="postal" value="{{ old('postal',  $user->postal) }}" />
                <div class="profile__error">
                    @error('postal')
                        {{ $message}}
                    @enderror
                </div>
            </div>

            <!-- 住所 -->
            <div class="profile__form-group">
                <label>住所</label>
                <input type="text" name="address" value="{{  old('address', $user->address) }}" />
                <div class="profile__error">
                    @error('address')
                        {{ $message}}
                    @enderror
                </div>
            </div>

            <!-- 建物名 -->
            <div class="profile__form-group">
                <label>建物名</label>
                <input type="text" name="building" value="{{ old('building', $user->building) }}" />
            </div>

            <!-- 送信ボタン -->
            <div class="profile__form-button">
                <button type="submit">更新する</button>
            </div>
        </form>
    </div>
@endsection