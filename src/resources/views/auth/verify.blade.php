{{-- resources/views/auth/verify.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>メール認証が必要です</h2>
        <p>こんにちは、{{ $user->name ?? 'ユーザー' }} さん。ご登録いただいたメールアドレスに認証リンクを送信しました。メールを確認してリンクをクリックしてください。</p>
        <a href="{{ route('home') }}" class="btn btn-primary">ホームに戻る</a>
    </div>
@endsection
