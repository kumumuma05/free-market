@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/order.css') }}">
@endsection

@section('content')
<div class="purchase">

    <!-- 商品情報 -->
    <div class="item__group">
        <img class="item__group-img" src="{{ $item->image_url }}" alt="商品画像">
        <p class="item__group-product-name">
            {{ $item->product_name}}
        </p>
        <p class="item__group-price">
            <span>¥</span>{{ number_format($item->price)}}
        </p>
    </div>

    <!-- 注文フォーム -->
    <form class="purchase__form" action="/purchase/{{$item->id}}" method="post">
        @csrf

        <!-- 支払方法 -->
        <div class="payment__select">
            <label class="payment__select-label" for="payment-select">支払い方法</label>
                <select name="payment_method" id="payment-select">
                <option value="" selected disabled>選択してください</option>
                <option value="1" {{ old('payment_method') == 1 ? 'selected' : '' }}>コンビニ払い</option>
                <option value="2" {{ old('payment_method') == 2 ? 'selected' : '' }}>カード支払い</option>
            </select>
            @error('payment_method')
                {{ $message }}
            @enderror
        </div>

        <!-- 配送先 -->
        <div class="shipping-address__select">
            <label class="shipping-address__select-label" for="shipping-address">配送先</label>
            <a class="shipping-address__change-link" href="/purchase/address/{{$item->id}}">変更する</a>
            <input type="text" name="shipping_postal" value="{{ old('shipping_postal', $user->postal ?? '') }}">
            <input class="shipping-address__input" type="text" name="shipping_address" value="{{ old('shipping_address', $user->address ?? '') }}" />
            <input class="shipping-address__input" type="text" name="shipping_building" value="{{ old('shipping_building', $user->building ?? '') }}" />
            @error('shipping_address')
                {{ $message }}
            @enderror
        </div>

        <!-- 確認表示 -->
        <div class="purchase-confirm__table-inner">
            <table class="purchase-confirm__table">
                <th class="purchase-confirm__table-header">
                    商品代金
                    <td class="purchase-confirm__table-data">
                        <span>¥</span>{{ number_format($item->price) }}
                    </td>
                </th>
                <th class="purchase-confirm__table-header">
                    支払方法
                    <td class="purchase-confirm__table-data">
                        <span id="confirm-payment">コンビニ払い</span>
                    </td>
                </th>
            </table>
        </div>

        <div class="purchase-form__button">
            <button type="submit">購入する</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        const select = document.getElementById('payment-select');
        const out = document.getElementById('confirm-payment');
        function syncPayment(){
            out.textContent = select.options[select.selectedIndex].text;
        }

        select.addEventListener('change', syncPayment);
        syncPayment();
    });
</script>
@endsection