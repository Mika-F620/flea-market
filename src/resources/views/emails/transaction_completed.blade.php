<!-- resources/views/emails/transaction_completed.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>取引完了のお知らせ</title>
</head>
<body>
    <h1>{{ $seller->name }}さん、取引が完了しました</h1>

    <p>商品名: {{ $product->name }}</p>
    <p>取引が完了しました。ご確認ください。</p>

    <p>ありがとうございました！</p>
</body>
</html>
