<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>取引完了のお知らせ</title>
</head>
<body>
    <p>{{ $item->user->name }} 様</p>

    <p>以下の商品の取引が完了し、評価が届きました。</p>

    <hr>
    <p><strong>商品名:</strong> {{ $item->item }}</p>
    <p><strong>評価:</strong> {{ $rating->rating }} / 5</p>
    <p><strong>コメント:</strong><br>
    {!! nl2br(e($rating->comment)) !!}</p>
    <hr>

    <p>ご利用ありがとうございました。</p>
</body>
</html>
