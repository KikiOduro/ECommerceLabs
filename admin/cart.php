<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/settings/core.php';
if (!isLoggedIn()) {
    header('Location: login/login.php');
    exit;
}

$logged_in = isLoggedIn();
$is_admin  = isAdmin();
$user_name = $_SESSION['user_name'] ?? 'User';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Cart • RadiantRoot</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --pink: #ffc0cb;
            --text: #333;
            --muted: #555;
            --border: #000;
            --white: #fff;
            --bg: #f4f6f8;
            --shadow: 0 2px 4px rgba(0, 0, 0, .1);
        }

        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
        }

        header {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 30px;
            background: var(--pink);
            box-shadow: var(--shadow);
        }

        nav a,
        nav form button {
            margin-left: 15px;
            padding: 10px 14px;
            text-decoration: none;
            border: 1px solid var(--border);
            border-radius: 400px;
            background: transparent;
            color: var(--border);
            font-size: 14px;
            cursor: pointer;
            transition: .2s;
        }

        nav a:hover,
        nav form button:hover {
            background: #000;
            color: #fff;
        }

        main {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h1 {
            margin: 0 0 16px;
        }

        .toolbar {
            display: flex;
            gap: 10px;
            margin: 10px 0 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 400px;
            background: transparent;
            cursor: pointer;
        }

        .btn:hover {
            background: #000;
            color: #fff;
        }

        #cart-items .cart-row {
            display: grid;
            grid-template-columns: 64px 1fr auto auto auto;
            gap: 12px;
            align-items: center;
            padding: 12px 0;
            border-top: 1px solid #eee;
        }

        #cart-items .cart-row:first-child {
            border-top: none;
        }

        .thumb {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        .info .title {
            font-weight: 600;
        }

        .info .price {
            color: var(--muted);
            margin-top: 4px;
        }

        .qty {
            width: 80px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        .totals {
            margin-top: 20px;
            padding: 16px;
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
        }

        .badge {
            display: inline-block;
            min-width: 22px;
            padding: 2px 6px;
            border-radius: 12px;
            background: #000;
            color: #fff;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <header>
        <div style="display:flex;align-items:center;gap:10px;">
            <img src="assets/logo.png" alt="RadiantRoot" style="height:28px; width:auto;">
        </div>
        <nav>
            <a href="all_product.php">Shop <span id="cart-count" class="badge">0</span></a>
            <?php if ($is_admin): ?>
                <a href="admin/category.php">Category</a>
                <a href="admin/brand.php">Brand</a>
                <a href="admin/product.php">Product</a>
            <?php endif; ?>
            <form action="login/logout.php" method="post"><button type="submit">Logout</button></form>
        </nav>
    </header>

    <main>
        <h1>Your Cart</h1>

        <div class="toolbar">
            <a class="btn" href="all_product.php">Continue Shopping</a>
            <a class="btn" href="checkout.php">Proceed to Checkout</a>
            <button class="btn" id="empty-cart">Empty Cart</button>
        </div>

        <div id="cart-items">
            <p>Loading cart…</p>
        </div>

        <div class="totals">
            <div>Subtotal: GHS <strong id="cart-subtotal">0.00</strong></div>
            <div>Total: GHS <strong id="cart-total">0.00</strong></div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // set endpoints for cart.js (root-relative)
        window.CART_ENDPOINTS = {
            add: 'actions/add_to_cart_action.php',
            remove: 'actions/remove_from_cart_action.php',
            update: 'actions/update_quantity_action.php',
            empty: 'actions/empty_cart_action.php',
            fetch: 'actions/get_cart_action.php'
        };
        // if your live server uses /~username/, set PUBLIC_PREFIX once:
        window.PUBLIC_PREFIX = window.PUBLIC_PREFIX || '/~egale-zoyiku/';
    </script>
    <script src="js/cart.js"></script>
</body>

</html>