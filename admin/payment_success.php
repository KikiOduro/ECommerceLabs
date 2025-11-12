<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/settings/core.php';
if (!isLoggedIn()) {
    header('Location: login/login.php');
    exit;
}

$ref = $_GET['ref'] ?? '';
$oid = $_GET['order_id'] ?? '';
$amt = $_GET['amount'] ?? '';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Payment Success</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .card {
            max-width: 560px;
            margin: auto;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Thank you! 🎉</h2>
        <p>Your order was placed successfully.</p>
        <p><strong>Order ID:</strong> <?= htmlspecialchars($oid) ?></p>
        <p><strong>Reference:</strong> <?= htmlspecialchars($ref) ?></p>
        <?php if ($amt !== ''): ?><p><strong>Amount:</strong> GHS <?= htmlspecialchars($amt) ?></p><?php endif; ?>
        <p><a href="all_product.php">Continue Shopping</a></p>
    </div>
</body>

</html>