<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>coachtechフリマ</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <a class="header__logo" href="/">
                    <img src="{{ asset('img/logo.svg' ) }}" alt="error">
                </a>
            </div>
        </div>
    </header>

    <main>
        <div class="register-form__content">
            <div class="register-form__heading">
                <h2>会員登録</h2>
            </div>

            <form class="form" action="/register" method="post">
            @csrf
                <div class="form__group">
                    <div class="form__group-title">
                        <span class="form__label-item">ユーザー名</span>
                    </div>
                    <div class="form__group-content">
                        <div class="form__input-text">
                            <input type="text" name="name" value="{{ old('name') }}" />
                        </div>
                        <div class="form__error">
                            @error('name')
                            {{ $message }}
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form__group">
                    <div class="form__group-title">
                        <span class="form__label-item">メールアドレス</span>
                    </div>
                    <div class="form__group-content">
                        <div class="form__input-text">
                            <input type="email" name="email" value="{{ old('email') }}" />
                        </div>
                        <div class="form__error">
                            @error('email')
                            {{ $message }}
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form__group">
                    <div class="form__group-title">
                        <span class="form__label-item">パスワード</span>
                    </div>
                    <div class="form__group-content">
                        <div class="form__input-text">
                            <input type="password" name="password" />
                        </div>
                        <div class="form__error">
                            @error('password')
                            {{ $message }}
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form__group">
                    <div class="form__group-title">
                        <span class="form__label-item">確認用パスワード</span>
                    </div>
                    <div class="form__group-content">
                        <div class="form__input-text">
                            <input type="password" name="password_confirmation" />
                        </div>
                    </div>
                </div>

                <div class="form__button">
                    <button class="form__button-submit" type="submit">登録する</button>
                </div>
            </form>

            <div class="login__link">
                <a class="login__button-submit" href="/login">ログインはこちら</a>
            </div>
        </div>
    </main>
</body>

</html>
