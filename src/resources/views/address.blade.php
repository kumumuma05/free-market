@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="shipping-address">

    <!-- タイトル -->
    <h2 class="shipping-address__title">
        住所の変更
    </h2>

    <!-- アドレス入力フォーム -->
    <form class="shipping-address__form" action="" method="post">
        @csrf

        <!-- 郵便番号 -->
        <div class="shipping-address__group">
            <label class="shipping-address__group-label" for="postal">郵便番号</label>
            <input class="shipping-address__group-text" type="text" name="shipping_postal" id="postal" value="{{ old('shipping_postal') }}" />
        </div>

        <!-- 住所 -->
        <div class="shipping-address__group">
            <label class="shipping-address__group-label" for="address">住所</label>
            <input class="shipping-address__group-text" type="text" name="shipping_address" id="address" value="{{ old('shipping_address') }}" />
            
        </div>

        <!-- 建物名 -->
        <div class="shipping-address__group">
            <label class="shipping-address__group-label" for="building">建物名</label>
            <input class="shipping-address__group-text" type="text" name="shipping_building" id="building" value="{{ old('shipping_building') }}" />
            
        </div>

        <div class="shipping-address__button">
            <button type="submit">変更する</button>
        </div>
    </form>
</div>
@endsection