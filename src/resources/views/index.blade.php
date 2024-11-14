@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection
@section('content')
  <section class="top">
    <div class="top__select">
      <div class="top__tab wrapper">
        <p class="top__tabList">おすすめ</p>
        <p class="top__tabList">マイリスト</p>
      </div>
    </div>
    <div class="top__contents wrapper">
      <div class="top__item">
        <img class="top__itemImg" src="{{ asset('img/dammy.png') }}" alt="商品画像">
        <p class="top__itemName">商品名</p>
      </div>
      <div class="top__item">
        <img class="top__itemImg" src="{{ asset('img/dammy.png') }}" alt="商品画像">
        <p class="top__itemName">商品名</p>
      </div>
      <div class="top__item">
        <img class="top__itemImg" src="{{ asset('img/dammy.png') }}" alt="商品画像">
        <p class="top__itemName">商品名</p>
      </div>
      <div class="top__item">
        <img class="top__itemImg" src="{{ asset('img/dammy.png') }}" alt="商品画像">
        <p class="top__itemName">商品名</p>
      </div>
      <div class="top__item">
        <img class="top__itemImg" src="{{ asset('img/dammy.png') }}" alt="商品画像">
        <p class="top__itemName">商品名</p>
      </div>
      <div class="top__item">
        <img class="top__itemImg" src="{{ asset('img/dammy.png') }}" alt="商品画像">
        <p class="top__itemName">商品名</p>
      </div>
    </div>
  </section>
@endsection