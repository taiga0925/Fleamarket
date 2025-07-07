@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')

    <h2 class="main-title">プロフィール設定</h2>

    <form class="form-wrap h-adr" action="/mypage/profile/update" method="post" enctype="multipart/form-data">
    @csrf
        <div class="image-group">
            <img class="image-group__icon" id="preview-image" src="{{ asset('storage/profiles/'.$user->img_url) }}">
            <label class="image-group__label">
                <input class="image-group__input" type="file" name="file" id="image" onchange="previewFile()">画像を選択する</input>
            </label>
            <script>
                function previewFile() {
                    var preview = document.getElementById('preview-image');
                    var file    = document.querySelector('input[type=file]').files[0];
                    var reader  = new FileReader();

                    if (file) {
                        reader.onloadend = function () {
                            preview.src = reader.result;
                        }
                        reader.readAsDataURL(file);
                    }
                }
            </script>
        </div>

        <label class="form-wrap__label">ユーザー名
            <input class="form-wrap__input" type="text" name="name" value="{{ $user->name }}">
        </label>
        @error('name')
            <div class="form-wrap__error">{{ $message }}</div>
        @enderror

        <span class="p-country-name" style="display: none;">Japan</span>
        <label class="form-wrap__label">郵便番号
            <input class="form-wrap__input p-postal-code" type="text" size="8" maxlength="8" name="postcode" value="{{ $profile->postcode ?? '' }}">
        </label>
        @error('postal-code')
            <div class="form-wrap__error">{{ $message }}</div>
        @enderror

        <label class="form-wrap__label">住所
            <input class="form-wrap__input p-region p-locality p-street-address p-extended-address" type="text" name="address" value="{{ $profile->address ?? '' }}">
        </label>
        @error('address')
            <div class="form-wrap__error">{{ $message }}</div>
        @enderror

        <label class="form-wrap__label">建物名
            <input class="form-wrap__input" type="text" name="building" value="{{ $profile->building ?? '' }}">
        </label>

        <button class="form-wrap__button" type="submit" onclick="return confirm('プロフィールを更新しますか？')">更新する</button>
    </form>
@endsection
