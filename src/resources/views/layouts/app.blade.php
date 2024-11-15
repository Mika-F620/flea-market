<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>coachtech フリマアプリ</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
  @yield('css')
</head>
<body>
  <header class="header">
    <div class="header__contents">
      <h1 class="header__title">
        <img class="header__logo" src="{{ asset('img/logo.svg') }}" alt="COACHTECH">
      </h1>
      <input type="text" class="header__search" placeholder="なにをお探しですか？">
      <div class="header__btn">
        <p class="header__btnItem">ログイン</p>
        <p class="header__btnItem">マイページ</p>
        <p class="header__btnItem">出品</p>
      </div>
    </div>
  </header>
  <main>
    @yield('content')
  </main>
  </body>
</html>