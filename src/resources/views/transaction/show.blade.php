@extends('layouts.auth')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/transaction/show.css') }}">
@endsection

@section('content')
    <div class="transaction">

        <!-- 左サイド -->
        <aside class="transaction__sidebar">
            <p class="transaction__sidebar-title">その他の取引</p>

            @foreach($sidebarPurchases as $sidePurchase)
                <a class="transaction__sidebar-link" href="/transaction/{{ $sidePurchase->id }}">
                    {{ $sidePurchase->item->product_name }}
                </a>
            @endforeach
        </aside>

        <!-- 右メイン -->
        <main class="transaction__main">

            <!-- ヘッダー -->
            <div class="transaction__header">
                <div class="transaction__header-left">
                    <div class="transaction__avatar">
                        @if ($isBuyer)
                            <img src="{{ $purchase->item->seller->profile_image_url ?? asset('profile_image/default.png') }}" alt="プロフィール画像">
                        @else
                            <img src="{{ $purchase->buyer->profile_image_url ?? asset('profile_image/default.png') }}" alt="プロフィール画像">
                        @endif
                    </div>

                    <p class="transaction__header-title">
                        @if ($isBuyer)
                            「{{ $purchase->item->seller->name ?? '出品者' }}」さんとの取引画面
                        @else
                            「{{ $purchase->buyer->name ?? '購入者' }}」さんとの取引画面
                        @endif
                    </p>
                </div>

                <div class="transaction__header-right">
                    @if ($isBuyer)
                        <form action="/transaction/{{ $purchase->id }}/complete" method="post">
                            @csrf
                            <button class="transaction__complete-button" type="submit">取引を完了する</button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- 商品情報 -->
            <section class="transaction__item">
                <div class="transaction__item-img">
                    <img src="{{ $purchase->item->image_url }}" alt="商品画像">
                </div>

                <div class="transaction__item-info">
                    <p class="transaction__item-name">{{ $purchase->item->product_name }}</p>
                    <p class="transaction__item-price">¥{{ number_format($purchase->item->price) }}</p>
                </div>
            </section>

            <!-- メッセージ -->
            <section class="transaction__messages">
                @forelse($messages as $message)
                    @php
                        $isMine = ($message->sender_id === auth()->id());
                    @endphp

                    <!-- メッセージ全体 -->
                    <div class="message {{ $isMine ? 'message--mine' : 'message--other' }}">

                        <!-- メッセージ送信者情報 -->
                        <div class="message__meta">
                            <span class="message__name">{{ $message->sender->name }}</span>
                            <img class="message__avatar" src="{{ $message->sender->profile_image_url ?? asset('profile_image/default.png') }}" alt="プロフィール画像">
                        </div>
                        <!-- メッセージ本体 -->
                        <div class="message__bubble">
                            <p class="message__text">{{ $message->body }}</p>

                            @if($message->image_path)
                                <div class="message__image">
                                    <img src="{{ asset('storage/' . $message->image_path) }}" alt="添付画像">
                                </div>
                            @endif
                        </div>

                        <!-- メッセージ編集・削除（自分が送信したメッセージのみ） -->
                        @if($isMine)
                            <div class="message__actions">
                                <!-- 編集リンク -->
                                <a class="message__edit-link" href="/transaction/{{ $purchase->id }}/messages/{{ $message->id }}/edit">
                                    編集
                                </a>

                                <!-- 削除 -->
                                <form action="/transaction/{{ $purchase->id }}/messages/{{ $message->id }}" method="post" onsubmit="return confirm('削除しますか？')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="message__delete-button" type="submit">削除</button>
                                </form>
                            </div>
                        @endif
                    </div>

                @empty
                    <p class="transaction__empty">まだメッセージはありません</p>
                @endforelse
            </section>

            <!-- 送信フォーム -->
            <section class="transaction__form">
                @php
                    $isEditing = !empty($editingMessage);
                @endphp

                @error('body')
                        <p class="transaction__error">{{ $message }}</p>
                    @enderror
                    @error('image')
                        <p class="transaction__error">{{ $message }}</p>
                @enderror

                <form action="{{ $isEditing ? "/transaction/{$purchase->id}/messages/{$editingMessage->id}" : "/transaction/{$purchase->id}/messages" }}" method="post" enctype="multipart/form-data">
                    @csrf

                    @if ($isEditing)
                        @method('PUT')
                    @endif

                    <div class="transaction__form-row">
                        <textarea class="transaction__form-textarea" name="body" placeholder="取引メッセージを記入してください">{{ old('body', $isEditing ? $editingMessage->body : '') }}</textarea>

                        <label class="transaction__upload">
                            <input type="file" name="image" accept="image/*" hidden>
                            画像を追加
                        </label>

                        <button class="transaction__send" type="submit">
                            <img src="{{ asset('images/紙飛行機.jpg') }}" alt="紙飛行機">
                        </button>
                    </div>
                </form>
            </section>
        </main>

        @php
  $showRatingModal = session('show_rating_modal')&& !$myRating;
@endphp

<div class="rating-modal {{ $showRatingModal ? 'is-open' : '' }}">
  <a class="rating-modal__overlay" href="/transaction/{{ $purchase->id }}"></a>

  <div class="rating-modal__panel" role="dialog" aria-modal="true">
    <p class="rating-modal__title">取引が完了しました。</p>

    {{-- ★評価フォーム（例） --}}
    <form method="post" action="/transaction/{{ $purchase->id }}/ratings">
      @csrf
      {{-- 星UIはあなたの実装に合わせて --}}
      <button type="submit">送信する</button>
    </form>

    <a class="rating-modal__close" href="/transaction/{{ $purchase->id }}">×</a>
  </div>
</div>
    </div>
@endsection