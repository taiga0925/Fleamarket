<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $sellItems = $user->items;
        $soldItems = $user->soldToItems ?? null;

        $data = [
            'user' => $user,
            'sellItems' => $sellItems,
            'soldItems' => $soldItems,
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
