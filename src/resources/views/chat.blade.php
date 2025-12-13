@extends('layouts.chat_app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="chat-page-wrapper">
    <div class="chat-container">
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <h3 class="sidebar-title">その他の取引</h3>
            </div>
            <ul class="dealing-list">
                @foreach($dealingItems as $dealingItem)
                    @if($dealingItem->id != $item->id)
                        <li class="dealing-item">
                            <a href="{{ route('chat.index', ['item_id' => $dealingItem->id]) }}" class="dealing-link">
                                <p class="dealing-item-name">{{ $dealingItem->item }}</p>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>

        <div class="chat-main">
            <div class="chat-header">
                <div class="chat-header-top">
                    <div class="chat-partner-info">
                        <img src="{{ $partner && $partner->img_url ? asset('storage/profiles/' . $partner->img_url) : asset('img/default_icon.svg') }}" alt="相手アイコン" class="chat-partner-icon">
                        <h2 class="chat-partner-name">{{ optional($partner)->name ?? '不明なユーザー' }} さんとの取引画面</h2>
                    </div>

                    @if($isBuyer)
                        {{-- 購入者のみボタンを表示 --}}
                        <button type="button" class="complete-button" onclick="openBuyerModal()">取引を完了する</button>
                    @endif
                    {{-- 出品者($canRate)の場合は、自動モーダルが出るのでボタンは表示しない --}}
                    {{-- ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲ --}}
                </div>

                <div class="chat-header-item">
                    <img src="{{ asset('storage/images/' . $item->image) }}" alt="商品画像" class="header-item-img">
                    <div class="header-item-info">
                        <p class="header-item-name">{{ $item->item }}</p>
                        <p class="header-item-price">¥{{ number_format($item->money) }}</p>
                    </div>
                </div>
            </div>

            <div class="chat-messages" id="chat-area">
                @foreach($chats as $chat)
                    @if($chat->sender_id == Auth::id())
                        <div class="message message-self">
                            <div class="message-user-info">
                                <span class="message-name">{{ Auth::user()->name }}</span>
                                <img src="{{ Auth::user()->img_url ? asset('storage/profiles/' . Auth::user()->img_url) : asset('img/default_icon.svg') }}" class="message-icon">
                            </div>
                            <div class="message-bubble">
                                @if($chat->message)
                                    <p class="message-text">{{ $chat->message }}</p>
                                @endif
                                @if($chat->image)
                                    <img src="{{ asset('storage/chat_images/' . $chat->image) }}" class="message-image">
                                @endif
                                <span class="message-time">{{ $chat->created_at->format('H:i') }}</span>
                            </div>
                            <div class="message-actions">
                                <a href="{{ route('chat.edit', ['item_id' => $item->id, 'chat_id' => $chat->id]) }}" class="action-link">編集</a>
                                <form action="{{ route('chat.destroy', ['item_id' => $item->id, 'chat_id' => $chat->id]) }}" method="POST" style="display:flex; align-items:center;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-button" onclick="return confirm('本当に削除しますか？')">削除</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="message message-partner">
                            <div class="message-user-info">
                                <img src="{{ $partner && $partner->img_url ? asset('storage/profiles/' . $partner->img_url) : asset('img/default_icon.svg') }}" class="message-icon">
                                <span class="message-name">{{ optional($partner)->name }}</span>
                            </div>
                            <div class="message-bubble">
                                @if($chat->message)
                                    <p class="message-text">{{ $chat->message }}</p>
                                @endif
                                @if($chat->image)
                                    <img src="{{ asset('storage/chat_images/' . $chat->image) }}" class="message-image">
                                @endif
                                <span class="message-time">{{ $chat->created_at->format('H:i') }}</span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="chat-footer">
                @if ($errors->any())
                    <div class="chat-errors">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="chat-input-form" action="{{ route('chat.store', ['item_id' => $item->id]) }}" method="POST" enctype="multipart/form-data" class="chat-input-form">
                    @csrf
                    <input type="text" name="message" id="chat-message-input" class="chat-input-text" placeholder="取引メッセージを送信" value="{{ old('message') }}">
                    <label class="chat-upload-label">
                        <input type="file" name="image" id="chat-image-input" style="display:none;" onchange="updateFileName()">
                        画像を追加する
                    </label>
                    <span id="file-name-display" class="file-name-display"></span>
                    <button type="submit" class="chat-send-button">
                        <img src="{{ asset('img/send_icon.jpg') }}" alt="送信" class="send-icon">
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ▼▼▼ ① 購入者用 モーダル (ID: buyer-rating-modal) ▼▼▼ --}}
<div id="buyer-rating-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <h2 class="modal-title">取引が完了しました</h2>

        <p class="modal-message">
            今回の取引相手はどうでしたか？
        </p>

        <div class="modal-divider"></div>

        <form action="{{ route('rating.store', ['item_id' => $item->id]) }}" method="POST" class="modal-form">
            @csrf

            <p class="modal-label">評価</p>

            <div class="modal-rating-section">
                <div class="stars">
                    <input type="radio" id="b-star5" name="rating" value="5" checked />
                    <label for="b-star5"><i class="fas fa-star"></i></label>
                    <input type="radio" id="b-star4" name="rating" value="4" />
                    <label for="b-star4"><i class="fas fa-star"></i></label>
                    <input type="radio" id="b-star3" name="rating" value="3" />
                    <label for="b-star3"><i class="fas fa-star"></i></label>
                    <input type="radio" id="b-star2" name="rating" value="2" />
                    <label for="b-star2"><i class="fas fa-star"></i></label>
                    <input type="radio" id="b-star1" name="rating" value="1" />
                    <label for="b-star1"><i class="fas fa-star"></i></label>
                </div>
            </div>

            <div class="modal-divider"></div>

            <input type="hidden" name="comment" value="評価のみ">

            <div class="modal-button-area">
                <button type="submit" class="modal-submit-button">送信する</button>
            </div>
        </form>

        <div class="modal-close-trigger" onclick="closeBuyerModal()">×</div>
    </div>
</div>

{{-- ▼▼▼ ② 出品者用 モーダル (ID: seller-rating-modal) ▼▼▼ --}}
<div id="seller-rating-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        {{-- 文言等は必要に応じて変更してください --}}
        <h2 class="modal-title">取引が完了しました</h2>

        <p class="modal-message">
            購入者の評価を行ってください。
        </p>

        <div class="modal-divider"></div>

        <form action="{{ route('rating.store', ['item_id' => $item->id]) }}" method="POST" class="modal-form">
            @csrf

            <p class="modal-label">評価</p>

            <div class="modal-rating-section">
                <div class="stars">
                    {{-- IDが重複しないように s- を付与 --}}
                    <input type="radio" id="s-star5" name="rating" value="5" checked />
                    <label for="s-star5"><i class="fas fa-star"></i></label>
                    <input type="radio" id="s-star4" name="rating" value="4" />
                    <label for="s-star4"><i class="fas fa-star"></i></label>
                    <input type="radio" id="s-star3" name="rating" value="3" />
                    <label for="s-star3"><i class="fas fa-star"></i></label>
                    <input type="radio" id="s-star2" name="rating" value="2" />
                    <label for="s-star2"><i class="fas fa-star"></i></label>
                    <input type="radio" id="s-star1" name="rating" value="1" />
                    <label for="s-star1"><i class="fas fa-star"></i></label>
                </div>
            </div>

            <div class="modal-divider"></div>

            <input type="hidden" name="comment" value="評価のみ">

            <div class="modal-button-area">
                <button type="submit" class="modal-submit-button">送信する</button>
            </div>
        </form>

        <div class="modal-close-trigger" onclick="closeSellerModal()">×</div>
    </div>
</div>
{{-- ▲▲▲▲▲▲▲▲▲▲▲▲▲ --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. スクロールを一番下へ
        var chatArea = document.getElementById('chat-area');
        if(chatArea) chatArea.scrollTop = chatArea.scrollHeight;

        // 2. 入力内容の保持 (LocalStorage)
        const messageInput = document.getElementById('chat-message-input');
        const chatForm = document.getElementById('chat-input-form');

        if (messageInput) {
            const storageKey = 'chat_draft_item_{{ $item->id }}';
            const savedMessage = localStorage.getItem(storageKey);
            if (savedMessage) messageInput.value = savedMessage;

            messageInput.addEventListener('input', function() {
                localStorage.setItem(storageKey, this.value);
            });

            if (chatForm) {
                chatForm.addEventListener('submit', function() {
                    localStorage.removeItem(storageKey);
                });
            }
        }

        // ▼▼▼ 追加: 出品者用モーダルの自動表示 ▼▼▼
        // 「出品者」かつ「評価可能（＝購入者が既に評価済み）」の場合、自動でモーダルを開く
        @if(!$isBuyer && isset($canRate) && $canRate)
            openSellerModal();
        @endif
        // ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲
    });

    function updateFileName() {
        const input = document.getElementById('chat-image-input');
        const display = document.getElementById('file-name-display');
        if (input.files && input.files.length > 0) {
            display.textContent = input.files[0].name;
            display.style.display = 'block';
        } else {
            display.textContent = '';
            display.style.display = 'none';
        }
    }

    // 購入者用モーダル
    function openBuyerModal() {
        document.getElementById('buyer-rating-modal').style.display = 'flex';
    }
    function closeBuyerModal() {
        document.getElementById('buyer-rating-modal').style.display = 'none';
    }

    // 出品者用モーダル
    function openSellerModal() {
        document.getElementById('seller-rating-modal').style.display = 'flex';
    }
    function closeSellerModal() {
        document.getElementById('seller-rating-modal').style.display = 'none';
    }
</script>
@endsection
