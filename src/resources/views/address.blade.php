@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/address.css') }}">
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
  <section class="address wrapper">
    <h2 class="sectionTitle">住所の変更</h2>
    <form action="{{ route('purchase.address.update', ['id' => $product->id]) }}" class="address__form" method="POST">
      @csrf
      @method('PUT') <!-- 更新の場合はPUTメソッドを明示 -->
        <div class="address__item">
          <label class="address__label">郵便番号</label>
          <input class="address__input" type="text" name="postal_code" value="{{ old('postal_code', $purchase->postal_code ?? '') }}">
          @error('postal_code')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>
        <div class="address__item">
          <label class="address__label">住所</label>
          <input class="address__input" type="text" name="address" value="{{ old('address', $purchase->address ?? '') }}">
          @error('address')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>
        <div class="address__item">
          <label class="address__label">建物名</label>
          <input class="address__input" type="text" name="building_name" value="{{ old('building_name', $purchase->building_name ?? '') }}">
          @error('building_name')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>
        <input class="formBtnRed" type="submit" value="更新する" />
    </form>
  </section>
@endsection