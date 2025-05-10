@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection
@section('btn')
  <form method="GET" action="{{ route('products.index') }}" class="header__search header__searchPC">
    <input type="hidden" name="page" value="{{ $page ?? 'default' }}">
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
    @else
      <!-- ログインしていない場合、ログインボタンを表示 -->
      <p class="header__btnItem"><a href="{{ route('login') }}" class="header__loginBtn">ログイン</a></p>
    @endif
    <p class="header__btnItem"><a href="{{ route('mypage') }}" class="header__myLink">マイページ</a></p>
    <p class="header__btnItem"><a href="{{ route('sell.index') }}" class="header__sellLink">出品</a></p>
  </div>
  <form method="GET" action="{{ route('products.index') }}" class="header__search header__searchSP">
    <input type="hidden" name="page" value="{{ $page ?? 'default' }}">
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
  <section class="thank-you wrapper">
    <div class="thank-you__content">
      <h1>ご購入ありがとうございます！</h1>
      <p>お客様の購入が完了しました。</p>
      <p>今後ともよろしくお願いいたします。</p>
      
      <!-- 取引完了ボタンを追加 -->
      <form method="GET" action="{{ route('chat.show', ['product_id' => $product ?? ''->id]) }}">
        <button type="submit" class="btn btn-primary">取引をする</button>
      </form>



      <!-- <a href="{{ route('mypage') }}" class="btn btn-primary">マイページへ戻る</a> -->
    </div>
  </section>
@endsection
