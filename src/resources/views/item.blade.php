@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')

<div class="image-wrap">
    <div class="image-group">
        <img class="image-group__image" src="{{ asset('storage/images/'.$item->image) }}" alt="商品画像">
    </div>
</div>

<div class="detail-wrap">

    <div class="item-group">
        <h2 class="item-group__title">{{ $item ->item }}</h2>
        <span class="item-group__brand">{{ $item ->brand }}</span>
        <p class="item-group__price">
            ￥{{ number_format($item ->money) }}
            <span class="item-group__price-tax">(税込)</span>
        </p>

        <div class="item-unit">
            <div class="item-button">
                <div class="item-button__like">
                @if ($userLiked)
                    <form class="form-wrap" action="/item/unlike/{{ $item ->id }}" method="post">
                        @method('delete')
                        @csrf
                        <button class="item-icon__button" type="submit">
                            <img class="item-icon__image" src="{{ asset('img/star_red.svg') }}" alt="お気に入り">
                            <p class="likes-count likes-count--red">{{ $likesCount }}</p>
                        </button>
                    </form>
                @else
                    <form action="/item/like/{{ $item ->id }}" method="post">
                        @csrf
                        <button class="item-icon__button" type="submit">
                            <img class="item-icon__image" src="{{ asset('img/star.svg') }}" alt="お気に入り">
                            <p class="likes-count">{{ $likesCount }}</p>
                        </button>
                    </form>
                @endif
                </div>
                <div class="item-button__comment">
                    <button class="item-icon__button" onclick="location.href='/item/comment/{{$item ->id}}'">
                        <img class="item-icon__image"
                        src="{{ request()->is('item/comment/*') ? asset('img/comment_red.svg') : asset('img/comment.svg') }}"
                         alt="コメント">
                        <p class="comments-count">{{ $commentsCount }}</p>
                    </button>
                </div>
            </div>

            <div class="item-situation">
                @if ($item ->soldToUsers()->exists())
                    <div class="link-button link-button--disabled">売り切れ</div>
                @else
                    <a class="link-button" href="/purchase/{{ $item ->id }}">購入する</a>
                @endif
            </div>

            <div class="description-group">
                <h3 class="description-group__title">商品説明</h3>
                <p class="description-group__text">{{ $item ->detail }}
            </div>

            <div class="information-group">
                <h3 class="information-group__title">商品の情報</h3>
                <div class="information-content">
                    <span class="information-content__title">カテゴリー</span>
                    <div class="category-unit">
                    @if(! is_null($categories))
                        @foreach ($categories as $category)
                            <span class="information-content__category">{{ $category }}</span>
                        @endforeach
                    @endif
                    </div>
                </div>

                <div class="information-content">
                    <span class="information-content__title">商品の状態</span>
                    <span class="information-content__text">{{ $item ->status }}</span>
                </div>

                <div class="comment-group">
                    @if (Auth::check())
                    <div class="comment-content comment-content--right">
                        <div class="user-area user-area--right">
                            <img class="user-area__image" src="{{ asset('storage/profiles/'.$user->img_url) }}">
                            <span class="user-area__name">{{ $user->name }}</span>
                        </div>
                    </div>
                    @endif

                    <form class="form-group" action="/item/comment/{{ $item->id }}" method="post">
                        @csrf
                        <label class="form-group__label">商品へのコメント
                            <textarea class="form-group__textarea" name="comment"rows="5" required></textarea>
                        </label>
                        @error('comment')
                        <div class="form-wrap__error">{{ $message }}</div>
                        @enderror
                        <button class="submit-button" type="submit" onclick="return confirm('コメントを送信しますか？')">コメントを送信する</button>
                    </form>

                </div>

            </div>
        </div>

    </div>
@endsection
