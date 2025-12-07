@extends('layouts.search')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection
@section('js')
    <script src="{{ asset('/js/sell.js') }}" defer></script>
@endsection
@section('content')
    <div class="sell">
        <div class="sell__heading">
            <h1>商品の出品</h1>
        </div>
        <form class="form" action="/exhibiting" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form__group">
                <h2 class="form__content">商品画像</h2>
                <div class="image-upload">
                    {{-- 実際の file input（非表示） --}}
                    <input type="file" id="product_image" name="img_url" accept="image/*" class="image-upload__input">

                    {{-- 見た目用の枠＆ボタン／画像 --}}
                    <label for="product_image" class="image-upload__label">
                        {{-- 画像未選択時に表示される「画像を選択する」ボタン --}}
                        <span class="image-upload__button">
                            画像を選択する
                        </span>

                        {{-- 画像選択後に表示されるプレビュー画像 --}}
                        <img src="" alt="選択された画像のプレビュー" class="image-upload__preview" id="image_preview">
                    </label>
                </div>
                <div class="form__group-content">
                    <div class="form__error">
                        @error('img_url')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <p class="form__detail">商品の詳細</p>
            <div class="form__group">
                <div class="category-list">
                    <h2 class="form__content">カテゴリー</h2>
                    <div class="category-list__grid">
                        @foreach($categories as $category)
                            @php
    $checked = in_array(
        $category->id,
        old('categories', [])
    );
                            @endphp

                            <label class="category-item">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}" @if($checked) checked
                                @endif>
                                <span class="category-item__label">{{ $category->content }}</span>
                            </label>
                        @endforeach
                    </div>

                    @error('categories')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                    @error('categories.*')
                        <div class="form__error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <h2 class="form__content">商品の状態</h2>
                <div class="form__group-content">
                    <div class="form__condition">
                        <select name="condition" id="" class="form__select" name="condition">
                            <option value="">選択してください</option>
                            <option value="1" {{ old('condition') == '1' ? 'selected' : '' }}>良好</option>
                            <option value="2" {{ old('condition') == '2' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                            <option value="3" {{ old('condition') == '3' ? 'selected' : '' }}>やや傷や汚れあり</option>
                            <option value="4" {{ old('condition') == '4' ? 'selected' : '' }}>状態が悪い</option>
                        </select>
                    </div>
                    <div class="form__error">
                        @error('condition')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <p class="form__detail">商品と説明</p>
            <div class="form__group">
                <h2 class="form__content">商品名</h2>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="product_name" value="{{ old('product_name') }}" />
                    </div>
                    <div class="form__error">
                        @error('product_name')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <h2 class="form__content">ブランド名</h2>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="brand_name" value="{{ old('brand_name') }}" />
                    </div>
                    <div class="form__error">
                        @error('brand_name')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <h2 class="form__content">商品の説明</h2>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <textarea type="text" class="explanation" name="explanation">{{ old('explanation') }}</textarea>
                    </div>
                    <div class="form__error">
                        @error('explanation')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <h2 class="form__content">販売価格</h2>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <div class="price-input">
                            <input type="number" name="price" id="price" value="{{ old('price') }}">
                        </div>
                    </div>
                    <div class="form__error">
                        @error('price')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit" type="submit">出品する</button>
            </div>
        </form>
    </div>
@endsection