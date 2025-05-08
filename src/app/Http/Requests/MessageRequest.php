<?php

// app/Http/Requests/MessageRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    public function authorize()
    {
        // 認可処理（例えばユーザーがメッセージを送信できるかチェック）
        return true;
    }

    public function rules()
    {
        return [
            'message' => 'required|max:400', // 本文が必須で400文字以内
            'image' => 'nullable|mimes:png,jpeg,jpg|max:2048', // 画像がPNGまたはJPEG形式で、最大2MB
        ];
    }

    public function messages()
    {
        return [
            'message.required' => '本文を入力してください。',
            'message.max' => '本文は400文字以内で入力してください。',
            'image.mimes' => '.png または .jpeg 形式でアップロードしてください。',
        ];
    }
}
