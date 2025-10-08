@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/css.sellcss') }}">
@endsection

@section('content')
<div class="sell">
    <!-- タイトル -->
    <div class="sell__title">
        <h2>商品の出品</h2>
    </div>
    <!-- 画像 -->
    <form class="sell-form" action="">
        @csrf
        <div class="sell__image-group">
            <label for="image_path">商品画像</label>
            <div class="sell__image">
                <label class="sell__image-choice" for="image_path">画像を選択する</label>
                <input type="file" id="image_path"  name="image_path" accept="image/*" hidden/>
            </div>
        </div>
        <!-- 商品詳細 -->
        <div class="sell__detail">
            <div class="sell__detail-title">
                <h3>商品の詳細</h3>
            </div>
            <div class="sell__detail-category">
                @foreach($categories as $category)
                    <label class="category-select__label">
                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                        <span class="sell__category-button">{{ $category->name }}</span>
                    </label>
                @endforeach
            </div>

        </div>
    </form>
</div>


@endsection