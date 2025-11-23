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

        // 3. 取引中の商品（出品して売れたもの + 購入したもの）
        // ※ 厳密な「取引中」の定義が必要ですが、今回は「売買が成立した商品すべて」とします
        // 自分が購入した商品
        $boughtDealing = $soldItems;
        // 自分が出品して売れた商品
        $soldDealing = Item::where('user_id', $user->id)->has('soldToUsers')->get();

        // 結合して日付順（新しい順）にソート
        $dealingItems = $boughtDealing->merge($soldDealing)->sortByDesc(function ($item) {
            $lastChat = $item->chats()->latest()->first();
            return $lastChat ? $lastChat->created_at : $item->created_at;
        });

        // 取引中の商品のメッセージ総数（タブの横に表示用）
        $totalMessagesCount = 0;
        foreach ($dealingItems as $item) {
            $totalMessagesCount += $item->chats()->count();
        }

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
