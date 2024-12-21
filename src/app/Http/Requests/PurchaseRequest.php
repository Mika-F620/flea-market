<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // すべてのユーザーに対して許可
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id', // product_idが必須で、productsテーブルに存在すること
            'payment_method' => 'required|string|in:コンビニ払い,カード払い', // 支払い方法
            'postal_code' => 'required|string|max:10', // 郵便番号
            'address' => 'required|string|max:255', // 住所
            'building_name' => 'nullable|string|max:255', // 建物名（オプション）
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
            'product_id.required' => '商品IDは必須です。',
            'product_id.exists' => '指定された商品は存在しません。',
            'payment_method.required' => '支払い方法を選択してください',
            'payment_method.in' => '支払い方法は「コンビニ払い」または「カード払い」を選択してください。',
            'postal_code.required' => '郵便番号は必須です。',
            'postal_code.max' => '郵便番号は最大10文字までです。',
            'address.required' => '住所は必須です。',
            'address.max' => '住所は最大255文字までです。',
            'building_name.max' => '建物名は最大255文字までです。',
        ];
    }
}