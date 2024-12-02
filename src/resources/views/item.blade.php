@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/item.css') }}">
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
          <p class="item__commentNum"><span id="comment-count-bubble">{{ $product->comments->count() }}</span></p>
        </div>
      </div>
      <a href="{{ route('purchase.show', ['id' => $product->id]) }}" class="formBtnRed">購入手続きへ</a>
      <div class="item__explanation">
        <h3 class="item__title">商品説明</h3>
        <p class="item__description">{{ $product->description }}</p>
      </div>
      <div class="item__info">
        <h3 class="item__title">商品の情報</h3>
        <div class="item__list">
          <h4 class="item__listName">カテゴリー</h4>
          <ul class="item__listTag">
            @foreach (json_decode($product->categories, true) as $category)
              <li class="item__listTagItem">{{ $category }}</li>
            @endforeach
          </ul>
        </div>
        <div class="item__list">
          <h4 class="item__listName">商品の状態</h4>
          <p class="item__listCondition">{{ $product->condition }}</p>
        </div>
      </div>
      <div class="item__comment">
        <p class="item__commentNumText">コメント(<span id="comment-count">{{ $product->comments->count() }}</span>)</p>
        <div class="item__commentUser">
          @if (Auth::check())
            <img class="item__commentUserImg" src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('img/dammy2.png') }}" alt="画像">
            <p class="item__commentUserName">{{ $user->name }}</p>
          @else
            <p class="item__unknow">未登録のユーザーです。</p>
          @endif
        </div>
        <ul class="item__commentList" id="comment-list">
          @foreach ($product->comments as $comment)
            <li>{{ $comment->user->name }}: {{ $comment->content }}</li>
          @endforeach
        </ul>
        @auth
          <h4 class="item__listName">商品へのコメント</h4>
          <form action="{{ route('comments.store') }}" method="POST">
            @csrf
              <textarea class="item__formArea" name="content" rows="3" placeholder="コメントを入力してください">{{ old('content') }}</textarea>
            
              <!-- バリデーションエラーの表示 -->
              @error('content')
                <p class="form__error">{{ $message }}</p>
              @enderror
              <button class="formBtnRed" id="submit-comment">コメントを送信する</button>
          </form>
        @else
          <p class="item__unknow">コメントを投稿するには<a href="{{ route('login') }}">ログイン</a>してください。</p>
        @endauth
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

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const submitButton = document.querySelector('#submit-comment');
      const commentContent = document.querySelector('textarea[name="content"]'); // コメントのテキストエリア
      const commentList = document.querySelector('#comment-list'); // コメントリストの要素
      const commentCount = document.querySelector('#comment-count'); // コメント数の表示要素
      const commentCountBubble = document.querySelector('#comment-count-bubble'); // バブルのコメント数表示
      const productId = {{ $product->id }}; // 商品ID

      if (submitButton) {
        submitButton.addEventListener('click', async (event) => {
          event.preventDefault(); // デフォルトのフォーム送信を防止

          const content = commentContent.value.trim();

          if (content === '') {
            alert('コメントを入力してください。');
            return;
          }

          try {
            const response = await fetch('{{ route('comments.store') }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
              },
              body: JSON.stringify({
                product_id: productId,
                content: content,
              }),
            });

            if (!response.ok) {
              const errorData = await response.json();
              console.error(errorData);
              alert(errorData.message || 'コメントの送信に失敗しました。');
              return;
            }

            const data = await response.json();

            // 新しいコメントをリストに追加
            const newComment = document.createElement('li');
            newComment.textContent = `${data.user_name}: ${data.comment.content}`;
            commentList.appendChild(newComment);

            // コメント数を更新
            const currentCount = parseInt(commentCount.textContent);
            const newCount = currentCount + 1;
            commentCount.textContent = newCount;
            if (commentCountBubble) {
              commentCountBubble.textContent = newCount; // バブルのコメント数も更新
            }

            // フォームをリセット
            commentContent.value = '';
          } catch (error) {
            console.error('コメントの送信に失敗しました:', error);
            alert('予期しないエラーが発生しました。');
          }
        });
      }
    });
  </script>
@endsection