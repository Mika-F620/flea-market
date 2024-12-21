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
        return true; // 認証済みのユーザーに対して許可
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048', // 画像のバリデーション
            'categories' => 'required|array|min:1', // 少なくとも1つのカテゴリーが選択されていること
            'condition' => 'required|string', // 商品の状態
            'name' => 'required|string|max:255', // 商品名
            'description' => 'required|string', // 商品説明
            'price' => 'required|min:1',
        ];
    }

    /**
     * バリデーションエラーメッセージのカスタマイズ
     *
     * @return array
     */
    public function messages()
    {
        return [
            'image.required' => '商品画像を選択してください。',
            'image.image' => '画像形式はjpg, jpeg, png, gifのみ対応しています。',
            'image.max' => '画像サイズは最大2MBまでです。',
            'categories.required' => 'カテゴリは少なくとも1つ選択してください。',
            'condition.required' => '商品の状態を選択してください。',
            'name.required' => '商品名を入力してください。',
            'name.max' => '商品名は最大255文字までです。',
            'description.required' => '商品説明を入力してください',
            'price.required' => '販売価格を入力してください',
            'price.min' => '販売価格は1円以上でなければなりません。',
            'price.integer' => '販売価格は整数でなければなりません。',
        ];
    }
}