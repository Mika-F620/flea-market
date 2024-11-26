@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
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
  <section class="purchase wrapper">
    <div class="purchase__left">
      <div class="purchase__product">
        <img class="purchase__productImg" src="{{ asset('img/dammy3.png') }}" alt="画像">
        <div class="purchase__productInfo">
          <h2 class="product__productName">商品名</h2>
          <p class="product__productPrice">¥<span class="product__productPrice--big">47,000</span></p>
        </div>
      </div>
      <div class="purchase__pay">
        <label class="purchase__payTitle">支払い方法</label>
        <select>
          <option>選択してください</option>
          <option>コンビニ払い</option>
          <option>カード払い</option>
        </select>
      </div>
      <div class="purchase__address">
        <div class="purchase__addressHeading">
          <label class="purchase__addressTitle">配送先</label>
          <a class="purchase__addressLink" href="#">変更する</a>
        </div>
        <p class="purchase__addressDetails">〒XXX-YYYY<br>ここは住所と建物が入ります</p>
      </div>
    </div>
    <div class="purchase__right">
      <dl class="purchase__buyInfo">
        <div class="purchase__buyInfoLine">
          <dt class="purchase__buyInfoTitle">商品代金</dt>
          <dd class="purchase__buyInfoDetail"><span class="purchase__buyInfoDetail--small">¥</span>47,000</dd>
        </div>
        <div class="purchase__buyInfoLine">
          <dt class="purchase__buyInfoTitle">支払い方法</dt>
          <dd class="purchase__buyInfoDetail">コンビニ払い</dd>
        </div>
      </dl>
      <input class="formBtnRed" type="submit" value="購入する" />
    </div>
  </section>
@endsection