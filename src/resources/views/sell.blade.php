@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell">
    <h2 class="sell__title">商品の出品</h2>

    <form class="sell-form" action="/sell/store" method="post" enctype="multipart/form-data">
        @csrf

        <!-- 画像 -->
        <div class="sell__group">
            <label class="sell__group-label" for="image_path">商品画像</label>
            <div class="sell__image">
                <label class="sell__image-button" for="image_path">画像を選択する</label>
                <input type="file" id="image_path"  name="image_path" accept="image/*" hidden/>
            </div>
            @error('image_path')
                <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        <!-- 商品詳細 -->
        <div class="sell__section">
            <h3 class="sell__section-title">商品の詳細</h3>

            <div class="sell__group">
                <label class="sell__group-label">カテゴリー</label>
                @foreach($categories as $category)
                    <label class="sell__category-item">
                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                        <span class="sell__detail-category-button">{{ $category->name }}</span>
                    </label>
                @endforeach
            </div>
            
            <div class="sell__detail-condition">
                <label class="sell__detail-category-label">商品の状態</label>
                <select name="condition" id="">
                    <option value="1">良好</option>
                    <option value="2">目立った傷や汚れなし</option>
                    <option value="3">やや傷や汚れあり</option>
                    <option value="4">状態が悪い</option>
                </select>

            </div>
                

                </div>

        </div>
    </form>
</div>


@endsection