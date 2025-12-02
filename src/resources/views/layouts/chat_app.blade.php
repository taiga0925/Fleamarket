<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>coachtechフリマ</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  @yield('css')
</head>

<body>
  <header class="header">
    <div class="header__inner">
      <div class="header-logo-only">
        <a class="header__logo" href="/">
            <img src="{{ asset('img/logo.svg') }}" alt="coachtech">
        </a>
      </div>
    </div>
  </header>

  <main>
    @yield('content')
  </main>
</body>

</html>
