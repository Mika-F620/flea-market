@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection
@section('btn')
  <input type="text" class="header__search header__searchPC" placeholder="なにをお探しですか？">
  <div class="header__btn">
    @if (Auth::check())
      <form class="header__loginLink" action="/logout" method="post">
        @csrf
        <button class="header__loginBtn">ログアウト</button>
      </form>
    @else
      <!-- ログインしていない場合、ログインボタンを表示 -->
      <p class="header__btnItem"><a href="{{ route('login') }}" class="header__loginBtn">ログイン</a></p>
    @endif
    <p class="header__btnItem"><a href="{{ route('mypage') }}" class="header__myLink">マイページ</a></p>
    <p class="header__btnItem"><a href="{{ route('sell.index') }}" class="header__sellLink">出品</a></p>
  </div>
  <input type="text" class="header__search header__searchSP" placeholder="なにをお探しですか？">
@endsection
@section('content')
  <section class="top">
    <div class="top__select">
      <div class="top__tab wrapper">
        <a href="{{ url('/') }}" class="top__tabList {{ $page === 'recommend' ? 'active' : '' }}">おすすめ</a>
        @if (Auth::check()) <!-- ログインしている場合のみ表示 -->
          <a href="{{ url('/?page=mylist') }}" class="top__tabList {{ $page === 'mylist' ? 'active' : '' }}">マイリスト</a>
        @else
          <!-- ログインしていない場合はリンクを表示しない -->
          <span class="top__tabList disabled" data-disabled="true">マイリスト</span>
        @endif
      </div>
    </div>
    <div class="top__contents wrapper">
      @forelse ($products as $product)
        @if ($product->is_sold) <!-- 購入済みの商品 -->
          <div class="top__item">
            <img class="top__itemImg" src="{{ $product->image ? asset('storage/' . $product->image) : asset('img/dammy.png') }}" alt="{{ $product->name }}">
            <p class="top__itemName">{{ $product->name }} <span class="solid-label">Sold</span></p>
            <p class="top__itemPrice">¥{{ number_format($product->price) }}</p>
          </div>
        @else <!-- 購入されていない商品 -->
          <a href="{{ route('products.show', $product->id) }}" class="top__item">
            <div class="top__item">
              <img class="top__itemImg" src="{{ $product->image ? asset('storage/' . $product->image) : asset('img/dammy.png') }}" alt="{{ $product->name }}">
              <p class="top__itemName">{{ $product->name }}</p>
              <p class="top__itemPrice">¥{{ number_format($product->price) }}</p>
            </div>
          </a>
        @endif
        @empty
          <p>商品が見つかりません。</p>
        @endforelse
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const tabs = document.querySelectorAll('.top__tabList');
      const contents = document.querySelector('.top__contents');

      tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => {
          const queryParam = index === 0 ? 'recommend' : 'mylist';
          window.location.href = `?page=${queryParam}`;
        });
      });
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const tabs = document.querySelectorAll('.top__tabList');

      tabs.forEach((tab) => {
        // ログインしていない場合、「マイリスト」タブに pointer-events: none を適用
        if (tab.classList.contains('disabled')) {
          tab.style.pointerEvents = 'none'; // クリック無効化
          tab.style.color = '#ccc'; // 任意: 色を変更して無効感を示す
        }
      });
    });
  </script>
@endsection