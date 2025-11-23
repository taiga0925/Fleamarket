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
                {{-- 評価の星を表示 --}}
                <div class="user-rating">
                    @if(isset($averageRating) && $averageRating > 0)
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $averageRating)
                                <span class="star-on">★</span>
                            @else
                                <span class="star-off">★</span>
                            @endif
                        @endfor
                        <span class="rating-number">{{ $averageRating }}</span>
                    @else
                        <span class="rating-none">評価なし</span>
                    @endif
                </div>
            </div>
        </div>
        <a class="user-wrap__profile" href="/mypage/profile">プロフィールを編集</a>
    </div>

    <div class="tab-wrap">
        {{-- タブ1: 出品した商品 --}}
        <input id="tab1" type="radio" name="tab_btn" checked>
        <input id="tab2" type="radio" name="tab_btn">
        <input id="tab3" type="radio" name="tab_btn">

        <div class="tab-area">
            <label class="tab-label" for="tab1">出品した商品</label>
            <label class="tab-label" for="tab2">購入した商品</label>
            <label class="tab-label" for="tab3">
                取引中の商品
                @if($totalMessagesCount > 0)
                    <span class="tab-badge">{{ $totalMessagesCount }}</span>
                @endif
            </label>
        </div>

        <div class="panel-area">
            {{-- パネル1: 出品した商品 --}}
            <div id="panel1" class="tab-panel">
                <div class="item-list">
                    @foreach ($sellItems as $item)
                        <div class="item-box">
                            @if ($item->soldToUsers()->exists())
                                <div class="sold-out__mark">SOLD OUT</div>
                                <a href="{{ route('chat.index', ['item_id' => $item->id]) }}">
                                    <img class="item-image" src="{{ asset('storage/images/'.$item->image) }}">
                                    <p class="item-name">{{ $item->item }}</p>
                                </a>
                            @else
                                <a href="/item/{{ $item->id }}">
                                    <img class="item-image" src="{{ asset('storage/images/'.$item->image) }}">
                                    <p class="item-name">{{ $item->item }}</p>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- パネル2: 購入した商品 --}}
            <div id="panel2" class="tab-panel">
                <div class="item-list">
                    @foreach ($soldItems as $item)
                        <div class="item-box">
                            <div class="sold-out__mark">Sold</div>
                            <a href="{{ route('chat.index', ['item_id' => $item->id]) }}">
                                <img class="item-image" src="{{ asset('storage/images/'.$item->image) }}">
                                <p class="item-name">{{ $item->item }}</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- パネル3: 取引中の商品 --}}
            <div id="panel3" class="tab-panel">
                <div class="item-list">
                    @foreach ($dealingItems as $item)
                        <div class="item-box">
                            {{-- メッセージ数のバッジ（左上） --}}
                            @php
                                $msgCount = $item->chats()->count();
                            @endphp
                            @if($msgCount > 0)
                                <span class="item-badge">{{ $msgCount }}</span>
                            @endif

                            <a href="{{ route('chat.index', ['item_id' => $item->id]) }}">
                                <img class="item-image" src="{{ asset('storage/images/'.$item->image) }}">
                                <p class="item-name">{{ $item->item }}</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
