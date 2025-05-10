@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection
@section('btn')
  <form method="GET" action="{{ route('products.index') }}" class="header__search header__searchPC">
    <input type="hidden" name="page" value="{{ $page }}">
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
    <input type="hidden" name="page" value="{{ $page }}">
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
  <section class="mypage">
    <div class="maypage__info">
      <div class="mypage__infoUser">
        <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('img/dammy2.png') }}" class="mypage__infoImg" alt="ユーザー画像">
        <div>
          <p class="mypage__infoName">{{ $user->name }}</p>
          <ul class="stars">
            @for ($i = 1; $i <= 5; $i++)
                <li class="star {{ $i <= round($user->averageRating()) ? 'selected' : '' }}">&#9733;</li>
            @endfor
          </ul>
        </div>
      </div>
      <a href="{{ route('profile.edit') }}" class="mypage__infoBtn">プロフィールを編集</a>
    </div>
    <div class="mypage__select">
      <div class="mypage__tab wrapper">
        <p class="mypage__tabList">
          <a href="{{ route('mypage', ['page' => 'sell']) }}" class="mypage__tabListLink {{ $page === 'sell' ? 'active' : '' }}">
            出品した商品
          </a>
        </p>
        <p class="mypage__tabList">
          <a href="{{ route('mypage', ['page' => 'buy']) }}" class="mypage__tabListLink {{ $page === 'buy' ? 'active' : '' }}">
            購入した商品
          </a>
        </p>
        
        <p class="mypage__tabList">
          <a href="{{ route('mypage', ['page' => 'trading']) }}" class="mypage__tabListLink {{ $page === 'trading' ? 'active' : '' }}">
            @php
              // 現在ログイン中のユーザーIDを取得
              $user_id = Auth::id();
              
              // 未読メッセージの件数をカウント
              $unreadMessagesCount = App\Models\ChatMessage::where('receiver_id', $user_id)
                                                            ->where('is_read', 0)
                                                            ->count();
            @endphp
            取引中の商品<span class="mypage__tabListNum">{{ $unreadMessagesCount ?? 'なし' }}</span>
          </a>
        </p>
      </div>
    </div>
    <div class="mypage__contents wrapper">
      @if ($page === 'sell')
        <!-- 出品した商品を表示 -->
        @if ($products->isEmpty())
        <p>出品した商品がありません。</p>
      @else
        @foreach ($products as $product)
            <div class="mypage__item">
                <a class="mypage__itemLink" href="{{ route('item.show', ['id' => $product->id]) }}" 
                @if ($product->is_sold) style="pointer-events: none;" @endif>
                    <img class="mypage__itemImg" src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    <p class="mypage__itemName">{{ $product->name }}</p>
                    @if ($product->is_sold) <!-- 購入済みの商品 -->
                        <div class="sold__itemMask"></div>
                        <span class="sold-label">Sold</span>
                    @endif
                </a>
            </div>
        @endforeach
      @endif
      @elseif ($page === 'buy')
        <!-- 購入した商品を表示 -->
        @if ($products->isEmpty())
            <p>購入した商品がありません。</p>
        @else
            @foreach ($products as $product)
                <div class="mypage__item">
                    <a class="mypage__itemLink" href="{{ route('item.show', ['id' => $product->id]) }}" style="pointer-events: none;">
                        <img class="mypage__itemImg" src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                        <p class="mypage__itemName">{{ $product->name }}</p>
                        <div class="sold__itemMask"></div>
                        <span class="sold-label">Sold</span> <!-- 購入済み商品には「Sold」を表示 -->
                    </a>
                </div>
            @endforeach
        @endif
        @elseif ($page === 'trading')
      @if ($products->isEmpty())
        <p>取引中の商品がありません。</p>
      @else
        <!-- @foreach ($products as $product)
            <div class="mypage__item">
                <a class="mypage__itemLink" href="{{ route('chat.show', ['product_id' => $product->product_id]) }}">
                    <img class="mypage__itemImg" src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    <p class="mypage__itemName">{{ $product->name }}</p>
                </a>
            </div>
        @endforeach -->
        
      @php
    // 取引中の商品を最新メッセージ順で並べ替え
    $sortedProducts = $products->sortByDesc(function ($tradingProduct) {
        // 各商品ごとの最新メッセージの作成日時を取得
        $latestMessage = App\Models\ChatMessage::where('product_id', $tradingProduct->product_id)
                                                ->where('receiver_id', Auth::id())
                                                ->orderBy('created_at', 'desc')
                                                ->first();  // 最新のメッセージを取得

        // 最新メッセージがあればその作成日時を返す、なければ商品作成日時を返す
        return $latestMessage ? $latestMessage->created_at : $tradingProduct->created_at;
    });
@endphp

@foreach ($sortedProducts as $tradingProduct)
    @php
        // 並べ替えた後で未読メッセージ数を取得
        $unreadMessagesCount = App\Models\ChatMessage::where('product_id', $tradingProduct->product_id)
                                                      ->where('receiver_id', Auth::id())
                                                      ->where('is_read', 0)
                                                      ->count();
    @endphp

    <div class="mypage__item">
        <a class="mypage__itemLink" href="{{ route('chat.show', ['product_id' => $tradingProduct->product_id]) }}">
            <div class="mypage__itemThumbnails">
                <img class="mypage__itemImg" src="{{ asset('storage/' . $tradingProduct->product->image) }}" alt="{{ $tradingProduct->product->name }}">
                <!-- 未読メッセージ数を表示 -->
                @if ($unreadMessagesCount > 0)
                    <p class="mypage__itemUnread">{{ $unreadMessagesCount }}</p>
                @endif
            </div>
            <p class="mypage__itemName">{{ $tradingProduct->product->name }}</p>
        </a>
    </div>
@endforeach




      @endif
    @endif




</div>

  </section>
@endsection