@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
  <section class="show">
    <aside class="show__sidebar">
      <p class="show__sidebarTitle">その他の取引</p>
      <div class="show__sidebarDetails">
        @foreach ($other_products as $tradingProduct)
          <a href="{{ route('chat.show', ['product_id' => $tradingProduct->product_id]) }}" class="show__sidebarDetailsName">
            {{ $tradingProduct->product->name }}
          </a>
        @endforeach
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
        @if ($tradingProduct && $tradingProduct->status != '取引完了')
          @php
            // 評価が既にされているかどうかを確認
            $existingRating = App\Models\Rating::where('rater_id', Auth::id())
                                                ->where('rated_id', $seller->id)
                                                ->where('product_id', $product->id)
                                                ->first();
          @endphp
          @if ($existingRating)
            <!-- すでに評価がされている場合 -->
            <p>取引は完了しました。</p>
          @else
            <!-- 評価がされていない場合 -->
            <a href="javascript:void(0);" class="show__headingBtn" onclick="openModal()">取引を完了する</a>
          @endif
        @else
          <!-- 取引が完了した場合 -->
          <p>取引は完了しました。</p>
        @endif
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
          <input type="text" class="show__productChatBottomInput" name="message" placeholder="取引メッセージを記入してください" value="{{ old('message') }}">
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
      <h3 class="rating-modal-title">取引を完了しました！</h3>
      <p class="rating-modal-text">今回の取引相手はどうでしたか？</p>

      <form action="{{ route('rating.store') }}" method="POST">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        <div class="rating">
          <ul class="stars">
            <li data-value="1" class="star">&#9733;</li>
            <li data-value="2" class="star">&#9733;</li>
            <li data-value="3" class="star">&#9733;</li>
            <li data-value="4" class="star">&#9733;</li>
            <li data-value="5" class="star">&#9733;</li>
          </ul>
          <input type="hidden" name="score" id="score" value="0">
        </div>
        <button class="rating-modal-btn" type="submit">送信する</button>
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
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.querySelector('input[name="message"]');
    const chatForm = document.querySelector('.show__productChatBottomForm');

    // メッセージ入力の変更時にlocalStorageに保存
    messageInput.addEventListener('input', function() {
      localStorage.setItem('chatMessage', messageInput.value);
    });

    // ページが読み込まれたときにlocalStorageからメッセージを復元
    const savedMessage = localStorage.getItem('chatMessage');
    if (savedMessage) {
      messageInput.value = savedMessage;
    }

    // フォーム送信時にlocalStorageをクリア
    chatForm.addEventListener('submit', function() {
      localStorage.removeItem('chatMessage'); // メッセージ送信後、ローカルストレージを削除
    });
  });
</script>
<script>
$(document).ready(function() {
  // 評価フォームが送信されたときにイベントをキャッチ
  $('form[action="{{ route('rating.store') }}"]').on('submit', function(event) {
    event.preventDefault(); // フォーム送信を一時的に停止
    
    // フォームからスコアを取得
    var score = $('#score').val();
    
    // Ajaxリクエストを送信
    $.ajax({
      url: '{{ route('transaction.sendRatingEmail') }}',
      method: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        product_id: '{{ $product->id }}',
        seller_id: '{{ $seller->id }}',
        score: score,
        message: '評価を送信しました'
      },
      success: function(response) {
        console.log('メール送信成功:', response);
        
        // メール送信後にフォーム送信を実行
        if(response.success) {
          // メール送信が成功したらフォームを送信
          $('form[action="{{ route('rating.store') }}"]').off('submit').submit();  // イベントハンドラを解除してからフォーム送信
        }
      },
      error: function(xhr, status, error) {
        console.log('メール送信エラー:', error);
        alert('メール送信に失敗しました');
      }
    });
  });
});


</script>