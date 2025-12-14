<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        // 既に認証済みの場合
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(config('fortify.home'));
        }

        // メールを再送信
        $request->user()->sendEmailVerificationNotification();

        // 成功メッセージとともにリダイレクト
        return back()->with('status', 'verification-link-sent');
    }
}
