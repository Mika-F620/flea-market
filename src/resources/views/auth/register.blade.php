@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection
@section('content')
  <section class="wrapper">
    <h2 class="sectionTitle">会員登録</h2>
    <form class="register__form">
      @csrf
      <div class="register__formContents">
        <div class="register__item">
          <label class="register__label" for="name">ユーザー名</label>
          <input class="register__input" type="text" name="name" class="" for="name" value="{{ old('name') }}">
        </div>
        <div class="register__item">
          <label class="register__label" for="email">メールアドレス</label>
          <input class="register__input" type="email" name="email" class="" for="email" value="{{ old('email') }}">
        </div>
        <div class="register__item">
          <label class="register__label" for="pass">パスワード</label>
          <input class="register__input" type="password" name="password" class="" for="pass" value="{{ old('password') }}">
        </div>
        <div class="register__item">
          <label class="register__label" for="pass-confirm">確認用パスワード</label>
          <input class="register__input" type="password" name="password" class="" for="pass-confirm" value="{{ old('password') }}">
        </div>
      </div>
      <div class="register__btn">
        <input class="formBtnRed" type="submit" value="登録" />
        <a href="#" class="formLink">ログインはこちら</a>
      </div>
    </form>
  </section>
@endsection