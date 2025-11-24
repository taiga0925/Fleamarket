<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // 1. 本文（必須）、3. 400文字以内
            'message' => 'required|string|max:400',
            // 2. 画像形式（.png, .jpeg）
            'image'   => 'nullable|image|mimes:png,jpeg,jpg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'message.required' => '本文を入力してください',
            'message.max'      => '本文は400文字以内で入力してください',
            'image.mimes'      => '「.png」または「.jpeg」形式でアップロードしてください',
            // その他の画像エラー用
            'image.image'      => '画像ファイルを選択してください',
            'image.max'        => 'ファイルサイズは2MB以内にしてください',
        ];
    }
}
