@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
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
  <section class="purchase wrapper">
  @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form class="purchase__form" action="{{ route('purchase.store') }}" method="POST">
      @csrf
        <!-- 商品IDを隠しフィールドで送信 -->
        <input type="hidden" name="product_id" value="{{ $product->id ?? '' }}">
        <div class="purchase__left">
          <div class="purchase__product">
            <img class="purchase__productImg" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
            <div class="purchase__productInfo">
              <h2 class="product__productName">{{ $product->name }}</h2>
              <p class="product__productPrice">¥<span class="product__productPrice--big">{{ number_format($product->price) }}</span></p>
            </div>
          </div>
          <div class="purchase__pay">
            <label class="purchase__payTitle">支払い方法</label>
            <select name="payment_method" id="payment-method-select">
              <option value="" disabled selected>選択してください</option>
              <option value="コンビニ払い">コンビニ払い</option>
              <option value="カード払い">カード払い</option>
            </select>
          </div>
          <div class="purchase__address">
            <div class="purchase__addressHeading">
              <label class="purchase__addressTitle">配送先</label>
              <a class="purchase__addressLink" href="{{ route('purchase.address.edit', ['id' => $product->id]) }}">変更する</a>
            </div>
            <p class="purchase__addressDetails">
              〒{{ $user->postal_code ?? '未設定' }}<br>
              {{ $user->address ?? '未設定' }}<br>
              {{ $user->building_name ?? '' }}
            </p>

            <!-- 隠しフィールドで住所情報を送信 -->
            <input type="hidden" name="postal_code" value="{{ $user->postal_code }}">
            <input type="hidden" name="address" value="{{ $user->address }}">
            <input type="hidden" name="building_name" value="{{ $user->building_name }}">
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