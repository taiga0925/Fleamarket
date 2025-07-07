<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'image' => 'required|image',
            'category_id' => 'required',
            'status' => 'required',
            'item' => 'required',
            'detail' => 'required',
            'money' => 'required|regex:/^[1-9][0-9]*$/'
        ];

        if ($this->route('item_id')) {
            $rules['image'] = 'image';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'image.required' => '画像ファイル(jpg png bmp gif svg)を選択してください',
            'image.image' => '画像ファイル(jpg png bmp gif svg)を選択してください',
            'category_id.required' => 'カテゴリーを選択してください',
            'status.required' => '商品の状態を選択してください',
            'item.required' => '商品名を入力してください',
            'detail.required' => '商品説明を入力してください',
            'money.required' => '販売価格を半角数字で入力してください',
            'money.regex' => '販売価格を半角数字で入力してください',
        ];

    }
}
