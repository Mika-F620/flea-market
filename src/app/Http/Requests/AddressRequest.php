<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * ユーザーがリクエストを行うことを許可するかどうか
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // このリクエストは全てのユーザーに対して許可
    }

    /**
     * バリデーションルールを取得
     *
     * @return array
     */
    public function rules()
    {
        return [
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
            'postal_code.required' => '郵便番号を入力してください。',
            'postal_code.max' => '郵便番号は最大10文字までです。',
            'address.required' => '住所を入力してください。',
            'address.max' => '住所は最大255文字までです。',
            'building_name.max' => '建物名は最大255文字までです。',
        ];
    }
}