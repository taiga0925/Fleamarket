<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Item;
use App\Models\Sold_item;
use App\Models\Rating;
use App\Mail\AssessmentNotification;

class RatingController extends Controller
{
    // ... (indexメソッド等は削除済み) ...

    /**
     * 評価の保存処理
     */
    public function store(Request $request, $item_id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $soldItem = Sold_item::where('item_id', $item_id)->firstOrFail();
        $item = Item::findOrFail($item_id);

        // 評価される人（相手）のID
        $target_user_id = ($user->id == $soldItem->user_id) ? $item->user_id : $soldItem->user_id;

        // 評価を保存
        $rating = Rating::create([
            'item_id' => $item_id,
            'rater_id' => $user->id,
            'user_id' => $target_user_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        //  出品者へメール送信
        if ($user->id == $soldItem->user_id) {
            // 相手（出品者）を取得
            $seller = $item->user;
            // メール送信実行
            Mail::to($seller->email)->send(new AssessmentNotification($item, $rating));
        }

        // 商品一覧画面（トップページ）へ遷移
        // withのメッセージも少し変更
        return redirect('/')->with('success', '評価を送信し、取引が完了しました。');
    }
}
