@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
    <div class="tab-wrap">
        <label class="tab-wrap__label recommendation__label">
            <input class="tab-wrap__input" type="radio" name="tab" checked>おすすめ
        </label>
        <div class="tab-wrap__group">
            @foreach ($items as $item)
                <div class="tab-wrap__content">
                    @if ($item->soldToUsers()->exists())
                        <div class="sold-out__mark">Sold</div>
                    @endif
                    <a class="tab-wrap__content-link" href="/item/{{ $item->id }}">
                        <img class="tab-wrap__content-image" src="{{ asset('storage/images/'.$item->image) }}">
                        <a href="/item/{{ $item->id }}">{{ $item ->item }}</a>
                    </a>
                </div>
            @endforeach

        </div>

        <label class="tab-wrap__label">
            <input class="tab-wrap__input" type="radio" name="tab">マイリスト
        </label>
        @if (Auth::check())
            <div class="tab-wrap__group">
                @forelse ($likeItems as $likeItem)
                    <div class="tab-wrap__content">
                        @if ($likeItem->soldToUsers()->exists())
                            <div class="sold-out__mark">sold</div>
                        @endif
                        <a class="tab-wrap__content-link" href="/item/{{ $likeItem->id }}">
                            <img class="tab-wrap__content-image" src="{{ asset('storage/images/'.$likeItem->image) }}">
                            <a href="/item/{{ $likeItem->id }}">{{ $likeItem ->item }}</a>
                        </a>
                    </div>
                @empty
                    <p class="no-message">マイリストはありません</p>
                @endforelse

            </div>
        @else
            <div class="tab-wrap__group-link">
                <a class="link-button" href="/register">会員登録</a>
                <span class="tab-wrap__group-text">及び</span>
                <a class="link-button" href="/login">ログイン</a>
                <span class="tab-warp__group-text">が必要です。</span>
            </div>
        @endif
    </div>
@endsection
