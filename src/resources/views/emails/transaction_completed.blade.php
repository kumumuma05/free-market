<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>取引完了のお知らせ</title>
</head>
<body>
    <h1>取引が完了しました</h1>

    <p>
        商品「{{ $purchase->item->product_name }}」の取引が完了しました。
    </p>

    <p>
        取引ID：{{ $purchase->id }}
    </p>

    <p>
        評価者への評価をお願いします。引き続きサービスをご利用ください。
    </p>
</body>
</html>
