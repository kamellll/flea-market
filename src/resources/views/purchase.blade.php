@extends('layouts.search')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection
@section('js')
    <script src="{{ asset('/js/purchase.js') }}" defer></script>
@endsection
@section('content')
    <div class="content">
        <div class="content__left">
            <div class="detail">
                <div class="detail__img">
                    @php
                        $src = $product->img_url;
                        if (!empty($src) && !preg_match('#^https?://#', $src)) {
                            // http/https で始まっていない場合は storage のパスとして扱う
                            $src = asset('storage/' . ltrim($src, '/'));
                        }
                    @endphp
                    @if(!empty($src))
                        <img src="{{ $src }}" alt="商品名" class="product-card__image">
                    @endif
                </div>
                <div class="detail__content">
                    <h1 class="product-name">{{ $product->product_name }}</h1>
                    <span class="price">&yen;{{ number_format($product->price) }}</span>
                </div>
            </div>
            <div class="pay">
                <h2 class="pay__title">支払い方法</h2>
                <form action="" class="pay__form">
                    <select name="pay" id="pay">
                        <option value="">選択してください</option>
                        <option value="1">コンビニ払い</option>
                        <option value="2">カード支払い</option>
                    </select>
                </form>
            </div>
            <div class="address">
                <div class="address__title">
                    <h2 class="">配送先</h2>
                    <a href="/purchase/address/{{ $product->id }}">変更する</a>
                </div>
                <div class="address__content">
                    <p class="address__postal-code">&#12306; {{ $profile->postal_code }}</p>
                    <p>{{ $profile->address }} {{ $profile->building }}</p>
                </div>
            </div>
        </div>
        <div class="content__right">
            <div class="content__right--box">
                <div class="pay-box">
                    <span class="pay-box__title">商品代金</span>
                    <span class="pay-box__price">&yen;{{ number_format($product->price) }}</span>
                </div>
                <div class="pay-box">
                    <span class="pay-box__title">支払い方法</span>
                    <span class="pay-box__content">コンビニ払い</span>
                </div>
            </div>
            <form action="/product/checkout" method="POST" class="purchase-form">
                @csrf
                <input type="hidden" name="pay_method" value="" id="pay_method">
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="product_name" value="{{ $product->product_name }}">
                <input type="hidden" name="price" value="{{ $product->price }}">
                <button type="submit">
                    購入する
                </button>
            </form>
        </div>
    </div>
@endsection