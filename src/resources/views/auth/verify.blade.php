<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtechフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
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
    <div class="header__wrap">
        <div class="header__text" >
            {{ ('登録していただいたメールアドレスに認証メールを送付しました') }}
        </div>
        <div class="header__text" >
            {{ ('メール認証を完了してください') }}
        </div>

        <a class="input-link " href="https://mailtrap.io/inboxes/">
            {{ ('確認はこちらから') }}
        </a>

        <form class="form__item" action="{{ route('verification.send') }}" method="post">
            @csrf
            <button type="submit" class=" form__input-button">
                {{ ('確認メールを再送する') }}
            </button>
        </form>
    </div>
    </main>
