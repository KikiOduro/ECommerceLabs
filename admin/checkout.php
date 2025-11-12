<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once '../settings/core.php';
if (!isLoggedIn()) {
    header('Location: login/login.php');
    exit;
}

$logged_in = isLoggedIn();
$is_admin  = isAdmin();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Checkout • RadiantRoot</title>
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

    /* ✨ NEW: make the nav itself a flex row */
    nav {
        display: flex;
        align-items: center;
        gap: 15px;           /* controls spacing between items */
    }

    /* remove margin-left; let gap handle spacing */
    nav a,
    nav form button {
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

    /* keep the logout form inline so it behaves like a link */
    nav form {
        margin: 0;
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
        <div style="display:flex;align-items:center;gap:10px;">
            <img src="../assets/Radiant.png" alt="RadiantRoot" class="logo">
        </div>
        <nav>
            <a href="../all_product.php">Shop</a>
            <a href="../cart.php">Cart <span id="cart-count" class="badge">0</span></a>
            <?php if ($is_admin): ?>
                <a href="../admin/category.php">Category</a>
                <a href="../admin/brand.php">Brand</a>
                <a href="../admin/product.php">Product</a>
            <?php endif; ?>
            <form action="login/logout.php" method="post"><button type="submit">Logout</button></form>
        </nav>
    </header>

    <main>
        <h1>Checkout</h1>

        <div class="summary">
            <h3>Order Summary</h3>
            <div id="checkout-items">
                <p class="muted">Loading…</p>
            </div>
            <div class="totals">
                Subtotal: GHS <strong id="co-subtotal">0.00</strong><br>
                Total: GHS <strong id="co-total">0.00</strong>
            </div>
            <div style="margin-top:14px; display:flex; gap:10px;">
                <a href="cart.php" class="btn">Back to Cart</a>
                <button id="simulate-pay" class="btn">Simulate Payment</button>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // endpoints for both summary fetch + process checkout
        const CART_FETCH_URL = '../actions/get_cart_action.php';
        window.CHECKOUT_ENDPOINT = '../actions/process_checkout_action.php';
        window.PUBLIC_PREFIX = window.PUBLIC_PREFIX || '/~egale-zoyiku/';

        function escapeHtml(s) {
            return (s || '').replace(/[&<>"']/g, m => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [m]));
        }

        function imgUrl(p) {
            if (!p) return 'https://via.placeholder.com/64';
            if (/^https?:\/\//i.test(p)) return p;
            return window.PUBLIC_PREFIX + p.replace(/^\/+/, '');
        }

        // Render a simple summary table
        function loadSummary() {
            $.getJSON(CART_FETCH_URL, function(res) {
                const $wrap = $('#checkout-items');
                $wrap.empty();
                if (!res || res.status !== 'success') {
                    $wrap.html('<p class="muted">Could not load cart.</p>');
                    return;
                }
                const items = res.data || [];
                if (!items.length) {
                    $wrap.html('<p>Your cart is empty.</p>');
                    $('#co-subtotal,#co-total').text('0.00');
                    return;
                }

                let subtotal = 0;
                items.forEach(it => {
                    const price = Number(it.product_price || 0),
                        qty = Number(it.qty || 0);
                    const line = price * qty;
                    subtotal += line;
                    $wrap.append(`
            <div class="row">
              <img class="thumb" src="${imgUrl(it.product_image)}" alt="">
              <div>
                <div><strong>${escapeHtml(it.product_title)}</strong></div>
                <div class="muted">Qty: ${qty}</div>
              </div>
              <div>GHS ${price.toFixed(2)}</div>
              <div><strong>GHS ${line.toFixed(2)}</strong></div>
            </div>
          `);
                });
                $('#co-subtotal').text(subtotal.toFixed(2));
                $('#co-total').text(subtotal.toFixed(2));
            }).fail(() => $('#checkout-items').html('<p class="muted">Could not load cart.</p>'));
        }

        $(loadSummary);
    </script>
    <script src="js/checkout.js"></script>
</body>

</html>