<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    protected $loginField;
    protected $loginValue;

    public function authorize()
    {
        return true; // 必要に応じて認可ロジックを追加
    }

    public function rules()
    {
        return [
            // $this->loginField => ['required', 'string', 'max:255'],
            'login_identifier' => ['required', 'string', 'max:255'], // 固定キー
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages()
    {
        return [
            // "{$this->loginField}.required" => 'ユーザー名またはメールアドレスを入力してください。',
            // 'password.required' => 'パスワードを入力してください。',
            // 'password.min' => 'パスワードは8文字以上で入力してください。',
            'login_identifier.required' => 'ユーザー名またはメールアドレスを入力してください。',
        'login_identifier.string' => '入力が無効です。',
        'login_identifier.max' => '255文字以内で入力してください。',
        'password.required' => 'パスワードを入力してください。',
        'password.min' => 'パスワードは8文字以上で入力してください。',
        ];
    }

    protected function prepareForValidation()
    {
        $this->loginField = filter_var($this->input('login_identifier'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $this->loginValue = $this->input('login_identifier');
        $this->merge([$this->loginField => $this->loginValue]);
    }
}
