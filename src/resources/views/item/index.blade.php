@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/itemindex.css') }}">
@endsection

@section('content')
<div class="item-index">
    <!-- タブ -->
    <div class="tab-menu">
        <a class="tab-menu__link {{ $activeTab === 'rec' ? 'tab-menu__link--active' : '' }}" href="/">おすすめ</a>
        <a class="tab-menu__link {{ $activeTab === 'mylist' ? 'tab-menu__link--active' : '' }}" href="/?tab=mylist">マイリスト</a>
    </div>
    <!-- 一覧 -->
    <ul class="item-list">
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
    </ul>
</div>
@endsection