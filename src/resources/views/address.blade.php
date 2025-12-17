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
            <h1>住所の変更</h1>
        </div>
        <form class="form" action="/purchase/address/update" method="post">
            @csrf
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
                <input type="hidden" name="product_id" value="{{ $product_id }}">
                <button class="form__button-submit" type="submit">更新する</button>
            </div>
        </form>
    </div>
@endsection