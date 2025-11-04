@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
    <div class="sell">

        <!-- タイトル -->
        <h2 class="sell__title">商品の出品</h2>

        <!-- 画像 -->
        <section class="sell__section">
            <div class="sell__group">
                <form method="get" action="/sell">
                    <label class="sell__group-label">商品画像</label>
                    <div class="sell__image {{ session('temp_image') ? 'sell__image--has-image' : '' }}">
                        <label class="sell__image-button" for="image_path">画像を選択する</label>
                        @if(session('temp_image'))
                            <img src="{{ Storage::url(session('temp_image')) }}" alt="プレビュー">
                        @endif
                    </div>
                </form>

                <form class="sell-form" action="/sell/session" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="file" id="image_path"  name="image_path" accept="image/*" hidden  onchange="this.form.submit()" />
                </form>
                @error('image_path')
                    <p class="sell__error">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <!-- 商品の詳細 -->
        <section class="sell__section">
            <h3 class="sell__section-title">商品の詳細</h3>

            <form class="sell-form" action="/sell" method="post" enctype="multipart/form-data">
            @csrf
                <!-- カテゴリ選択 -->
                <div class="sell__group">
                    <label class="sell__group-label" for="category">カテゴリー</label>
                    @foreach($categories as $category)
                        <input class="sell__category-input" type="checkbox" name="category_ids[]" id="category-{{ $category->id }}" value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }} />
                        <label class="sell__category-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
                    @endforeach
                    @error('category_ids')
                        <p class="sell__error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 商品の状態 -->
                <div class="sell__group">
                    <label class="sell__group-label" for="condition">商品の状態</label>
                    <div class="sell__group-select-wrap">
                        <select class="sell__group-condition-select" name="condition" id="condition">
                            <option value="" disabled selected hidden>選択してください</option>
                            <option value="1" {{ old('condition') == 1 ? 'selected' : '' }}>良好</option>
                            <option value="2" {{ old('condition') == 2 ? 'selected' : '' }}>目立った傷や汚れなし</option>
                            <option value="3" {{ old('condition') == 3 ? 'selected' : '' }}>やや傷や汚れあり</option>
                            <option value="4" {{ old('condition') == 4 ? 'selected' : '' }}>状態が悪い</option>
                        </select>
                    </div>
                    @error('condition')
                        <p class="sell__error">{{ $message }}</p>
                    @enderror
                </div>
            </section>

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
                    <textarea class="sell__group-textarea" id="description" name="description">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="sell__error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 販売価格 -->
                <div class="sell__group sell__group-price">
                    <label class="sell__group-label" for="price">販売価格</label>
                    <div class="sell__price">
                        <span class="price__symbol">¥</span>
                        <input class="sell__group-input sell__group-input-price" id="price" type="text" name="price" value="{{ old('price') }}" />
                    </div>

                    @error('price')
                        <p class="sell__error">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <button class="sell__form-button" type="submit">出品する</button>
        </form>
    </div>
@endsection