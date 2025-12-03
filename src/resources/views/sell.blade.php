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
        <form class="form" action="/mypage/profile/update" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form__group">
                <h2 class="form__content">商品画像</h2>
                <div class="image-upload">
                    {{-- 実際の file input（非表示） --}}
                    <input type="file" id="product_image" name="product_image" accept="image/*" class="image-upload__input">

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
                        @error('name')
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
                        <div class="error">{{ $message }}</div>
                    @enderror
                    @error('categories.*')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">商品の状態</span>
                </div>
                <div class="form__group-content">
                    <div class="form__condition">
                        <select name="" id="">
                            <option value="0">選択してください</option>
                            <option value="1">良好</option>
                            <option value="2">目立った傷や汚れなし</option>
                            <option value="3">やや傷や汚れあり</option>
                            <option value="4">状態が悪い</option>
                        </select>
                    </div>
                    <div class="form__error">
                        @error('name')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <p class="form__detail">商品と説明</p>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">郵便番号</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" />
                    </div>
                    <div class="form__error">
                        @error('postal_code')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">住所</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="address" value="{{ old('address') }}" />
                    </div>
                    <div class="form__error">
                        @error('address')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">建物名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="building" value="{{ old('building') }}" />
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit" type="submit">更新する</button>
            </div>
        </form>
    </div>
@endsection