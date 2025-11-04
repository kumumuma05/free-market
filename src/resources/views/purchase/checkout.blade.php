@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/checkout.css') }}">
@endsection

@section('content')
    <div class="purchase">
        <section class="purchase-info">

            <!-- 商品情報 -->
            <div class="item__group">
                <img class="item__group-img" src="{{ $item->image_url }}" alt="商品画像">
                <div class="item__group-info">
                    <p class="item__group-product-name">
                        {{ $item->product_name}}
                    </p>
                    <p class="item__group-price">
                        <span>¥</span>  {{ number_format($item->price)}}
                    </p>
                </div>
            </div>

            <!-- 支払い方法 -->
            <form class="payment-select__form" action="/purchase/{{$item->id}}" method="get">
                <label class="payment-select__label" for="payment-select">支払い方法</label>
                <div class="payment-select__wrap">
                    <select class="payment-select__select" name="payment_method" id="payment-select" onchange="this.form.submit()">
                        <option value="" disabled {{ request('payment_method') ? '' : 'selected' }}>選択してください</option>
                        <option value="1" {{ $payment === 1 ? 'selected' : '' }}>コンビニ払い</option>
                        <option value="2" {{ $payment === 2 ? 'selected' : '' }}>カード支払い</option>
                    </select>
                </div>
                <div class="purchase__error">
                    @error('payment_method')
                        {{ $message }}
                    @enderror
                </div>
            </form>

            <!-- 配送先 -->
            <div class="shipping-address__select">
                <div class="shipping-address__titles">
                    <span class="shipping-address__select-title">配送先</span>
                    <a class="shipping-address__change-link" href="/purchase/address/{{$item->id}}">変更する</a>
                </div>

                <div class="shipping-address__display">
                    <p class="shipping-address__postal">〒 {{ $shipping['shipping_postal'] }}</p>
                    <p class="shipping-address__full">{{ $shipping['shipping_address'] . ' ' . $shipping['shipping_building'] }}</p>
                </div>
                <div class="purchase__error">
                @error('shipping_address')
                    {{ $message }}
                @enderror
                </div>
            </div>
        </section>

        <!-- 確認表示 -->
        <aside class="purchase-conf">
            <form class="purchase-conf__form" action="/purchase/{{$item->id}}" method="post">
                @csrf

                <input type="hidden" name="payment_method" value="{{ $payment ?: '' }}">

                <div class="purchase-confirm__definition-inner">
                    <dl class="purchase-confirm__definition">
                        <div class="purchase-confirm__definition-set">
                            <dt>商品代金</dt>
                            <dd><span>¥</span> {{ number_format($item->price) }}</dd>
                        </div>
                        <div class="purchase-confirm__definition-set">
                            <dt>支払方法</dt>
                            <dd>{{ ['1' => 'コンビニ払い', '2' => 'カード払い'][$payment] ?? '選択してください' }}</dd>
                        </div>
                    </dl>
                </div>

                <input type="hidden" name="shipping_postal" value="{{ $shipping['shipping_postal'] }}">
                <input type="hidden" name="shipping_address" value="{{  $shipping['shipping_address'] }}" />
                <input type="hidden" name="shipping_building" value="{{ $shipping['shipping_building'] }}" />

                <div class="purchase-form__button">
                    <button type="submit">購入する</button>
                </div>
            </form>
        </aside>
    </div>
@endsection