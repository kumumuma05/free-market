@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/item/index.css') }}">
@endsection

@section('content')
    <div class="item-index">

        <!-- セッションメッセージ表示 -->
        @if(session('status'))
            <div class="item-index__alert">
                {{ session('status') }}
            </div>
        @endif

        <!-- タブ -->
        <div class="item-index__tab-menu">
            <a class="item-index__tab-menu-link {{ $activeTab === 'recommend' ? 'tab-menu__link--active' : '' }}" href="/">おすすめ</a>
            <a class="item-index__tab-menu-link {{ $activeTab === 'mylist' ? 'tab-menu__link--active' : '' }}" href="/?tab=mylist&keyword={{ urlencode($keyword ?? request('keyword', '' )) }}">マイリスト</a>
        </div>

        <!-- 一覧 -->
        <ul class="item-index__list">
            @foreach($items as $item)
                <li class="item-index__list-item">
                    <a class="item-index__list-card" href="/item/{{ $item->id }}">
                        <div class="item-index__list-card-image">
                            <img src="{{ $item->image_url }}" alt="商品画像">
                            @if($item->is_sold)
                                <span class="item-index__list-card-sold">Sold</span>
                            @endif
                        </div>
                        <p class="item-index__list-card-title">{{ $item->product_name }}</p>
                    </a>
                </li>
            @endforeach
        </ul>

    </div>
@endsection