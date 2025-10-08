@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

@endsection

@section('content')
<div class="profile">
    <div class="profile-title">
        <h2>プロフィール設定</h2>
    </div>

    <form class="profile-form" action="/mypage/profile/update" method="post" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="profile-form__avatar">
            <img src="{{ $user->profile_image ? asset('storage/'.$user->profile_image) : asset('images/green.png') }}" alt="">
            <label class="profile-form__avatar-choice">
                画像を選択する
                <input type="file" name="profile_image" accept="image/*" hidden>
            </label>
        </div>

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