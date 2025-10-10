@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell">
    <h2 class="sell__title">商品の出品</h2>

    <form class="sell-form" action="/sell" method="post" enctype="multipart/form-data">
        @csrf

        <!-- 画像 -->
        <section class="sell__section">
            <div class="sell__group">
                <label class="sell__group-label" for="image_path">商品画像</label>
                <div class="sell__image">
                    @if(session('temp_image'))
                        <img src="{{ Storage::u('storage/' . session('temp_image')) }}">
                    @endif
                    <label class="sell__image-button" for="image_path">画像を選択する
                    <input type="file" id="image_path"  name="image_path" accept="image/*" hidden />
                    </label>
                </div>
                @error('image_path')
                    <p class="sell__error">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <!-- 商品の詳細 -->
        <div class="sell__section">
            <h3 class="sell__section-title">商品の詳細</h3>
            <!-- カテゴリ -->
            <div class="sell__group">
                <label class="sell__group-label" for="category">カテゴリー</label>
                @foreach($categories as $category)
                    <label class="sell__category-item">
                        <input type="checkbox" name="category_ids[]" id="category" value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }} />
                        <span class="sell__detail-category-button">{{ $category->name }}</span>
                    </label>
                @endforeach
                @error('category_ids')
                    <p class="sell__error">{{ $message }}</p>
                @enderror
            </div>
            <!-- 商品の状態 -->
            <div class="sell__group">
                <label class="sell__group-label" for="condition">商品の状態</label>
                <select name="condition" id="condition">
                    <option value="1" {{ old('condition') == 1 ? 'selected' : '' }}>良好</option>
                    <option value="2" {{ old('condition') == 2 ? 'selected' : '' }}>目立った傷や汚れなし</option>
                    <option value="3" {{ old('condition') == 3 ? 'selected' : '' }}>やや傷や汚れあり</option>
                    <option value="4" {{ old('condition') == 4 ? 'selected' : '' }}>状態が悪い</option>
                </select>
                @error('condition')
                    <p class="sell__error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- 商品名と説明 -->
        <section class="sell__section">
            <h3 class="sell__section-title">商品名と説明</h3>
            <!-- 商品名 -->
            <div class="sell__group">
                <label class="sell__group-label" for="product_name">商品名</label>
                <input class="sell__group-input" id="product_name" type="text" name="product_name" value="{{ old('product_name') }}" />
                @error('product_name')
                    <p class="sell__error">{{ $message }}</p>
                @enderror
            </div>
            <!-- ブランド名 -->
            <div class="sell__group">
                <label class="sell__group-label" for="brand">ブランド名</label>
                <input class="sell__group-input" id="brand" type="text" name="brand" value="{{ old('brand') }}" />
            </div>
            <!-- 商品説明 -->
            <div class="sell__group">
                <label class="sell__group-label" for="description">商品の説明</label>
                <textarea class="sell__group-input" id="description" name="description">{{ old('description') }}</textarea>
                @error('description')
                    <p class="sell__error">{{ $message }}</p>
                @enderror
            </div>
            <!-- 販売価格 -->
            <div class="sell__group">
                <label class="sell__group-label" for="price">販売価格</label>
                <span class="price-input__symbol">¥</span>
                <input class="sell__group-input" id="price" type="text" name="price" value="{{ old('price') }}" />
                @error('price')
                    <p class="sell__error">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <button class="sell__form-button" type="submit">出品する</button>
    </form>
</div>
@endsection