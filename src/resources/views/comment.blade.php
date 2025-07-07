@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/comment.css') }}">
@endsection

@section('content')
    <div class="item-group">
        <h2 class="item-group__title">{{ $item ->item }}</h2>

        <div class="item-group__comments">

            <div>
                <h2 class="item-group__comments-title">
                    コメント一覧
                </h2>
            </div>

            <div class="comments">
                @if(! is_null($comments))
                @foreach($comments as $comment)
                <div class="user-list">
                    <img class="user-list__image" src="{{ $user->image }}">
                    <span class="user-list__name">{{ $user->name }}</span>
                </div>
                <div class="comment-list">
                    <span class="user-comment">{{ $comment->comment }}</span>
                </div>
                @endforeach
                @else
                    <span class="no-comment"> コメントはありません</span>
                @endif
            </div>
        </div>
    </div>

    <a class="item-redirect" href="/item/{{ $item ->id }}">商品へ戻る</a>
@endsection
