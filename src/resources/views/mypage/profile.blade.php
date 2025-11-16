@extends('layouts.search')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection
@section('js')
    <script src="{{ asset('/js/profile.js') }}" defer></script>
@endsection
@section('content')
    <div class="login-form">
        <div class="login-form__heading">
            <h1>プロフィール設定</h1>
        </div>
        <form class="form" action="/mypage/profile/update" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form__group">
                <div class="avatar-upload">
                    <div class="avatar-preview" id="avatar-preview">
                        <div class="avatar-placeholder"></div>
                        <img id="avatar-preview-image" src="" alt="プロフィール画像">
                    </div>
                    <div class="avatar-actions">
                        <input type="file" id="avatar" name="avatar" class="avatar-input" accept="image/*">
                        <label for="avatar" class="avatar-button">
                            画像を選択
                        </label>
                        @error('avatar')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form__group-content">
                    <div class="form__error">
                        @error('name')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">ユーザー名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" />
                    </div>
                    <div class="form__error">
                        @error('name')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
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