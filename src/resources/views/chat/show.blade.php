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
          <!-- 出品者の画像を表示。画像がない場合は 'dammy2.png' を表示 -->
          <img class="show__headingImg" 
               src="{{ $seller->profile_image ? asset('storage/' . $product->profile_image) : asset('img/dammy2.png') }}" 
               alt="ユーザー画像">
               <h2 class="show__headingTitle">{{ $product->user->name }}さんとの取引画面</h2><!-- 出品者の名前 -->
        </div>
        <a class="show__headingBtn" href="#">取引を完了する</a>
      </div>
      <div class="show__product">
        <img class="show__productImg" src="{{ asset('storage/' . $product->image) }}" alt="商品画像">
        <div class="show__productDetails">
          <h3 class="show__productDetailsName">{{ $product->name }}</h3> <!-- 商品名 -->
          <p class="show__productDetailsPrice">¥{{ number_format($product->price) }}</p> <!-- 商品価格 -->
        </div>
      </div>

      <div class="show__productChat">
      @if ($messages ?? '' && is_array($messages ?? '') && count($messages ?? '') > 0)
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
        </div>
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
        <form action="{{ route('chat.send') }}" method="POST">
    @csrf
    <!-- receiver_id をフォームに埋め込む -->
    <input type="hidden" name="receiver_id" value="{{ $seller->id }}">
    <input type="hidden" name="product_id" value="{{ $product->id }}">  <!-- 商品IDを追加 -->
    <input type="text" class="show__productChatBottomInput" name="message" placeholder="メッセージを入力してください">
    <button type="submit" class="show__productChatBottomBtn">送信</button>
</form>


</div>







      </div>
    </div>
  </section>
@endsection

