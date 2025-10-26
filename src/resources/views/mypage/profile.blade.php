@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

@endsection

@section('content')
<div class="profile">
    <div class="profile-title">
        <h2>プロフィール設定</h2>
    </div>
    <form class="profile-image__form" method="get" action="/mypage/profile">
        <img class="profile__image" src="{{ session('temp_image') ? Storage::url(session('temp_image')) : Storage::url($user->profile_image) }}" alt="プロフィール画像">
        <label class="profile-form__avatar-choice" for="profile-image">画像を選択する</label>
    </form>

    <form method="post" action="/mypage/profile/session" enctype="multipart/form-data">
        @csrf
        <input type="file" id="profile-image" name="profile_image" accept="image/*" hidden onchange="this.form.submit()" />
    </form>

    <form class="profile-form" action="/mypage/profile/update" method="post" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="profile-form__group">
            <label>ユーザー名</label>
            <input type="text" name="name" value="{{ old('name',  $user->name) }}" />
            @error('name')
                {{ $message}}
            @enderror
        </div>
        <div class="profile-form__group">
            <label>郵便番号</label>
            <input type="text" name="postal" value="{{ old('postal',  $user->postal) }}" />
            @error('postal')
                {{ $message}}
            @enderror
        </div>
        <div class="profile-form__group">
            <label>住所</label>
            <input type="text" name="address" value="{{  old('address', $user->address) }}" />
            @error('address')
                {{ $message}}
            @enderror
        </div>
        <div class="profile-form__group">
            <label>建物名</label>
            <input type="text" name="building" value="{{ old('building', $user->building) }}" />
        </div>
        <div class="profile-form__button">
            <button type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection