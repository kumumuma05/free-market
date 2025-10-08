@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage">
    <!-- アバター・ユーザ名・編集ボタン -->
    <div class="profile-info">
        <div class="profile-info__avatar">
            <img class="profile-info__avatar-img" src="{{ $user->profile_image ? asset('storage/'.$user->profile_image) : asset('images/green.png') }}" alt="プロフィール画像">
        </div>
        <div class="profile-info__user-name">
            <h2>ユーザー名</h2>
        </div>
        <div class="profile-info__action">
            <a class="profile-info__action-link" href="/mypage/profile">プロフィールを編集</a>
        </div>
    </div>
    <!-- タブ -->
    <nav class="mypage-tab">
        <a class="mypage-tab__link {{ $page === 'sell' ? 'mypage-tab__link--active' : '' }}" href="/mypage?page=sell">出品した商品
        </a>
        <a class="mypage-tab__link {{ $page === 'buy' ? 'mypage-tab__link--active' : '' }}" href="/mypage?page=buy">購入した商品
        </a>
    </nav>

    <!-- 商品一覧 -->
    <ul class="item-list">
        @foreach($items as $item)
            <li class="item-list__item">
                <a class="item-list__card" href="/item/{{ $item->id }}">
                    <div class="item-list__card-image">
                        <img src="{{ $item->image_path }}" alt="商品画像">
                        @if($item->is_sold)
                            <span class="item-list__card-sold">Sold</span>
                        @endif
                    </div>
                    <p class="item-list__card-title">{{ $item->product_name }}</p>
                </a>
            </li>
        @endforeach
    </ul>
</div>
@endsection