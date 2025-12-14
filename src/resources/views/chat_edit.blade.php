@extends('layouts.app')

@section('css')
<style>
    .edit-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
    }
    .edit-title { margin-bottom: 20px; font-size: 20px; }
    .edit-textarea {
        width: 100%; height: 150px;
        padding: 10px; border: 1px solid #ccc; border-radius: 4px;
        font-size: 16px; resize: none; margin-bottom: 20px;
    }
    .edit-actions { display: flex; justify-content: center; gap: 20px; }
    .btn-update {
        background: #ff3333; color: #fff; padding: 10px 30px;
        border: none; border-radius: 4px; cursor: pointer; font-weight: bold;
    }
    .btn-back {
        background: #999; color: #fff; padding: 10px 30px;
        border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="edit-container">
    <h2 class="edit-title">メッセージの編集</h2>
    <form action="{{ route('chat.update', ['item_id' => $item_id, 'chat_id' => $chat->id]) }}" method="POST">
        @csrf
        @method('PATCH')

        <textarea name="message" class="edit-textarea">{{ old('message', $chat->message) }}</textarea>
        @error('message')
            <p style="color:red; text-align:left;">{{ $message }}</p>
        @enderror

        <div class="edit-actions">
            <a href="{{ route('chat.index', ['item_id' => $item_id]) }}" class="btn-back">戻る</a>
            <button type="submit" class="btn-update">更新する</button>
        </div>
    </form>
</div>
@endsection
