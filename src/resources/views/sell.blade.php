@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
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
  <section class="sell wrapper">
    <h2 class="sectionTitle">商品の出品</h2>
    <form>
      <div class="sell__item">
        <label class="sell__itemTitle">商品画像</label>
      </div>
      <h3 class="sell__SubTitle">商品の詳細</h3>
      <div class="sell__item">
        <label class="sell__itemTitle">カテゴリー</label>
        <ul class="sell__tag">
          <li class="sell__tagItem">ファッション</li>
          <li class="sell__tagItem">家電</li>
          <li class="sell__tagItem">インテリア</li>
          <li class="sell__tagItem">レディース</li>
          <li class="sell__tagItem">メンズ</li>
          <li class="sell__tagItem">コスメ</li>
          <li class="sell__tagItem">本</li>
          <li class="sell__tagItem">ゲーム</li>
          <li class="sell__tagItem">スポーツ</li>
          <li class="sell__tagItem">キッチン</li>
          <li class="sell__tagItem">ハンドメイド</li>
          <li class="sell__tagItem">アクセサリー</li>
          <li class="sell__tagItem">おもちゃ</li>
          <li class="sell__tagItem">ベビー・キッズ</li>
        </ul>
      </div>
      <div class="sell__item">
        <label class="sell__itemTitle">商品の状態</label>
        <select>
          <option>選択してください</option>
          <option>良好</option>
          <option>目立った傷や汚れなし</option>
          <option>やや傷や汚れあり</option>
          <option>状態が悪い</option>
        </select>
      </div>
      <h3 class="sell__SubTitle">商品名と説明</h3>
      <div class="sell__item">
        <label class="sell__itemTitle">商品名</label>
        <input type="text">
      </div>
      <div class="sell__item">
        <label class="sell__itemTitle">商品の説明</label>
        <textarea></textarea>
      </div>
      <div class="sell__item">
        <label class="sell__itemTitle">販売価格</label>
        <input type="text">
      </div>
      <input class="formBtnRed" type="submit" value="出品する" />
    </form>
  </section>
@endsection