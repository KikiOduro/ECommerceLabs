<?php
require_once __DIR__ . '/controllers/product_controller.php';

$id  = (int)($_GET['id'] ?? 0);
$row = $id ? view_single_product_ctr($id) : null;
if (!$row) {
    http_response_code(404);
    die('Product not found');
}

// Build a safe, public image URL (works locally and on /~akua.oduro/)
$raw = $row['product_image'] ?? '';
if (!$raw) {
    $img = 'https://via.placeholder.com/600x600?text=No+Image';
} else {
    if (preg_match('#^https?://#i', $raw)) {
        $img = $raw;                         // already absolute
    } else {
        $img = '/~egale-zoyiku/' . ltrim($raw, '/'); // make relative path public on live
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title><?= htmlspecialchars($row['product_title']) ?> - RadiantRoot</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px;
            background: #ffc0cb;
        }

        nav a {
            margin-left: 12px;
            padding: 8px 12px;
            border: 1px solid #000;
            border-radius: 999px;
            background: transparent;
            text-decoration: none;
            color: #000;
        }

        main {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px;
        }

        img.prod {
            width: 100%;
            height: auto;
            border: 1px solid #eee;
            border-radius: 12px;
            background: #fff;
        }

        .card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 18px;
        }

        .price {
            font-size: 20px;
            font-weight: bold;
            margin: 6px 0;
        }

        .muted {
            color: #666;
        }

        .btn {
            padding: 10px 14px;
            border: 1px solid #000;
            border-radius: 999px;
            background: transparent;
            cursor: pointer;
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

        .qty-input {
            width: 90px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <header>
        <div class="brand">
            <a href="index.php"><img src="assets/Radiant.png" alt="RadiantRoot Logo" class="logo"></a>
        </div>
        <nav>
            <a href="all_product.php">All Products</a>
            <a href="admin/cart.php">Cart <span id="cart-count" class="badge">0</span></a>
        </nav>
    </header>

    <main>
        <div class="card">
            <img class="prod" src="<?= htmlspecialchars($img) ?>" alt="">
        </div>

        <div class="card" data-product>
            <h2><?= htmlspecialchars($row['product_title']) ?></h2>
            <div class="price">GHS <?= number_format((float)$row['product_price'], 2) ?></div>
            <p class="muted"><?= htmlspecialchars($row['cat_name']) ?> • <?= htmlspecialchars($row['brand_name']) ?></p>
            <p><?= nl2br(htmlspecialchars($row['product_desc'])) ?></p>
            <p class="muted">Keywords: <?= htmlspecialchars($row['product_keywords']) ?></p>

            <!-- Add to Cart controls (hooked by cart.js) -->
            <div style="margin-top:10px;">
                <button class="btn add-to-cart" data-id="<?= (int)$row['product_id'] ?>">Add to Cart</button>
                <input type="number" class="qty-input" value="1" min="1" />
            </div>
        </div>
    </main>

    <!-- JS deps + cart wiring -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Endpoints for cart.js (root-relative)
        window.CART_ENDPOINTS = {
            add: 'actions/add_to_cart_action.php',
            remove: 'actions/remove_from_cart_action.php',
            update: 'actions/update_quantity_action.php',
            empty: 'actions/empty_cart_action.php',
            fetch: 'actions/get_cart_action.php'
        };
        // Live prefix for relative images (used by cart.js’ imgUrl too)
        window.PUBLIC_PREFIX = window.PUBLIC_PREFIX || '/~egale-zoyiku/';
    </script>
    <script src="js/cart.js"></script>
</body>

</html>