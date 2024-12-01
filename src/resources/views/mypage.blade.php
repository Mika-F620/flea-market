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
    <a href="{{ route('mypage') }}" class="header__btnItem">マイページ</a>
    <a href="{{ route('sell.index') }}" class="header__btnItem">出品</a>
  </div>
@endsection
@section('content')
  <section class="mypage">
    <div class="maypage__info">
      <div class="mypage__infoUser">
        <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('img/dammy2.png') }}" class="mypage__infoImg" alt="ユーザー画像">
        <p class="mypage__infoName">{{ $user->name }}</p>
      </div>
      <a href="{{ route('profile.edit') }}" class="mypage__infoBtn">プロフィールを編集</a>
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
      @if ($page === 'sell')
        @if ($products->isEmpty())
          <p>出品した商品がありません。</p>
        @else
          @foreach ($products as $product)
            <div class="mypage__item">
              <a href="{{ route('item.show', ['id' => $product->id]) }}" 
                @if ($product->is_sold) style="pointer-events: none;" @endif>
                <img class="mypage__itemImg" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                <p class="mypage__itemName">{{ $product->name }}</p>
                @if ($product->is_sold) <!-- 購入済みの商品 -->
                  <span class="sold-label">Sold</span>
                @endif
              </a>
            </div>
          @endforeach
        @endif
      @elseif ($page === 'buy')
        @if ($products->isEmpty())
          <p>購入した商品がありません。</p>
        @else
          @foreach ($products as $product)
            <div class="mypage__item">
              <a href="{{ route('item.show', ['id' => $product->id]) }}" style="pointer-events: none;">
                <img class="mypage__itemImg" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                <p class="mypage__itemName">{{ $product->name }}</p>
                <span class="sold-label">Sold</span> <!-- 購入済み商品には「Sold」を表示 -->
              </a>
            </div>
          @endforeach
        @endif
      @endif
    </div>
  </section>
@endsection