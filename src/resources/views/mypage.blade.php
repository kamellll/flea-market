@extends('layouts.search')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
    <div class="profile">
        <div class="profile__content">
            <div class="profile__avatar">
                @if(!empty($profile) && !empty($profile->avatar))
                    {{-- 画像あり：画像を丸く表示 --}}
                    <img src="storage/{{ $profile->avatar }}" alt="プロフィール画像">
                @else
                    {{-- 画像なし：グレーの円 --}}
                    <div></div>
                @endif
            </div>
            <div class="profile__name">
                {{ $user->name }}
            </div>
        </div>
        <div class="profile__edit">
            <a href="/mypage/profile">プロフィールを編集</a>
        </div>
    </div>
    <div class="tab">
        <a href="/mypage?page=sell">
            <div class="tab__recommended  {{ request('page') === 'buy' ? '' : 'color-red' }}">出品した商品</div>
        </a>
        <form action="/mypage" method="GET">
            <input type="hidden" name="page" value="buy">
            <input type="submit" value="購入した商品" class="tab__mylist {{ request('page') === 'buy' ? 'color-red' : '' }}">
        </form>
    </div>
    <div>
        <div class="products">
            @foreach($products as $product)
                <div class="products__item {{ $product->purchase ? 'soldout' : '' }}">
                    <a href="/item/{{ $product->id }}" class="product-card">
                        <div class="product-card__image-wrapper">
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
                        <div class="product-card__body">
                            <h2 class="product-card__name">{{ $product->product_name }}</h2>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection