@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/itemshow.css') }}">
@endsection

@section('content')
<div class="item-show">
    <div class="item-image">
            <img src="{{ $item->image_path }}" alt="商品画像">
    </div>

    <div class="item-detail">
        <h2 class="item-detail__title">
            {{ $item->product_name }}
        </h2>
        <div class="item-detail__brand">
            {{ $item->brand }}
        </div>
        <div class="item-detail__price">
            <span class="price__value">¥</span>{{ number_format($item->price) }}
            <span class="price__tax">(税込)</span>
        </div>
        <div class="item-detail__count">
            <form class="item-detail__like-count" action="/item/{{ $item->id }}/like" method="post">
                @csrf
                <button class="count__button" {{ $isLiked ? 'active' : '' }} type="submit">{{ $isLiked ? '★' : '☆' }} {{ $likeCount ?? 0 }}</button>
            </form>
            <p class="count__button" type="button">💬 {{ $commentCount ?? 0 }}</p>
        </div>
        <div class="item-purchase__link">
            <a class="item-purchase__link-button" href="/purchase/{{ $item->id }}">購入手続きへ</a>
        </div>
        <div class="item-detail__description">
            <h3 class="description__title">商品説明</h3>
            <div class="description__body">
                {{ $item->description }}
            </div>
        </div>
        <div class="item-detail__information">
            <h3 class="information__title">商品の情報</h3>
            <dl class="item-detail__item-info">
                <dt>カテゴリー</dt>
                <dd>
                    @foreach($item->categories as $category)
                        <span class="item-info__category">{{ $category->name }}</span>
                    @endforeach
                </dd>
                <dt>商品の状態</dt>
                <dd>{{ $labels[$item->condition] }}</dd>
            </dl>
        </div>
        <div class="item-detail__comment">
            <h3 class="comment-title">コメント {{ $item->comments->count()}} </h3>
            @foreach($item->comments as $comment)
                <div class="item-detail__user-comment">
                    <p> </p>
                    <div class="comment__meta">
                        <p class="comment__name">{{ $comment->user->name }}</p>
                        <p class="comment__body">{{ $comment->body }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <form class="item-comment__form" action="/item/{{ $item->id }}/comments" method="post">
            @csrf
            <label for="comment">商品へのコメント</label>
            <textarea name="body" id="comment">{{ old('body') }}</textarea>
            @error('body')
                <div class="form__error">
                    {{ $message }}
                </div>
            @enderror
            <button type="submit">コメントを送信する</button>
        </form>
    </div>
</div>
@endsection