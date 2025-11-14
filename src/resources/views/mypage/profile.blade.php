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
        <form class="form" action="/register" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form__group">
                <div class="avatar-upload">
                    {{-- 左側：画像 or グレーの丸 --}}
                    <div class="avatar-preview" id="avatar-preview">
                        {{-- プレースホルダー（グレーの丸） --}}
                        <div class="avatar-placeholder"></div>

                        {{-- 画像プレビュー用 --}}
                        <img id="avatar-preview-image" src="" alt="プロフィール画像">
                    </div>

                    {{-- 右側：ボタン＋ファイル名表示 --}}
                    <div class="avatar-actions">
                        {{-- 本物の input は隠しておく --}}
                        <input type="file" id="avatar" name="avatar" class="avatar-input" accept="image/*">

                        <label for="avatar" class="avatar-button">
                            画像を選択
                        </label>

                        <span id="avatar-file-name" class="avatar-file-name">
                            選択されていません
                        </span>

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
                        <input type="text" name="name" value="{{ old('name') }}" />
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
                        <input type="text" name="email" value="{{ old('email') }}" />
                    </div>
                    <div class="form__error">
                        @error('email')
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
                        <input type="password" name="password" />
                    </div>
                    <div class="form__error">
                        @error('password')
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
                        <input type="password" name="password_confirmation" />
                    </div>
                    <div class="form__error">
                        @error('password_confirmation')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit" type="submit">更新する</button>
            </div>
        </form>
    </div>
@endsection