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
                        @if(!empty($profile) && !empty($profile->avatar))
                            {{-- 画像あり：プレースホルダーは隠す、画像は表示 --}}
                            <div class="avatar-placeholder" style="display:none;"></div>
                            <img id="avatar-preview-image" src="{{ asset('storage/' . $profile->avatar) }}" alt="プロフィール画像"
                                style="display:block;">
                        @else
                            {{-- 画像なし：プレースホルダー表示、画像は非表示 --}}
                            <div class="avatar-placeholder" style="display:block;"></div>
                            <img id="avatar-preview-image" src="" alt="プロフィール画像" style="display:none;">
                        @endif
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
                        <input type="text" name="postal_code"
                            value="{{ old('postal_code', $profile->postal_code ?? '') }}" />
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
                        <input type="text" name="address" value="{{ old('address', $profile->address ?? '') }}" />
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
                        <input type="text" name="building" value="{{ old('building', $profile->building ?? '') }}" />
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit" type="submit">更新する</button>
            </div>
        </form>
    </div>
@endsection