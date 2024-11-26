@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/address.css') }}">
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
  <section class="address wrapper">
    <h2 class="sectionTitle">住所の変更</h2>
    <form class="address__form">
      <div class="address__item">
        <label class="address__label">郵便番号</label>
        <input class="address__input" type="text">
      </div>
      <div class="address__item">
        <label class="address__label">住所</label>
        <input class="address__input" type="text">
      </div>
      <div class="address__item">
        <label class="address__label">建物名</label>
        <input class="address__input" type="text">
      </div>
      <input class="formBtnRed" type="submit" value="更新する" />
    </form>
  </section>
@endsection