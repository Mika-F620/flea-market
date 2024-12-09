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
  <section class="purchase wrapper">
    <form class="purchase__form" action="{{ route('payment.store') }}" method="POST">
      @csrf
        <!-- 商品IDを隠しフィールドで送信 -->
        <input type="hidden" name="product_id" value="{{ $product->id ?? '' }}">
        <div class="purchase__left">
          <div class="purchase__product">
            <img class="purchase__productImg" src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
            <div class="purchase__productInfo">
              <h2 class="product__productName">{{ $product->name }}</h2>
              <p class="product__productPrice">¥<span class="product__productPrice--big">{{ number_format($product->price) }}</span></p>
            </div>
          </div>
          <div class="purchase__pay">
            <label class="purchase__payTitle">支払い方法</label>
            <div class="purchase__select">
              <select class="purchase__selectDrop" name="payment_method" id="payment-method-select">
                <option value="" disabled selected>選択してください</option>
                <option value="コンビニ払い">コンビニ払い</option>
                <option value="カード払い">カード払い</option>
              </select>
            </div>
            @error('payment_method')
              <p class="form__error">{{ $message }}</p>
            @enderror
          </div>
          <div class="purchase__address">
            <div class="purchase__addressHeading">
              <label class="purchase__addressTitle">配送先</label>
              <a class="purchase__addressLink" href="{{ route('purchase.address.edit', ['id' => $product->id]) }}">変更する</a>
            </div>
            <p class="purchase__addressDetails">
              〒{{ $tempAddress['postal_code'] ?? '未設定' }}<br>
              {{ $tempAddress['address'] ?? '未設定' }}<br>
              {{ $tempAddress['building_name'] ?? '' }}
            </p>

            <!-- 隠しフィールドで住所情報を送信 -->
            <input type="hidden" name="postal_code" value="{{ $tempAddress['postal_code'] ?? $user->postal_code }}">
            <input type="hidden" name="address" value="{{ $tempAddress['address'] ?? $user->address }}">
            <input type="hidden" name="building_name" value="{{ $tempAddress['building_name'] ?? $user->building_name }}">
          </div>
        </div>
        <div class="purchase__right">
          <dl class="purchase__buyInfo">
            <div class="purchase__buyInfoLine">
              <dt class="purchase__buyInfoTitle">商品代金</dt>
              <dd class="purchase__buyInfoDetail"><span class="purchase__buyInfoDetail--small">¥</span>{{ number_format($product->price) }}</dd>
            </div>
            <div class="purchase__buyInfoLine">
              <dt class="purchase__buyInfoTitle">支払い方法</dt>
              <dd class="purchase__buyInfoDetail" id="selected-payment-method">選択されていません</dd>
            </div>
          </dl>
          <!-- 購入ボタン -->
          <input class="formBtnRed" type="submit" value="購入する" />
        </div>
    </form>
  </section>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const paymentMethodSelect = document.getElementById('payment-method-select');
      const selectedPaymentMethod = document.getElementById('selected-payment-method');

      // 支払い方法が変更されたときに右側の要素を更新
      paymentMethodSelect.addEventListener('change', function () {
        selectedPaymentMethod.textContent = this.value || '選択されていません';
      });
    });
  </script>
@endsection