@extends('layouts.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
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
  <section class="sell wrapper">
    <h2 class="sectionTitle">商品の出品</h2>
    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
        <div class="sell__item">
          <label class="sell__itemTitle" for="image">商品画像</label>
          <div id="drop-zone" class="sell__dropZone">
              <p class="sell__dropZoneText"><button type="button" id="file-select" class="sell__fileButton">画像を選択する</button></p>
              <input type="file" name="image" id="image" class="sell__dropZoneInput" accept="image/*">
              <div id="preview-container" class="sell__previewContainer"></div> <!-- プレビュー画像用 -->
          </div>
          @error('image')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>
        <h3 class="sell__SubTitle">商品の詳細</h3>
        <div class="sell__item">
          <label class="sell__itemTitle">カテゴリー</label>
          <ul class="sell__tag">
            @php
              $categories = ['ファッション', '家電', 'インテリア', 'レディース', 'メンズ', 'コスメ', '本', 'ゲーム', 'スポーツ', 'キッチン', 'ハンドメイド', 'アクセサリー', 'おもちゃ', 'ベビー・キッズ'];
            @endphp
            @foreach ($categories as $index => $category)
              <li class="sell__tagItem" data-index="{{ $index }}">
                <input type="checkbox" id="category_{{ $index }}" name="categories[]" value="{{ $category }}" 
                  @if(in_array($category, old('categories', []))) checked @endif hidden>
                <label class="sell__tagItemLabel" for="category_{{ $index }}">{{ $category }}</label>
              </li>
            @endforeach
          </ul>
          @error('categories')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>
        <div class="sell__item">
          <label class="sell__itemTitle">商品の状態</label>
          <div class="sell__select">
            <select name="condition" id="condition" class="sell__selectDrop">
              <option value="" disabled selected>選択してください</option>
              <option value="良好" @if(old('condition') == '良好') selected @endif>良好</option>
              <option value="目立った傷や汚れなし" @if(old('condition') == '目立った傷や汚れなし') selected @endif>目立った傷や汚れなし</option>
              <option value="やや傷や汚れあり" @if(old('condition') == 'やや傷や汚れあり') selected @endif>やや傷や汚れあり</option>
              <option value="状態が悪い" @if(old('condition') == '状態が悪い') selected @endif>状態が悪い</option>
            </select>
          </div>
          @error('condition')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>
        <h3 class="sell__SubTitle">商品名と説明</h3>
        <div class="sell__item">
          <label class="sell__itemTitle" for="name">商品名</label>
          <input type="text" name="name" class="sell__itemInput" id="name" value="{{ old('name') }}">
          @error('name')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>
        <div class="sell__item">
          <label class="sell__itemTitle">商品の説明</label>
          <textarea name="description" class="sell__itemArea" id="description">{{ old('description') }}</textarea>
          @error('description')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>
        <div class="sell__item">
          <label class="sell__itemTitle">販売価格</label>
          <input type="text" name="price" class="sell__itemPrice" id="price" value="¥{{ old('price') }}">
          @error('price')
            <p class="form__error">{{ $message }}</p>
          @enderror
        </div>
        <input class="formBtnRed" type="submit" value="出品する" />
    </form>
  </section>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const dropZone = document.getElementById('drop-zone');
      const fileInput = document.getElementById('image');
      const fileSelect = document.getElementById('file-select');
      const previewContainer = document.getElementById('preview-container');

      // ドラッグ＆ドロップエリアの動作
      dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.add('dragover');
      });

      dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('dragover');
      });

      dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files; // inputにファイルを設定
            showPreview(files[0]); // プレビュー表示
        }
      });

      // 「画像を選択する」でファイル選択を開く
      fileSelect.addEventListener('click', () => {
        fileInput.click();
      });

      // ファイル選択時にプレビュー表示を更新
      fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            showPreview(fileInput.files[0]); // プレビュー表示
        }
      });

      // プレビュー画像を表示する関数
      function showPreview(file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            previewContainer.innerHTML = ''; // プレビューをクリア
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = 'プレビュー画像';
            previewContainer.appendChild(img);
        };
        reader.readAsDataURL(file);
      }
    });

    document.addEventListener('DOMContentLoaded', function () {
      const tagItems = document.querySelectorAll('.sell__tagItem');

      tagItems.forEach((item) => {
        const input = item.querySelector('input');
        const label = item.querySelector('label');

        // チェックボックスの選択状態を切り替える
        item.addEventListener('click', () => {
          input.checked = !input.checked;
          if (input.checked) {
            item.classList.add('selected');
          } else {
            item.classList.remove('selected');
          }
        });

        // 初期状態で選択済みの場合はスタイル適用
        if (input.checked) {
          item.classList.add('selected');
        }
      });
    });

    document.addEventListener('DOMContentLoaded', function () {
      const form = document.querySelector('form');
      const priceInput = document.getElementById('price');

      // フォーム送信時に¥を削除
      form.addEventListener('submit', function(event) {
        let price = priceInput.value;

        // 価格の先頭の「¥」を削除
        price = price.replace(/[^\d]/g, '');  // 数字以外の文字（¥記号含む）を削除

        // // 数値でない場合、エラーを表示
        // if (isNaN(price) || price === '') {
        //   event.preventDefault();  // フォーム送信を停止
        //   alert('販売価格は有効な整数で入力してください');
        //   return;
        // }

        priceInput.value = price;  // 修正された価格をフォームに設定
        console.log('送信する価格:', price);  // コンソールに送信される価格を表示
      });

      // フォーカス時に¥を削除
      priceInput.addEventListener('focus', () => {
        let price = priceInput.value;
        if (price.startsWith('¥')) {
          priceInput.value = price.slice(1).trim();  // ¥を削除
        }
      });

      // フォーカスアウト時に¥を追加
      priceInput.addEventListener('blur', () => {
        let price = priceInput.value;
        if (price && !price.startsWith('¥')) {
          priceInput.value = '¥' + price.trim();  // ¥を追加
        }
      });

      // 入力中の動作を制御（数値のみ入力を許可）
      priceInput.addEventListener('input', () => {
        priceInput.value = priceInput.value.replace(/[^\d]/g, '');  // 数字以外を削除
      });
    });
  </script>
@endsection