@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection
@section('content')
  <section class="wrapper">
    <h2 class="sectionTitle">会員登録</h2>
    <form class="register__form" action="{{ route('register') }}" method="POST" novalidate>
      @csrf
      <div class="register__formContents">
        <div class="register__item">
          <label class="register__label" for="name">ユーザー名</label>
          <input class="register__input" type="text" name="name" class="" id="name" value="{{ old('name') }}">
          @error('name')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>
        <div class="register__item">
          <label class="register__label" for="email">メールアドレス</label>
          <input class="register__input" type="email" name="email" class="" id="email" value="{{ old('email') }}">
          @error('email')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>
        <div class="register__item">
          <label class="register__label" for="pass">パスワード</label>
          <input class="register__input" type="password" name="password" class="" id="pass">
          @error('password')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>
        <div class="register__item">
          <label class="register__label" for="password_confirmation">確認用パスワード</label>
          <input class="register__input" type="password" name="password_confirmation" class="" id="password_confirmation">
          @error('password_confirmation')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>
      </div>
      <div class="register__btn">
        <input class="formBtnRed" type="submit" value="登録" />
        <a href="{{ route('login') }}" class="formLink">ログインはこちら</a>
      </div>
    </form>
  </section>
@endsection