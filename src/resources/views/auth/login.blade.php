@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection
@section('content')
  <section class="login wrapper">
    <h2 class="sectionTitle">ログイン</h2>
    <form class="login__form">
      @csrf
      <div class="login__formContents">
        <div class="login__item">
          <label class="login__label" for="email">ユーザー名 / メールアドレス</label>
          <input class="login__input" type="email" name="email" class="" for="email">
        </div>
        <div class="login__item">
          <label class="login__label" for="pass">パスワード</label>
          <input class="login__input" type="password" name="password" class="" for="pass">
        </div>
      </div>
      <div class="login__btn">
        <input class="formBtnRed" type="submit" value="ログインする" />
        <a href="#" class="formLink">会員登録はこちら</a>
      </div>
    </form>
  </section>
@endsection