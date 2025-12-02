<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Chat;
use App\Models\Sold_item;
use App\Http\Requests\ChatRequest;

class ChatController extends Controller
{
    /**
     * チャット画面
     * @return view ビュー
     */
    public function index($item_id)
    {
        $user = Auth::user();

        // 対象の商品を取得
        $item = Item::findOrFail($item_id);

        // アクセス権限のチェック（出品者か購入者でなければ403エラー）
        // 購入者を取得（sold_itemsテーブルから）
        $soldItem = Sold_item::where('item_id', $item_id)->first();

        if (!$soldItem) {
            abort(404, 'この商品はまだ購入されていません。');
        }

        $buyer_id = $soldItem->user_id;
        $seller_id = $item->user_id;

        if ($user->id !== $buyer_id && $user->id !== $seller_id) {
            abort(403, 'このチャットにアクセスする権限がありません。');
        }

        // ▼▼▼ 追加: 既読処理 ▼▼▼
        // 「この商品のチャット」かつ「受信者が自分」かつ「未読」のものを既読にする
        Chat::where('item_id', $item_id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        // ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲

        // チャット履歴を取得（古い順）
        $chats = Chat::where('item_id', $item_id)
            ->orderBy('created_at', 'asc')
            ->get();

        // 取引相手の情報を取得
        $partner = ($user->id === $buyer_id) ? $item->user : $soldItem->user;

        // サイドバー用：自分の取引中商品リストを取得
        // 1. 自分が購入した商品
        $boughtItems = Item::whereHas('soldToUsers', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        // 2. 自分が販売して売れた商品
        $soldItems = Item::where('user_id', $user->id)
            ->whereHas('soldToUsers')
            ->get();

        // 結合して新しい順にソート
        $dealingItems = $boughtItems->merge($soldItems)->sortByDesc(function ($item) {
            // 最新のチャット日時、なければ購入日時でソート
            $lastChat = $item->chats()->latest()->first();
            $soldInfo = Sold_item::where('item_id', $item->id)->first();
            return $lastChat ? $lastChat->created_at : ($soldInfo ? $soldInfo->created_at : $item->created_at);
        });

        // ▼▼▼ 追加: 自分が購入者かどうかを判定 ▼▼▼
        $isBuyer = ($user->id === $buyer_id);
        // ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲

        // compactに 'isBuyer' を追加
        return view('chat', compact('item', 'chats', 'partner', 'dealingItems', 'user', 'isBuyer'));
    }

    /**
     * チャット送信処理
     * @return redirect リダイレクト
     */
    public function store(ChatRequest $request, $item_id)
    {
        $user = Auth::user();

        // 対象の商品を取得
        $item = Item::findOrFail($item_id);

        // 取引情報を取得して相手を特定
        $soldItem = Sold_item::where('item_id', $item_id)->first();

        if (!$soldItem) {
            abort(404, '取引情報が見つかりません。');
        }

        // 送信者・受信者の判定
        $receiver_id = null;

        if ($user->id == $soldItem->user_id) {
            // 自分が「購入者」なら -> 相手は「出品者」
            $receiver_id = $item->user_id;
        } elseif ($user->id == $item->user_id) {
            // 自分が「出品者」なら -> 相手は「購入者」
            $receiver_id = $soldItem->user_id;
        }

        // 受信者が特定できない場合、処理を中断
        if (!$receiver_id) {
            abort(403, '取引相手が見つからないか、この取引の当事者ではありません。');
        }

        // 保存データの準備
        $chatData = [
            'sender_id'   => $user->id,
            'receiver_id' => $receiver_id,
            'item_id'     => $item->id,
            'message'     => $request->input('message'),
        ];

        // 画像アップロード処理
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/chat_images', $filename);
            $chatData['image'] = $filename;
        }

        Chat::create($chatData);

        // チャット画面にリダイレクト
        return redirect()->route('chat.index', ['item_id' => $item_id]);
    }

    /**
     * チャット削除処理
     * @return redirect リダイレクト
     */
    public function destroy($item_id, $chat_id)
    {
        $chat = Chat::findOrFail($chat_id);

        // 自分のメッセージ以外は削除できないようにチェック
        if ($chat->sender_id !== Auth::id()) {
            abort(403, '権限がありません。');
        }

        $chat->delete();

        return redirect()->route('chat.index', ['item_id' => $item_id]);
    }

    /**
     * チャット編集画面表示
     * @return view ビュー
     */
    public function edit($item_id, $chat_id)
    {
        $chat = Chat::findOrFail($chat_id);

        // 自分のメッセージ以外は編集できない
        if ($chat->sender_id !== Auth::id()) {
            abort(403, '権限がありません。');
        }

        return view('chat_edit', compact('chat', 'item_id'));
    }

    /**
     * チャット更新処理
     * @return redirect リダイレクト
     */
    public function update(Request $request, $item_id, $chat_id)
    {
        // バリデーション
        $request->validate([
            'message' => 'required|string|max:400',
        ]);

        $chat = Chat::findOrFail($chat_id);

        if ($chat->sender_id !== Auth::id()) {
            abort(403, '権限がありません。');
        }

        $chat->message = $request->input('message');
        $chat->save();

        return redirect()->route('chat.index', ['item_id' => $item_id]);
    }
}
