@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/item.css') }}">
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
  <section class="item wrapper">
    <img class="item__img" src="{{ asset('img/dammy3.png') }}" alt="商品画像">
    <div class="item__details">
      <h2 class="item__name">商品名がここに入る</h2>
      <p class="item__subName">ブランド名</p>
      <p class="item__price">¥<span class="item__price--big">47,000</span>(税込)</p>
      <div class="item__click">
        <div class="item__like">
          <img class="item__likeImg" src="{{ asset('img/star.svg') }}" alt="星">
          <p class="item__likeNum">3</p>
        </div>
        <div class="item__comment">
          <img class="item__commentImg" src="{{ asset('img/bubble.svg') }}" alt="星">
          <p class="item__commentNum">1</p>
        </div>
      </div>
      <input class="formBtnRed" type="submit" value="購入手続きへ" />
      <div class="item__explanation">
        <h3 class="item__title">商品説明</h3>
        <p>カラー：グレー</p>
        <p>新品<br>商品の状態は良好です。傷もありません。</p>
        <p>購入後、即発送いたします。</p>
      </div>
      <div class="item__info">
        <h3 class="item__title">商品の情報</h3>
        <div class="item__list">
          <h4 class="item__listName">カテゴリー</h4>
          <ul class="item__listTag">
            <li class="item__listTagItem">洋服</li>
            <li class="item__listTagItem">メンズ</li>
          </ul>
        </div>
        <div class="item__list">
          <h4 class="item__listName">商品の状態</h4>
          <p class="item__listCondition">良好</p>
        </div>
      </div>
      <div class="item__comment">
        <p class="item__commentNum">コメント(1)</p>
        <div class="item__commentUser">
          <img class="item__commentUserImg" src="{{ asset('img/dammy2.png') }}" alt="画像">
          <p class="item__commentUserName">admin</p>
        </div>
        <ul class="item__commentList">
          <li>こちらにコメントが入ります。</li>
        </ul>
        <h4 class="item__listName">商品へのコメント</h4>
        <textarea></textarea>
        <input class="formBtnRed" type="submit" value="コメントを送信する" />
      </div>
    </div>
  </section>
@endsection