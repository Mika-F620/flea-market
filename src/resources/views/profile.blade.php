@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
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
  <section class="profile wrapper">
    <h2 class="sectionTitle">プロフィール設定</h2>
    <div class="profile__contents">
      <form class="profile__form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
          <div class="profile__photo">
            <!-- プロフィール画像の表示 -->
            <img class="profile__img" id="preview" 
              src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('img/dammy2.png') }}" 
              alt="写真">
            <!-- ファイル選択ボタン -->
            <label for="profile_image" class="profile__btn">画像を選択する</label>
            <input type="file" id="profile_image" name="profile_image" class="profile__fileInput" accept="image/*" style="display: none;" onchange="previewImage(event)">
          </div>
          <div class="profile__formContents">
            <div class="profile__item">
                <label class="profile__label" for="name">ユーザー名</label>
                <input class="profile__input" type="text" name="name" id="name" value="{{ old('name', $user->name) }}">
                @error('name')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>
            <div class="profile__item">
                <label class="profile__label" for="postal_code">郵便番号</label>
                <input class="profile__input" type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
                @error('postal_code')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>
            <div class="profile__item">
                <label class="profile__label" for="address">住所</label>
                <input class="profile__input" type="text" name="address" id="address" value="{{ old('address', $user->address) }}">
                @error('address')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>
            <div class="profile__item">
                <label class="profile__label" for="building">建物名</label>
                <input class="profile__input" type="text" name="building" id="building" value="{{ old('building', $user->building) }}">
                @error('building')
                    <p class="form__error">{{ $message }}</p>
                @enderror
            </div>
          </div>
          <div class="profile__update">
            <input class="formBtnRed" type="submit" value="更新する" />
          </div>
      </form>
    </div>
  </section>
  <script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('preview');
            preview.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
  </script>
@endsection