@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="message-success" id="message">
            {{ session('success') }}
        </div>
        <script>
            $(document).ready(function(){
                $("#message").fadeIn(1000).delay(3000).fadeOut(1000);
            });
        </script>
    @endif

    <h2 class="main-title">商品の出品</h2>
    <form class="form-wrap" action="{{ isset($item_id) ? '/sell/' . $item_id : '/sell' }}" method="post" enctype="multipart/form-data">
        @csrf
        <span class="form-wrap__label">商品画像
            @if($item)
                <a class="image-link" href="{{ $item->image}}">
                    <img class="preview-image" id="preview-image" src="{{ $item->image }}">
                </a>
            @else
                <img class="preview-image" id="preview-image" style="display: none">
            @endif
            <div class="image-group">
                <label class="image-group__label">
                    <input class="image-group__input" type="file" id="image" name="file" onchange="previewFile()">画像を選択する
                </label>
            </div>
        </span>
        @error('image')
            <div class="form-wrap__error">{{ $message }}</div>
        @enderror

        <h3 class="form-wrap__title">商品の詳細</h3>
        <label class="form-wrap__label">カテゴリー
            <select class="form-wrap__select" name="category_id">
                @foreach ($selectCategories as $category)
                    <option value="{{ $category['id'] }}" {{ $category['selected'] ? 'selected' : '' }}>{{ $category['category'] }}</option>
                @endforeach
            </select>
        </label>
        @error('category_id')
            <div class="form-wrap__error">{{ $message }}</div>
        @enderror

        <label class="form-wrap__label">商品の状態
            <select class="form-wrap__select" name="status">
                <option value="良好">良好</option>
                <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                <option value="状態が悪い">状態が悪い</option>
            </select>
        </label>
        @error('status')
            <div class="form-wrap__error">{{ $message }}</div>
        @enderror

        <h3 class="form-wrap__title">商品名と説明</h3>
        <label class="form-wrap__label">商品名
            <input class="form-wrap__input" type="text" name="item" value="{{ $item->item ?? '' }}">
        </label>
        @error('item')
            <div class="form-wrap__error">{{ $message }}</div>
        @enderror

        <label class="form-wrap__label">ブランド名
            <input class="form-wrap__input" type="text" name="brand" value="{{ $item->brand ?? '' }}">
        </label>
        @error('brand')
            <div class="form-wrap__error">{{ $message }}</div>
        @enderror

        <label class="form-wrap__label">商品の説明
            <textarea class="form-wrap__textarea" name="detail" cols="30" rows="5">{{ $item->detail ?? '' }}</textarea>
        </label>
        @error('detail')
            <div class="form-wrap__error">{{ $message }}</div>
        @enderror

        <h3 class="form-wrap__title">販売価格</h3>
        <label class="form-wrap__label">販売価格
            <div class="input-wrap">
                <input class="form-wrap__input input-money" type="text" id="money" name="money"  value="{{ $item->money ?? '' }}" pattern="^[1-9][0-9]*$">
            </div>
        </label>
        @error('money')
            <div class="form-wrap__error">{{ $message }}</div>
        @enderror

        <input type="hidden" value="{{ Auth::id() }}" name="user_id">
        <button class="form-wrap__button" type="submit" onclick="return confirm('出品しますか？')">{{ $item ? '修正する' : '出品する' }}</button>
    </form>

    <script>
        function previewFile() {
            var preview = document.getElementById('preview-image');
            var file    = document.querySelector('input[type=file]').files[0];
            var reader  = new FileReader();

            if (file) {
                reader.onloadend = function () {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('price');

            const formatToCommaSeparated = (value) => {
                return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            };

            if (amountInput.value) {
                amountInput.value = formatToCommaSeparated(amountInput.value);
            }

            amountInput.addEventListener('focus', function(e) {
                let value = e.target.value;
                e.target.value = value.replace(/,/g, '');
            });

            amountInput.addEventListener('blur', function(e) {
                let value = e.target.value;

                value = value.replace(/[０-９]/g, function(s) {
                    return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
                }).replace(/[^0-9]/g, '');

                e.target.value = formatToCommaSeparated(value);
            });
        });
    </script>
@endsection
