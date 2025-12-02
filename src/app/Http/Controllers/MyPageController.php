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

        // 3. 取引中の商品（評価が完了していないもの）
        // ▼▼▼ 修正: 評価(rating)が存在しない商品のみを取得 ▼▼▼

        // 自分が購入した商品の中で、評価していないもの
        // (soldToUsersリレーション経由で取得したItemコレクションからフィルタリング)
        $boughtDealing = $soldItems->filter(function ($item) {
            return $item->rating === null;
        });

        // 自分が出品して売れた商品のうち、評価されていないもの
        // (doesntHave('rating') で評価がない商品をDB検索)
        $soldDealing = Item::where('user_id', $user->id)
            ->has('soldToUsers')    // 売れている
            ->doesntHave('rating')  // まだ評価されていない（取引完了していない）
            ->get();

        // ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲

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
