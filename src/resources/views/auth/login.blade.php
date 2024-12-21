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
          <label class="login__label" for="login_identifier">ユーザー名 / メールアドレス</label>
          <input class="login__input" type="text" name="login_identifier" id="login_identifier" value="{{ old('login_identifier') }}">
          @error('login_identifier')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>
        <div class="login__item">
          <label class="login__label" for="pass">パスワード</label>
          <input class="login__input" type="password" name="password" class="" id="pass">
          @error('password')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>
        @if (session('errors') && session('errors')->has('login_error'))
          <div class="form__error">
            {{ session('errors')->first('login_error') }}
          </div>
        @endif
      </div>
      <div class="login__btn">
        <input class="formBtnRed" type="submit" value="ログインする" />
        <a href="{{ route('register') }}" class="formLink">会員登録はこちら</a>
      </div>
    </form>
  </section>
@endsection