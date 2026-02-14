@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage/mypage.css') }}">
@endsection

@section('content')
    <div class="mypage">

        <!-- セッションメッセージ表示 -->
        @if(session('status'))
            <div class="mypage__alert">
                {{ session('status') }}
            </div>
        @endif

        <!-- アバター・ユーザ名・編集ボタン -->
        <div class="profile-info">
            <div class="profile-info__main">
                <div class="profile-info__avatar">
                    <img class="profile-info__avatar-img" src="{{ $user->profile_image_url }}" alt="プロフィール画像">
                </div>

                <div class="profile-info__heading">
                    <h1 class="profile-info__name">
                        {{ $user->name }}
                    </h1>

                    @if ($ratingsCount > 0)
                        <div class="profile-info__rating">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="profile-info__rating-star {{ $i <= $averageRating ? 'profile-info__rating-star--active' : '' }}">
                                    ★
                                </span>
                            @endfor
                        </div>
                    @endif
                </div>
            </div>

            <div class="profile-info__action">
                <a class="profile-info__action-link" href="/mypage/profile">プロフィールを編集</a>
            </div>
        </div>

        <!-- タブ -->
        <nav class="mypage-tab">
            <a class="mypage-tab__link {{ $tab === 'sell' ? 'mypage-tab__link--active' : '' }}" href="/mypage?page=sell">出品した商品
            </a>
            <a class="mypage-tab__link {{ $tab === 'buy' ? 'mypage-tab__link--active' : '' }}" href="/mypage?page=buy">購入した商品
            </a>
            <a class="mypage-tab__link {{ $tab === 'trading' ? 'mypage-tab__link--active' : '' }}" href="/mypage?page=trading">
                取引中の商品
            </a>
            @if ($tradingCount > 0)
                <span class="mypage-tab__badge">{{ $tradingCount }}</span>
            @endif
        </nav>

        <!-- 商品一覧 -->
        <ul class="item-list">
            @if ($tab === 'trading')
                @foreach($tradingPurchases as $purchase)
                    <li class="item-list__item">
                        <a class="item-list__card" href="/transaction/{{ $purchase->id }}">

                            <div class="item-list__card-image">
                                @if(($purchase->unread_count ?? 0) > 0)
                                    <span class="item-list__card-badge">
                                        {{ $purchase->unread_count }}
                                    </span>
                                @endif
                                <img src="{{ $purchase->item->image_url }}" alt="商品画像">
                            </div>

                            <p class="item-list__card-title">
                                {{ $purchase->item->product_name }}
                            </p>
                        </a>
                    </li>
                @endforeach
            @else
                @foreach($items as $item)
                        <li class="item-list__item">
                            <a class="item-list__card" href="/item/{{ $item->id }}">
                                <div class="item-list__card-image">
                                    <img src="{{ $item->image_url }}" alt="商品画像">
                                    @if($item->is_sold)
                                        <span class="item-list__card-sold">Sold</span>
                                    @endif
                                </div>
                            <p class="item-list__card-title">{{ $item->product_name }}</p>
                        </a>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
@endsection