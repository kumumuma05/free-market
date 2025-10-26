@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/show.css') }}">
@endsection

@section('content')
    <div class="item-show">

        <!-- 商品画像 -->
        <div class="item-image">
                <img src="{{ $item->image_url }}" alt="商品画像">
        </div>

        <!-- 商品詳細 -->
        <div class="item-detail">

            <!-- 商品名・ブランド・価格 -->
            <h2 class="item-detail__title">
                {{ $item->product_name }}
            </h2>
            <div class="item-detail__brand">
                {{ $item->brand }}
            </div>
            <div class="item-detail__price">
                ¥<span>{{ number_format($item->price) }} </span>(税込)
            </div>

            <!-- いいね・コメントカウント -->
            <div class="item-detail__count">

                <!-- いいね -->
                <div class="like-count__inner">
                    <form class="like-count__form" action="/item/{{ $item->id }}/like" method="post">
                    @csrf
                        <button class="like-count__button {{ $isLiked ? 'is-active' : '' }}" type="submit">
                            <svg data-testid="like-icon" viewBox="0 0 22 22" width ="40" hight="40" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round">
                                <path d="M12 17.27L18.18 21 16.54 13.97 22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                            </svg>
                        </button>
                    </form>
                    <span class="">{{ $likeCount ?? 0 }}</span>
                </div>

                <!-- コメント数 -->
                <div class="comment-count__inner">
                    <svg data-testid="comment-icon" viewBox="0 0 35 35" width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" stroke-linecap="round">
                        <path d="M20 6 a14 14 0 1 1 -8.5 25.5 l-4.5 2.5 2-5 a14 14 0 0 1 11-23z" fill="none" />
                    </svg>
                    <span>{{ $commentCount ?? 0 }}</span>
                </div>
            </div>

            <!-- 購入手続きリンク -->
            <a class="item-purchase__link-button" href="/purchase/{{ $item->id }}">購入手続きへ</a>

            <!-- 商品説明 -->
            <div class="item-detail__description">
                <h3 class="description__title">商品説明</h3>
                <div class="description__body">
                    {!! nl2br(e($item->description)) !!}
                </div>
            </div>

            <!-- 商品の情報 -->
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

            <!-- コメント表示 -->
            <div class="item-detail__comment">
                <h3 class="item-detail__comment-title">コメント( {{ $item->comments->count() }} ) </h3>
                @foreach($item->comments as $comment)
                    <div class="item-detail__comment-user">
                        <img class="comment__avatar" src="{{ asset('storage/' . $comment->user->profile_image) }}" alt="{{ $comment->user->name }}のプロフィール画像">
                        <p class="comment__user-name">{{ $comment->user->name }}</p>
                    </div>
                    <p class="comment__body">{!! nl2br(e( $comment->body)) !!}</p>
                @endforeach
            </div>

            <!-- コメント作成 -->
            <form class="comment-create__form" action="/item/{{ $item->id }}/comments" method="post">
                @csrf
                <label class="comment-create__form-label" for="comment">商品へのコメント</label>
                <textarea class="comment-create__form-body" name="body" id="comment">{{ old('body') }}</textarea>
                @error('body')
                    <div class="form__error">
                        {{ $message }}
                    </div>
                @enderror
                <button class="comment-create__form-button" type="submit">コメントを送信する</button>
            </form>
        </div>
    </div>
@endsection