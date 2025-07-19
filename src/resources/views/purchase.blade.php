@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')

    <div class="section-wrap">
        <div class="section-group">
            <div class="image-content">
                <img class="image-content__image" src="{{ asset('storage/images/'.$item->image) }}" alt="商品画像">
            </div>
            <div class="item-content">
                <h2 class="item-content__title">{{ $item->item }}</h2>
                <p class="item-content__price">￥{{ number_format($item->money) }}</p>
            </div>
        </div>
        <div class="payment-group">
            <div class="payment-content">
                <h3 class="header-content__title">支払方法</h3>
                <select class="select-method" name="payment-list" id="method" onchange="updateContent()">
                    <option value="">選択してください</option>
                    <option value="コンビニ支払い">コンビニ払い</option>
                    <option value="クレジットカード支払い">クレジットカード支払い</option>
                </select>
            </div>
        </div>
        <div class="address-group">
            <div class="header-content">
                <h3 class="header-content__title">配送先</h3>
                <a class="link-button" href="/purchase/address/{{ $item->id }}">変更する</a>
            </div>
            <div class="address-content">
                <p class="address-content__text">〒{{ substr($profile->postcode, 0, 3). '-' . substr($profile->postcode, 3) }}</p>
                <p class="address-content__text">{{ $profile->address }} <span>{{ $profile->building }}</span></p>
            </div>

        </div>
    </div>

    <form class="confirm-wrap" action="/purchase/decide/{{ $item->id }}" method="post">
        @csrf
        <div class="confirm-group">
            <div class="confirm-content confirm-content__price">
                <p class="confirm-content__title">商品代金</p>
                <p class="confirm-content__text">￥{{ number_format($item->money) }}</p>
            </div>
            <div class="confirm-content confirm-content__payment">
                <p class="confirm-content__title">支払方法</p>
                <input class="confirm-content__input" type="text" name="method" id="result" value="コンビニ払い" readonly>
            </div>
        </div>
        <button class="submit-button" type="submit" onclick="return confirm('購入しますか？')">購入する</button>
    </form>

    <script>
        const selectElement = document.getElementById("method");
        const inputElement = document.getElementById("result");

        selectElement.addEventListener("change", function() {
            inputElement.value = selectElement.value;
        });
    </script>


@endsection
