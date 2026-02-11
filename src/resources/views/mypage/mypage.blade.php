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
            <div class="profile-info__avatar">
                <img class="profile-info__avatar-img" src="{{ $user->profile_image_url }}" alt="プロフィール画像">
            </div>

            <div class="profile-info__user-name">
                <h1 class="profile-info__user-heading">{{ $user->name }}</h1>
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
                @if (!empty($tradingCount) && $tradingCount > 0)
                    <span class="mypage-tab__badge">{{ $tradingCount }}</span>
                @endif
            </a>
        </nav>

        <!-- 商品一覧 -->
        <ul class="item-list">
            @if ($tab === 'trading')

                @foreach($tradingPurchases as $purchase)
                    <li class="item-list__item">
                        <a class="item-list__card"href="/transactions/{{ $purchase->id }}">

                        <div class="item-list__card-image">
                            <img src="{{ $purchase->item->image_url }}" alt="商品画像">

                        {{-- あとで未読バッジをここに出す --}}
                            @if(($purchase->unread_count ?? 0) > 0)
                                <span class="item-list__card-notice">
                                {{ $purchase->unread_count }}
                                </span>
                            @endif
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