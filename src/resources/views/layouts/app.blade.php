<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>coachtechフリマ</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @yield('css')
</head>

<body>
  <header class="header">
    <div class="header__inner">
      <div class="header-utilities">

        <div class="header-utility">
            <a class="header__logo" href="/">
                <img src="{{ asset('img/logo.svg' ) }}" alt="error">
            </a>
        </div>

        <div class="header-utility">
            <form class="header__search" action="/search" method="get">
                @csrf
                <input class="search__item" type="text" name="searchText" placeholder="なにをお探しですか？">
            </form>
        </div>

        <div class="header-utility">
            <nav>
                <ul class="header-nav">
                @if (Auth::check())
                    <li class="header-nav__item">
                        <form class="form" action="/logout" method="post">
                        @csrf
                            <button class="header-nav__button">ログアウト</button>
                        </form>
                    </li>
                @else
                    <li class="header-nav__item">
                        <form class="form" action="/login" method="get">
                        @csrf
                            <button class="header-nav__button">ログイン</button>
                        </form>
                    </li>
                @endif
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="/mypage">マイページ</a>
                    </li>
                    <li class="header-nav__item">
                        <form class="form" action="/sell" method="get">
                        @csrf
                            <button class="header-nav__button--store">出品</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
      </div>
    </div>
  </header>

  <main>
    @yield('content')
  </main>
</body>

</html>
