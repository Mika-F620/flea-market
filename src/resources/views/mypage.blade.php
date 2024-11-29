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
        <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('img/dammy2.png') }}" class="mypage__infoImg" alt="ユーザー画像">
        <p class="mypage__infoName">{{ $user->name }}</p>
      </div>
      <button class="mypage__infoBtn">プロフィールを編集</button>
    </div>
    <div class="mypage__select">
      <div class="mypage__tab wrapper">
        <p class="mypage__tabList">
          <a href="{{ route('mypage', ['page' => 'sell']) }}" class="{{ $page === 'sell' ? 'active' : '' }}">
            出品した商品
          </a>
        </p>
        <p class="mypage__tabList">
          <a href="{{ route('mypage', ['page' => 'buy']) }}" class="{{ $page === 'buy' ? 'active' : '' }}">
            購入した商品
          </a>
        </p>
      </div>
    </div>
    <div class="mypage__contents wrapper">
      @if ($products->isEmpty())
        <p>商品がありません。</p>
      @else
        @foreach ($products as $product)
          <div class="mypage__item">
            <img class="mypage__itemImg" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
            <p class="mypage__itemName">{{ $product->name }}</p>
          </div>
        @endforeach
      @endif
    </div>
  </section>
@endsection