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

        // ▼▼▼ 修正: サイドバー用 取引中リストの取得ロジック統一 ▼▼▼

        // A. 自分が購入した商品
        $soldItems = $user->soldToItems ?? collect();
        $boughtDealing = $soldItems->filter(function ($item) use ($user) {
            $hasMyRating = $item->ratings->where('rater_id', $user->id)->count();
            $hasSellerRating = $item->ratings->where('rater_id', $item->user_id)->count();
            return !($hasMyRating && $hasSellerRating);
        });

        // B. 自分が出品した商品
        $mySoldItems = Item::where('user_id', $user->id)->has('soldToUsers')->get();
        $soldDealing = $mySoldItems->filter(function ($item) use ($user) {
            $hasMyRating = $item->ratings->where('rater_id', $user->id)->count();
            $hasBuyerRating = $item->ratings->where('rater_id', '!=', $user->id)->count();
            return !($hasMyRating && $hasBuyerRating);
        });

        // ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲

        // 結合して新しい順にソート
        $dealingItems = $boughtDealing->merge($soldDealing)->sortByDesc(function ($item) {
            $lastChat = $item->chats()->latest()->first();
            return $lastChat ? $lastChat->created_at : $item->created_at;
        });
        
        // 自分が購入者かどうか
        $isBuyer = ($user->id === $buyer_id);

        // ▼▼▼ 追加: 評価が可能かどうかを判定 ▼▼▼
        // 条件: 自分がまだ評価していないこと
        $myRating = \App\Models\Rating::where('item_id', $item_id)
            ->where('rater_id', $user->id)
            ->exists();

        // 相手が評価済みかどうか (出品者の場合、購入者が評価済みでないと評価できないようにする場合)
        $partnerRating = \App\Models\Rating::where('item_id', $item_id)
            ->where('rater_id', '!=', $user->id)
            ->exists();

        // ボタンを表示するかどうか
        // 購入者: 常に表示可能（自分が未評価なら）
        // 出品者: 購入者が評価済み かつ 自分が未評価なら表示可能
        $canRate = false;
        if ($isBuyer) {
            if (!$myRating) $canRate = true;
        } else {
            // 出品者の場合、相手(購入者)の評価が終わっている必要がある
            if ($partnerRating && !$myRating) $canRate = true;
        }
        // ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲

        return view('chat', compact('item', 'chats', 'partner', 'dealingItems', 'user', 'isBuyer', 'canRate'));
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
