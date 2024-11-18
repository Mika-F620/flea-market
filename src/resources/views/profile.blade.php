@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection
@section('btn')
  <input type="text" class="header__search" placeholder="なにをお探しですか？">
  <div class="header__btn">
    @if (Auth::check())
      <form class="header__loginLink" action="/logout" method="post">
        @csrf
        <button class="header__loginBtn">ログアウト</button>
      </form>
    @endif
    <p class="header__btnItem">マイページ</p>
    <p class="header__btnItem">出品</p>
  </div>
@endsection
@section('content')
  <section class="profile wrapper">
    <h2 class="sectionTitle">プロフィール設定</h2>
    <div class="profile__contents">
      <div class="profile__photo">
        <img class="profile__img" src="{{ asset('img/dammy2.png') }}" alt="写真">
        <button class="profile__btn">画像を選択する</button>
      </div>
      <form class="profile__form">
        @csrf
          <div class="profile__formContents">
            <div class="profile__item">
              <label class="profile__label" for="name">ユーザー名</label>
              <input class="profile__input" type="text" name="name" class="" id="name" value="{{ old('name') }}">
              @error('name')
                <p class="form__error">{{ $message }}</p>
              @enderror
            </div>
            <div class="profile__item">
              <label class="profile__label" for="post-num">郵便番号</label>
              <input class="profile__input" type="text" name="post-num" class="" id="post-num" value="{{ old('post-num') }}">
              @error('post-num')
                <p class="form__error">{{ $message }}</p>
              @enderror
            </div>
            <div class="profile__item">
              <label class="profile__label" for="address">住所</label>
              <input class="profile__input" type="text" name="address" class="" id="address" value="{{ old('address') }}">
              @error('address')
                <p class="form__error">{{ $message }}</p>
              @enderror
            </div>
            <div class="profile__item">
              <label class="profile__label" for="building">建物名</label>
              <input class="profile__input" type="text" name="building" class="" id="building" value="{{ old('building') }}">
              @error('building')
                <p class="form__error">{{ $message }}</p>
              @enderror
            </div>
          </div>
          <div class="profile__update">
            <input class="formBtnRed" type="submit" value="更新する" />
          </div>
      </form>
    </div>
  </section>
@endsection