@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/index.css') }}">
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
    <p class="header__btnItem"><a href="{{ route('mypage') }}" class="">マイページ</a></p>
    <p class="header__btnItem">出品</p>
  </div>
@endsection
@section('content')
  <section class="top">
    <div class="top__select">
      <div class="top__tab wrapper">
        <p class="top__tabList {{ $page === 'recommend' ? 'active' : '' }}">おすすめ</p>
        <p class="top__tabList {{ $page === 'mylist' ? 'active' : '' }}">マイリスト</p>
      </div>
    </div>
    <div class="top__contents wrapper">
      @forelse ($products as $product)
        <a href="{{ route('products.show', $product->id) }}" class="top__item">
          <div class="top__item">
            <img class="top__itemImg" src="{{ $product->image ? asset('storage/' . $product->image) : asset('img/dammy.png') }}" alt="{{ $product->name }}">
            <p class="top__itemName">{{ $product->name }}</p>
            <p class="top__itemPrice">¥{{ number_format($product->price) }}</p>
          </div>
        </a>
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
@endsection