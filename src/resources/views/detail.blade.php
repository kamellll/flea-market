@extends('layouts.search')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
    <div class="detail">
        <div class="detail__img">
            <img src="{{ $product->img_url }}" alt="">
        </div>
        <div class="detail__content">
            <h1 class="product-name">{{ $product->product_name }}</h1>
            <p class="brand-name">{{ $product->brand_name }}</p>
            <p class="price">&yen;<span class="price__num">{{ number_format($product->price) }}</span>（税込み）</p>
            <div class="icon">
                <div class="good">
                    <form action="/good" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="good__button">
                            @if($isLikedByMe)
                                <img src="/images/good_pink.png" alt="">
                            @else
                                <img src="/images/good_default.png" alt="">
                            @endif
                        </button>
                    </form>
                    <p>{{ count($goods) }}</p>
                </div>
                <div class="hukidasi">
                    <img src="/images/hukidasi.png" alt="">
                    <p>{{ count($comments) }}</p>
                </div>
            </div>
            <div class="detail__purchase">
                <a href="/purchase/{{ $product->id }}">購入手続きへ</a>
            </div>
            <h2>商品説明</h2>
            <div class="detail__explanation">
                {{ $product->explanation }}
            </div>
            <h2>商品の情報</h2>
            <div class="detail__inf">
                <div>カテゴリー</div>
                <div class="detail__category">
                    @foreach ($product->categories as $category)
                        <span class="product-category-tag">
                            {{ $category->content }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="detail__inf">
                <div>商品の状態</div>
                <div class="detail__category">
                    {{ $conditionPrefix[$product->condition] ?? '' }}
                </div>
            </div>
            <h2>コメント({{ count($comments) }})</h2>
            <div class="detail__comment">
                @foreach ($comments as $comment)
                    <div class="comment">
                        <div class="comment__user">
                            <div class="avatar-preview" id="avatar-preview">
                                @if(isset($comment->avatar))
                                    <img id="avatar-preview-image" src="{{ $comment->avatar }}" alt="">
                                @else
                                    <div class="avatar-placeholder"></div>
                                @endif
                                <p class="comment__name">{{ $comment->user->name }}</p>
                            </div>
                        </div>
                        <div class="comment__content">{{ $comment->comment }}</div>
                    </div>
                @endforeach
            </div>
            <h3>商品へのコメント</h3>
            @error('comment')
                <div class="error">{{ $message }}</div>
            @enderror
            <form action="/comment" method="POST" class="new-comment">
                @csrf
                <textarea name="comment" id="" class="new-commnet__text"></textarea>
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="submit" class="new-commnet__button" value="コメントを送信する">
            </form>
        </div>
    </div>
@endsection