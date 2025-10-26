<?php
require_once __DIR__ . '/controllers/product_controller.php';

$id = (int)($_GET['id'] ?? 0);
$row = $id ? view_single_product_ctr($id) : null;
if (!$row) {
    http_response_code(404);
    die('Product not found');
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($row['product_title']) ?> - RadiantRoot</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px;
            background: #ffc0cb
        }

        nav a {
            margin-left: 12px;
            padding: 8px 12px;
            border: 1px solid #000;
            border-radius: 999px;
            background: transparent;
            text-decoration: none;
            color: #000
        }

        main {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px
        }

        img {
            width: 100%;
            height: auto;
            border: 1px solid #eee;
            border-radius: 12px;
            background: #fff
        }

        .card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 18px
        }

        .price {
            font-size: 20px;
            font-weight: bold;
            margin: 6px 0
        }

        .muted {
            color: #666
        }

        .btn {
            padding: 10px 14px;
            border: 1px solid #000;
            border-radius: 999px;
            background: transparent;
            cursor: pointer
        }

        .brand {
            display: flex;
            align-items: center;
            margin-left: 25px;

        }

        .logo {
            height: 60px;
            width: auto;
            object-fit: contain;
            transform: scale(1.9);
            transform-origin: left center;
            margin-left: 15px;

        }
    </style>
</head>

<body>
    <header>
        <div class="brand">
            <a href="index.php">
                <img src="assets/Radiant.png" alt="RadiantRoot Logo" class="logo">
            </a>
        </div>

        <nav>
            <a href="all_product.php">All Products</a>
        </nav>
    </header>

    <main>
        <div class="card">
            <img src="<?= $row['product_image'] ?: 'https://via.placeholder.com/600x600?text=No+Image' ?>" alt="">
        </div>
        <div class="card">
            <h2><?= htmlspecialchars($row['product_title']) ?></h2>
            <div class="price">$<?= number_format((float)$row['product_price'], 2) ?></div>
            <p class="muted"><?= htmlspecialchars($row['cat_name']) ?> • <?= htmlspecialchars($row['brand_name']) ?></p>
            <p><?= nl2br(htmlspecialchars($row['product_desc'])) ?></p>
            <p class="muted">Keywords: <?= htmlspecialchars($row['product_keywords']) ?></p>
            <input type="hidden" value="<?= (int)$row['product_id'] ?>" />
            <button class="btn">Add to Cart</button>
        </div>
    </main>
</body>

</html>