@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/item.css') }}">
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
  <section class="item wrapper">
    <img class="item__img" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
    <div class="item__details">
      <h2 class="item__name">{{ $product->name }}</h2>
      <p class="item__subName">ブランド名</p>
      <p class="item__price">¥<span class="item__price--big">{{ number_format($product->price) }}</span>(税込)</p>
      <div class="item__click">
        <div class="item__like">
          <img 
            class="item__likeImg" 
            id="like-icon" 
            src="{{ $isLiked ? asset('img/star-filled.svg') : asset('img/star.svg') }}" 
            alt="星" 
            data-liked="{{ $isLiked ? 'true' : 'false' }}" 
            data-product-id="{{ $product->id }}" 
            style="cursor: pointer;"
          />
          <p class="item__likeNum" id="like-count">{{ $product->likes->count() }}</p>
        </div>
        <div class="item__comment">
          <img class="item__commentImg" src="{{ asset('img/bubble.svg') }}" alt="吹き出し">
          <p class="item__commentNum">1</p>
        </div>
      </div>
      <a href="{{ route('purchase.show', ['id' => $product->id]) }}" class="formBtnRed">購入手続きへ</a>
      <div class="item__explanation">
        <h3 class="item__title">商品説明</h3>
        <p>{{ $product->description }}</p>
      </div>
      <div class="item__info">
        <h3 class="item__title">商品の情報</h3>
        <div class="item__list">
          <h4 class="item__listName">カテゴリー</h4>
          <ul class="item__listTag">
            <li class="item__listTagItem">{{ implode(', ', json_decode($product->categories, true)) }}</li>
          </ul>
        </div>
        <div class="item__list">
          <h4 class="item__listName">商品の状態</h4>
          <p class="item__listCondition">{{ $product->condition }}</p>
        </div>
      </div>
      <div class="item__comment">
        <p class="item__commentNum">コメント(1)</p>
        <div class="item__commentUser">
          <img class="item__commentUserImg" src="{{ asset('img/dammy2.png') }}" alt="画像">
          <p class="item__commentUserName">admin</p>
        </div>
        <ul class="item__commentList">
          <li>こちらにコメントが入ります。</li>
        </ul>
        <h4 class="item__listName">商品へのコメント</h4>
        <textarea></textarea>
        <input class="formBtnRed" type="submit" value="コメントを送信する" />
      </div>
    </div>
  </section>

  <script>
document.addEventListener('DOMContentLoaded', () => {
    const likeButton = document.querySelector('#like-icon'); // いいねアイコンの取得
    const likeCountElement = document.querySelector('#like-count'); // いいね数の取得
    const productId = likeButton.dataset.productId; // 商品IDの取得

    if (likeButton && likeCountElement) {
        likeButton.addEventListener('click', async () => {
            try {
                // サーバーにリクエストを送信
                const response = await fetch(`/like/toggle/${productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                // いいね状態の切り替え
                if (data.liked) {
                    likeButton.src = '{{ asset("img/star-filled.svg") }}'; // いいねされた状態
                } else {
                    likeButton.src = '{{ asset("img/star.svg") }}'; // いいねされていない状態
                }

                // いいね数を更新
                likeCountElement.textContent = data.likeCount;

            } catch (error) {
                console.error('Error:', error);
            }
        });
    }
});

</script>

@endsection