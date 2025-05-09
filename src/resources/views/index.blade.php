@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection
@section('btn')
  <form method="GET" action="{{ route('products.index') }}" class="header__search header__searchPC">
    <input type="hidden" name="page" value="{{ $page }}">
    <input 
      type="text" 
      name="search" 
      class="header__search header__searchPC" 
      placeholder="なにをお探しですか？" 
      value="{{ $searchQuery ?? '' }}" 
    />
    <button type="submit" style="display: none;"></button>
  </form>
  <div class="header__btn">
    @if (Auth::check())
      <form class="header__loginLink" action="/logout" method="post">
        @csrf
        <button class="header__loginBtn">ログアウト</button>
      </form>
      <p class="header__btnItem"><a href="{{ route('mypage') }}" class="header__myLink">マイページ</a></p>
    @else
      <!-- ログインしていない場合、ログインボタンを表示 -->
      <p class="header__btnItem"><a href="{{ route('login') }}" class="header__loginBtn">ログイン</a></p>
    @endif
    <p class="header__btnItem"><a href="{{ route('sell.index') }}" class="header__sellLink">出品</a></p>
  </div>
  <form method="GET" action="{{ route('products.index') }}" class="header__search header__searchSP">
    <input type="hidden" name="page" value="{{ $page }}">
    <input 
      type="text" 
      name="search" 
      class="header__search header__searchSP" 
      placeholder="なにをお探しですか？" 
      value="{{ $searchQuery ?? '' }}" 
    />
    <button type="submit" style="display: none;"></button>
  </form>
@endsection
@section('content')
  <section class="top">
    <div class="top__select">
      <div class="top__tab wrapper">
        <a href="{{ url('/?page=recommend&search=' . urlencode($searchQuery ?? '')) }}" class="top__tabList {{ $page === 'recommend' ? 'active' : '' }}">おすすめ</a>
        @if (Auth::check()) <!-- ログインしている場合のみ表示 -->
          <a href="{{ url('/?page=mylist&search=' . urlencode($searchQuery ?? '')) }}" class="top__tabList {{ $page === 'mylist' ? 'active' : '' }}">マイリスト</a>
        @else
          <!-- ログインしていない場合はリンクを表示しない -->
          <span class="top__tabList disabled" data-disabled="true">マイリスト</span>
        @endif
      </div>
    </div>
    @if ($searchQuery)
      <p class="top__result">「{{ $searchQuery }}」の検索結果</p>
    @endif
    <div class="top__contents wrapper">
      @forelse ($products as $product)
        @if ($product->is_sold) <!-- 購入済みの商品 -->
          <div class="top__item">
            <div class="top__itemMask"></div>
            <img class="top__itemImg" src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
            <p class="top__itemName">{{ $product->name }} <span class="solid-label">Sold</span></p>
          </div>
        @elseif (isset($product->status) && $product->status === '取引中') <!-- 取引中の商品 -->
        <div class="top__item">
          <a class="top__itemLink" href="{{ route('chat.show', $product->id) }}">
            <img class="top__itemImg" src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
            <p class="top__itemName">{{ $product->name }}</p>
          </a>
        </div>
        @else <!-- 購入されていない商品 -->
          <div class="top__item">
            <a class="top__itemLink" href="{{ route('products.show', $product->id) }}" class="top__item">
              <img class="top__itemImg" src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
              <p class="top__itemName">{{ $product->name }}</p>
            </a>
          </div>
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