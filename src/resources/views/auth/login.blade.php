@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection
@section('content')
  <section class="login wrapper">
    <h2 class="sectionTitle">ログイン</h2>
    <form class="login__form" method="POST" action="{{ route('login') }}">
      @csrf
        <div class="login__formContents">
          <div class="login__item">
            <label class="login__label" for="login">ユーザー名 / メールアドレス</label>
            <input class="login__input" type="text" name="username_email" id="username_email" value="{{ old('username_email') }}">
          </div>
          <div class="login__item">
            <label class="login__label" for="pass">パスワード</label>
            <input class="login__input" type="password" name="password" class="" id="pass">
          </div>
        </div>
        <div class="login__btn">
          <input class="formBtnRed" type="submit" value="ログインする" />
          <a href="{{ route('register') }}" class="formLink">会員登録はこちら</a>
        </div>
    </form>
    @if($errors->any())
  <div class="formErrors">
    <ul>
      @foreach ($errors->all() as $error)
        <li class="formErrorItem">{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
  </section>
@endsection