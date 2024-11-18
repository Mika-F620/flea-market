@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
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
  <section class="mypage">
    <div class="maypage__info">
      <div class="mypage__infoUser">
        <img src="{{ asset('img/dammy2.png') }}" class="mypage__infoImg" alt="写真">
        <p class="mypage__infoName">ユーザー名</p>
      </div>
      <button class="mypage__infoBtn">プロフィールを編集</button>
    </div>
    <div class="mypage__select">
      <div class="mypage__tab wrapper">
        <p class="mypage__tabList">出品した商品</p>
        <p class="mypage__tabList">購入した商品</p>
      </div>
    </div>
    <div class="mypage__contents wrapper">
      <div class="mypage__item">
        <img class="mypage__itemImg" src="{{ asset('img/dammy.png') }}" alt="商品画像">
        <p class="mypage__itemName">商品名</p>
      </div>
      <div class="mypage__item">
        <img class="mypage__itemImg" src="{{ asset('img/dammy.png') }}" alt="商品画像">
        <p class="mypage__itemName">商品名</p>
      </div>
      <div class="mypage__item">
        <img class="mypage__itemImg" src="{{ asset('img/dammy.png') }}" alt="商品画像">
        <p class="mypage__itemName">商品名</p>
      </div>
      <div class="mypage__item">
        <img class="mypage__itemImg" src="{{ asset('img/dammy.png') }}" alt="商品画像">
        <p class="mypage__itemName">商品名</p>
      </div>
      <div class="mypage__item">
        <img class="mypage__itemImg" src="{{ asset('img/dammy.png') }}" alt="商品画像">
        <p class="mypage__itemName">商品名</p>
      </div>
      <div class="mypage__item">
        <img class="mypage__itemImg" src="{{ asset('img/dammy.png') }}" alt="商品画像">
        <p class="mypage__itemName">商品名</p>
      </div>
    </div>
  </section>
@endsection