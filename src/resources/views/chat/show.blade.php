@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
  <section class="show">
    <aside class="show__sidebar">
      <p class="show__sidebarTitle">その他の取引</p>
      <div class="show__sidebarDetails">
        <p class="show__sidebarDetailsName">商品名</p>
        <p class="show__sidebarDetailsName">商品名</p>
        <p class="show__sidebarDetailsName">商品名</p>
      </div>
    </aside>
    <div class="show__contents">
      <div class="show__heading">
        <div class="show__headingInfo">
          <!-- 取引を開始したユーザー（購入者）の画像を表示 -->
          @if ($seller->id === Auth::id())
              <img class="show__productChatUserImg" 
                   src="{{ $buyer->profile_image ? asset('storage/' . $buyer->profile_image) : asset('img/dammy2.png') }}" 
                   alt="購入者の画像">
            @else
              <img class="show__productChatUserImg" 
                   src="{{ $seller->profile_image ? asset('storage/' . $seller->profile_image) : asset('img/dammy2.png') }}" 
                   alt="出品者の画像">
            @endif
          @if ($seller->id === Auth::id())
          <h2 class="show__headingTitle">{{ $buyer->name }}さんとの取引画面</h2>
        @else
          <h2 class="show__headingTitle">{{ $seller->name }}さんとの取引画面</h2>
        @endif
        </div>
       <!-- 取引が完了していない場合のみ表示 -->
       @if($tradingProduct && $tradingProduct->status != '取引完了')
          <a href="javascript:void(0);" class="show__headingBtn" onclick="openModal()">取引を完了する</a>
        @else
          <p>取引は完了しました。</p>
        @endif
        <!-- 取引を完了するボタン -->
        <!-- <a href="javascript:void(0);" class="show__headingBtn" onclick="openModal()">取引を完了する</a> -->
      </div>

      <div class="show__product">
        <img class="show__productImg" src="{{ asset('storage/' . $product->image) }}" alt="商品画像">
        <div class="show__productDetails">
          <h3 class="show__productDetailsName">{{ $product->name }}</h3> <!-- 商品名 -->
          <p class="show__productDetailsPrice">¥{{ number_format($product->price) }}</p> <!-- 商品価格 -->
        </div>
      </div>

      <div class="show__productChat">
        @if (isset($messages) && $messages->isNotEmpty()) <!-- メッセージがあるかチェック -->
          @foreach ($messages as $message)
            <div class="show__productChatItem">
              <div class="show__productChatUser">
                <img class="show__productChatUserImg" 
                  src="{{ $message->sender ? ($message->sender->profile_image ? asset('storage/' . $message->sender->profile_image) : asset('img/dammy2.png')) : asset('img/dammy2.png') }}" 
                  alt="ユーザー画像">
                <p class="show__productChatUserName">{{ $message->sender ? $message->sender->name : 'ユーザー名なし' }}</p>
              </div>
              <div class="show__productChatArea">
                <p>{{ $message->message }}</p> <!-- メッセージ内容 -->
                @if ($message->image)
                  <img src="{{ asset('storage/' . $message->image) }}" alt="Uploaded Image" class="show__productChatImage">
                @endif
              </div>
              <!-- 編集と削除のリンク -->
              @if ($message->sender_id === Auth::id()) <!-- ログインユーザーが送信者の場合 -->
                <div class="show__productChatLink">
                  <a href="{{ route('chat.edit', $message->id) }}">編集</a>
                  <!-- 削除フォーム -->
                  <form action="{{ route('chat.delete', $message->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE') <!-- DELETEメソッドを使用 -->
                    <button type="submit" onclick="return confirm('削除してもよろしいですか？');">削除</button>
                  </form>
                </div>
              @endif
            </div>
          @endforeach
        @else
          <p>メッセージはありません。</p>
        @endif
      </div>
      <!-- メッセージ送信フォーム -->
      <div class="show__productChatBottom">
        <form class="show__productChatBottomForm" action="{{ route('chat.send') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <!-- receiver_id をフォームに埋め込む -->
          <input type="hidden" name="receiver_id" value="{{ $seller->id }}">
          <input type="hidden" name="product_id" value="{{ $product->id }}">
          <input type="text" class="show__productChatBottomInput" name="message" placeholder="取引メッセージを記入してください">
          <!-- 画像選択 -->
          <!-- <input type="file" name="image" accept="image/*"> -->
          <label for="file-upload" class="show__productChatBottomBtn">
            画像を追加
          </label>
          <input type="file" id="file-upload" name="image" accept="image/*" style="display: none;">

          <button type="submit" class="show__submit">
            <img class="show__submitImg" src="{{ asset('img/chat-img.png') }}" alt="紙飛行機">
          </button>
        </form>
        <!-- メッセージのエラーメッセージ -->
        @error('message')
          <div class="error-message">{{ $message }}</div>
        @enderror
        <!-- 画像のエラーメッセージ -->
        @error('image')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
    </div>
  </section>

  <!-- 評価モーダル -->
  <div id="ratingModal" class="rating-modal">
    <div class="rating-modal-content">
      <span class="close-btn" onclick="closeModal()">&times;</span>
      <h3>取引を完了しました！</h3>
      <p>相手の評価をお願いします。</p>

      <form action="{{ route('rating.store') }}" method="POST">
        @csrf
        <input type="hidden" name="rated_id" value="{{ $seller->id }}"> <!-- 出品者のID -->
        
        <div class="rating">
          <label for="score">評価 (1~5):</label>
          <!-- 星の評価 -->
          <ul class="stars">
            <li data-value="1" class="star">&#9733;</li>
            <li data-value="2" class="star">&#9733;</li>
            <li data-value="3" class="star">&#9733;</li>
            <li data-value="4" class="star">&#9733;</li>
            <li data-value="5" class="star">&#9733;</li>
          </ul>
          <input type="hidden" name="score" id="score" value="0">
        </div>
        <button type="submit">評価する</button>
      </form>
    </div>
  </div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  // モーダルを開く
  function openModal() {
    document.getElementById("ratingModal").style.display = "block";
  }

  // モーダルを閉じる
  function closeModal() {
    document.getElementById("ratingModal").style.display = "none";
  }

  // モーダル外をクリックしたら閉じる
  window.onclick = function(event) {
    if (event.target == document.getElementById("ratingModal")) {
      closeModal();
    }
  }

  $(document).ready(function() {
    // 星をクリックしたときの処理
    $('.star').on('click', function() {
      var value = $(this).data('value'); // クリックされた星の評価値を取得
      $('#score').val(value); // 評価値をhiddenのinputにセット

      // 星を選択状態にする
      $('.star').removeClass('selected'); // すべての星を未選択に戻す
      $(this).prevAll().addClass('selected'); // クリックした星とその前の星を選択にする
      $(this).addClass('selected'); // クリックした星を選択状態にする
    });

    // マウスオーバーしたときの処理
    $('.star').on('mouseover', function() {
      var value = $(this).data('value');
      $('.star').each(function(index) {
        if (index < value) {
          $(this).addClass('hover'); // マウスオーバー時の星をハイライト
        } else {
          $(this).removeClass('hover');
        }
      });
    });

    // マウスアウトしたときの処理
    $('.star').on('mouseout', function() {
      $('.star').removeClass('hover');
    });
  });
</script>