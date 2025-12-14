<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class MyPageController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * マイページ画面
     * @return view ビュー
     */
    public function index()
    {
        $user = Auth::user();

        // 1. 出品した商品
        $sellItems = $user->items;

        // 2. 購入した商品
        $soldItems = $user->soldToItems ?? collect();

        // ▼▼▼ 修正: 3. 取引中の商品 (双方の評価が終わるまで表示) ▼▼▼

        // A. 自分が購入した商品 (Buyer Side)
        $boughtDealing = $soldItems->filter(function ($item) use ($user) {
            // 自分の評価があるか
            $hasMyRating = $item->ratings->where('rater_id', $user->id)->count();
            // 相手(出品者)の評価があるか
            $hasSellerRating = $item->ratings->where('rater_id', $item->user_id)->count();

            // 「両方」が評価済みでない限り、取引中として表示する
            return !($hasMyRating && $hasSellerRating);
        });

        // B. 自分が出品して売れた商品 (Seller Side)
        // 以前の whereDoesntHave ロジックを変更し、フィルタリングで統一します
        $mySoldItems = Item::where('user_id', $user->id)->has('soldToUsers')->get();

        $soldDealing = $mySoldItems->filter(function ($item) use ($user) {
            // 自分の評価
            $hasMyRating = $item->ratings->where('rater_id', $user->id)->count();
            // 相手(購入者)の評価 (自分以外の評価)
            $hasBuyerRating = $item->ratings->where('rater_id', '!=', $user->id)->count();

            // 「両方」が評価済みでない限り、取引中として表示する
            return !($hasMyRating && $hasBuyerRating);
        });

        // ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲

        // 結合して日付順（新しい順）にソート
        $dealingItems = $boughtDealing->merge($soldDealing)->sortByDesc(function ($item) {
            $lastChat = $item->chats()->latest()->first();
            return $lastChat ? $lastChat->created_at : $item->created_at;
        });

        // ▼▼▼ 修正: 未読メッセージ数のカウント ▼▼▼
        $totalMessagesCount = 0;
        foreach ($dealingItems as $item) {
            // 自分宛て(receiver_id = $user->id) かつ 未読(is_read = false) の数をカウント
            $unreadCount = $item->chats()
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();

            // ビューで使いやすいように、アイテムごとに未読数を一時的に保存
            $item->unread_count = $unreadCount;

            $totalMessagesCount += $unreadCount;
        }
        // ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲

        // 4. ユーザー評価の平均値（四捨五入）
        // receivedRatingsリレーションを使用
        $averageRating = $user->receivedRatings()->avg('rating');
        $averageRating = round($averageRating); // 四捨五入

        $data = [
            'user' => $user,
            'sellItems' => $sellItems,
            'soldItems' => $soldItems,
            'dealingItems' => $dealingItems,
            'totalMessagesCount' => $totalMessagesCount,
            'averageRating' => $averageRating,
        ];

        return view('mypage', $data);
    }


    /**
     * プロフィール編集画面
     * @return view ビュー
     */
    public function profile()
    {
        $user = Auth::user();
        $profile = null;

        if ($user->profile) {
            $profile = $user->profile;
        }

        return view('profile', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * プロフィール編集機能
     * @redirect redirect リダイレクト
    */
    public function update(Request $request)
    {
        $user = Auth::user();
        $userForm = $request->only('name');
        unset($request->all()['_token']);

        // プロフィール画像の変更があった場合
        if ($request->file != null) {

            $filename = $request->file->getClientOriginalName();
            $filename = $request->file->storeAs('public/profiles', $filename);

            $userForm['img_url'] = $request->file->getClientOriginalName();

        }


        $user->update($userForm);

        $profile = $user->profile;
        $profileForm = $request->only(['postcode', 'address', 'building']);

        if ($profile) {
            $profile->update($profileForm);
        } else {
            $user->profile()->create($profileForm);
        }

        return redirect('/');
    }
}
