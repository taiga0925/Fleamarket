<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Sold_item;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

class PurchaseController extends Controller
{
    /**
     * 購入画面
     * @return view ビュー
     */
    public function index($item_id)
    {
        $user = Auth::user();
        $item = Item::find($item_id);
        $profile = Profile::find($user)->first();


        return view('purchase', compact('item', 'profile', ));
    }

    /**
     * 配達先変更画面
     * @return view ビュー
     */
    public function address($item_id)
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('address', compact('user', 'profile', 'item_id'));
    }

    /**
     * 配送先変更
     * @return redirect リダイレクト　
     */
    public function update(Request $request, $item_id)
    {
        $user = Auth::user();
        $form = $request->all();
        $isChanged = false;
        unset($form['_token']);

        if ($user->profile) {
            foreach ($form as $key => $value) {
                if ($user->profile->$key != $value) {
                    $isChanged = true;
                    break;
                }
            }

            $user->profile->update($form);
        } else {
            $user->profile()->create($form);
            $isChanged = true;
        }

        if ($isChanged) {
            session()->flash('success', '配送先を変更しました');
        }

        return redirect('/purchase/' . $item_id);
    }

    /**
     * 購入処理
     * @return redirect リダイレクト
     */
    public function purchase(PurchaseRequest $request, $item_id)
    {
        $userId = Auth::id();
        $method = $request->input('method');

        $sold_item = new Sold_item();
        $sold_item->item_id = $item_id;
        $sold_item->user_id = $userId;
        $sold_item->method = $method;
        $sold_item->save();


        return redirect('/item/' . $item_id)->with('success', '購入完了しました');
    }

     /*単発決済用のコード*/
     public function charge(Request $request)
     {
         try {
             Stripe::setApiKey(env('STRIPE_SECRET'));

             $customer = Customer::create(array(
                 'email' => $request->stripeEmail,
                 'source' => $request->stripeToken
             ));

             $charge = Charge::create(array(
                 'customer' => $customer->id,
                 'amount' => 1000,
                 'currency' => 'jpy'
             ));

             return back();
         } catch (\Exception $ex) {
             return $ex->getMessage();
         }
     }
}
