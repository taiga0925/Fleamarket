@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
    <div class="user-wrap">
        <div class="user-group">
            <img class="user-group__icon" src="{{ asset('storage/profiles/'.$user->img_url) }}">
            <div class="user-unit">
                <p class="user-unit__name">{{ $user->name }}</p>
            </div>
        </div>
        <a class="user-wrap__profile" href="/mypage/profile">プロフィールを編集</a>
    </div>

    <div class="tab-wrap">
        <label class="tab-wrap__label">
            <input class="tab-wrap__input" type="radio" name="tab" value="sell_items" checked>出品した商品
        </label>
        <div class="tab-wrap__group">
            @foreach ($sellItems as $item)
                <div class="tab-wrap__content">
                    @if ($item->soldToUsers()->exists())
                        <div class="sold-out__mark">SOLD OUT</div>
                    @endif
                    <a class="tab-wrap__content-link" href="/item/{{ $item->id }}">
                        <img class="tab-wrap__content-image" src="{{ asset('storage/images/'.$item->image) }}">
                        <a href="/item/{{ $item->id }}">{{ $item ->item }}</a>
                    </a>
                </div>
            @endforeach
        </div>

        <label class="tab-wrap__label">
            <input class="tab-wrap__input" type="radio" value="bought_items" name="tab">購入した商品
        </label>
        <div class="tab-wrap__group">
            @foreach ($soldItems as $item)
                <div class="tab-wrap__content">
                    <div class="sold-out__mark">Sold</div>
                    <a class="tab-wrap__content-link" href="/item/{{ $item->id }}">
                        <img class="tab-wrap__content-image" src="{{ asset('storage/images/'.$item->image) }}">
                        <a href="/item/{{ $item->id }}">{{ $item ->item }}</a>
                    </a>
                </div>
            @endforeach
         </div>
    </div>
@endsection
