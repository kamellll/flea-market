@extends('layouts.search')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
    <div class="tab">
        <a href="/">
            <div class="tab__recommended  {{ request('tab') === 'mylist' ? '' : 'color-red' }}">おすすめ</div>
        </a>
        <form action="/" method="GET">
            <input type="hidden" name="tab" value="mylist">
            <input type="submit" value="マイリスト" class="tab__mylist {{ request('tab') === 'mylist' ? 'color-red' : '' }}" >
        </form>
    </div>
    <div>
        <div class="products">
            @foreach($products as $product)
                <div class="products__item {{ $product->purchase ? 'soldout' : '' }}">
                    <a href="/item/{{ $product->id }}" class="product-card">
                        <div class="product-card__image-wrapper">
                            <img src="{{ $product->img_url }}" alt="商品名" class="product-card__image">
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