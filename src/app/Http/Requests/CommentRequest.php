<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // コメント投稿は全ての認証ユーザーに許可
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required|max:255', // コメントは必須、最大255文字
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
            'content.required' => 'コメントを入力してください。',
            'content.max' => 'コメントは255文字以内で入力してください。',
        ];
    }
}
