<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/settings/core.php';
require_once __DIR__ . '/settings/db_class.php';
require_once __DIR__ . '/controllers/product_controller.php';

$logged_in = isLoggedIn();
$is_admin  = isAdmin();
$user_name = $logged_in ? ($_SESSION['user_name'] ?? 'User') : '';

$db = new db_connection();
$db->db_conn();
$cats = $db->db_fetch_all("
  SELECT c.cat_id, c.cat_name
  FROM categories c
  WHERE EXISTS (
    SELECT 1 FROM products p WHERE p.product_cat = c.cat_id
  )
  ORDER BY c.cat_name ASC
");


$featured = view_all_products_ctr(8, 0);

function e($s)
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>RadiantRoot — Healthy Skin, Happy You</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <style>
        :root {
            --pink: #ffc9d6;
            --pink-strong: #ff9fb6;
            --text: #222;
            --muted: #666;
            --border: #000;
            --white: #fff;
            --bg: #f7f7f8;
            --shadow: 0 6px 24px rgba(0, 0, 0, .06);
            --ring: 0 0 0 3px rgba(0, 0, 0, .08) inset;
            --pill-radius: 999px;
            --radius: 16px;
        }

        * {
            box-sizing: border-box;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
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
            padding: 18px 28px;
            background: var(--pink);
            box-shadow: var(--shadow);
        }

        .nav-layout {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            flex-wrap: wrap;
        }

        .left-nav,
        .center-nav,
        .right-nav {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .center-nav {
            flex: 1;
            justify-content: center;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-form input[type="text"] {
            padding: 10px 12px;
            border: 1px solid #111;
            border-radius: 400px;
            background: #fff;
            font-size: 14px;
            width: 280px;
        }

        .search-form button {
            padding: 10px 14px;
            border-radius: 400px;
            border: 1px solid #111;
            background: #111;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
        }

        .search-form button:hover {
            background: #000;
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



        .brand img {
            display: block;
        }


        .left-nav {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: 40px;

        }



        nav {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        nav a,
        nav form button {
            padding: 10px 14px;
            text-decoration: none;
            border: 1px solid #111;
            border-radius: var(--pill-radius);
            background: transparent;
            color: #111;
            font-size: 14px;
            cursor: pointer;
            transition: .2s all ease-in-out;
        }

        nav a:hover,
        nav form button:hover {
            background: #111;
            color: #fff;
        }

        nav form {
            display: flex;
            gap: 8px;
            margin: 0;
        }

        nav input[type="text"] {
            padding: 10px 12px;
            border: 1px solid #111;
            border-radius: var(--pill-radius);
            background: #fff;
            font-size: 14px;
            width: 180px;
        }

        main {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 20px 60px;
        }

        /* ---------- HERO ---------- */
        .hero {
            margin: 28px 0 34px;
            border-radius: 24px;
            overflow: hidden;
            background: #ffeef2;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 0;
            min-height: 340px;
            box-shadow: var(--shadow);
        }

        .hero .copy {
            padding: 42px 40px 40px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .eyebrow {
            font-weight: 700;
            color: #c4145a;
            letter-spacing: .12em;
            text-transform: uppercase;
            font-size: 12px;
        }

        .hero h1 {
            margin: 10px 0 8px;
            font-size: 36px;
            line-height: 1.12;
        }

        .hero p {
            color: #444;
            max-width: 560px;
        }

        .ctaRow {
            display: flex;
            gap: 10px;
            margin-top: 18px;
        }

        .btn {
            padding: 12px 16px;
            border-radius: var(--pill-radius);
            border: 1px solid #111;
            background: #111;
            color: #fff;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
        }

        .btn.secondary {
            background: transparent;
            color: #111;
        }

        .hero .art {
            background:
                radial-gradient(1200px 500px at 120% -20%, rgba(255, 255, 255, .35), rgba(255, 255, 255, 0) 70%),
                radial-gradient(400px 300px at 0% 100%, rgba(255, 159, 182, .35), rgba(255, 159, 182, 0) 70%),
                url('hero.jpg') center/cover no-repeat;
            min-height: 340px;
        }

        .sectionTitle {
            text-align: center;
            margin: 32px 0 18px;
            font-size: 22px;
        }

        .subtleRule {
            width: 120px;
            height: 2px;
            background: #111;
            margin: 8px auto 16px;
            border-radius: 2px;
            opacity: .1;
        }

        .catRow {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 18px;
            margin-bottom: 36px;
        }

        .cat {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 16px;
            padding: 16px 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #111;
            box-shadow: var(--shadow);
        }

        .cat .avatar {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: linear-gradient(180deg, #ffd6e1, #ffeef2);
            display: grid;
            place-items: center;
            font-weight: 800;
            border: 1px solid #f3c0cf;
        }

        .cat span {
            font-weight: 600;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
        }

        .card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 16px;
            overflow: hidden;
            text-decoration: none;
            color: #111;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #fafafa;
        }

        .meta {
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .title {
            font-weight: 700;
            min-height: 42px;
        }

        .price {
            font-weight: 800;
        }

        .sub {
            color: #666;
            font-size: 12px;
        }

        .addRow {
            margin-top: 6px;
        }

        .btn.small {
            padding: 8px 10px;
            font-size: 13px;
        }

        footer {
            margin-top: 48px;
            padding: 26px 20px;
            text-align: center;
            color: #666;
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
            margin-left: 4px;
        }


        @media (max-width:880px) {
            .hero {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="brand">
            <a href="../index.php">
                <img src="assets/Radiant.png" alt="RadiantRoot Logo" class="logo">
            </a>
        </div>


        <nav class="nav-layout">
            <div class="left-nav">
                <a href="all_product.php">Shop</a>
            </div>

            <div class="center-nav">
                <form action="all_product.php" method="get" class="search-form">
                    <input type="text" name="q" placeholder="Search products..." />
                    <button type="submit">Search</button>
                </form>
            </div>

            <div class="right-nav">
                <?php if (!$logged_in): ?>
                    <a href="login/login.php">Login</a>
                    <a href="login/register.php">Register</a>
                <?php else: ?>
                    <!-- Cart button for logged-in users -->
                    <a href="admin/cart.php">
                        Cart
                        <span id="cart-count" class="badge">0</span>
                    </a>

                    <?php if ($is_admin): ?>
                        <a href="admin/category.php">Category</a>
                        <a href="admin/brand.php">Brand</a>
                        <a href="admin/product.php">Product</a>
                    <?php endif; ?>

                    <form action="login/logout.php" method="post" style="display:inline;">
                        <button type="submit">Logout</button>
                    </form>
                <?php endif; ?>
            </div>


        </nav>
    </header>

    <main>
        <!-- HERO -->
        <section class="hero">
            <div class="copy">
                <div class="eyebrow">Top Brands</div>
                <h1>Spotless Beauty For Your<br>Healthy Skin</h1>
                <p>Discover health & beauty picks curated for glow, balance, and everyday confidence.</p>
                <div class="ctaRow">
                    <a class="btn" href="all_product.php">Shop Now</a>
                    <a class="btn secondary" href="all_product.php?q=serum">Read More</a>
                </div>
            </div>
            <div class="art"></div>
        </section>

        <!-- SHOP BY CATEGORY -->
        <h2 class="sectionTitle">Shop By Category</h2>
        <div class="subtleRule"></div>
        <section class="catRow">
            <?php if ($cats && count($cats)): ?>
                <?php foreach ($cats as $c):
                    $name = $c['cat_name'];
                    $initial = mb_strtoupper(mb_substr($name, 0, 1));
                    // Optional: if you place an image at images/categories/{id}.jpg it will be used.
                    $catImg = "images/categories/" . $c['cat_id'] . ".jpg";
                    $hasImg = file_exists(__DIR__ . "/" . $catImg);
                ?>
                    <a class="cat" href="all_product.php?cat_id=<?= (int)$c['cat_id'] ?>">
                        <div class="avatar">
                            <?php if ($hasImg): ?>
                                <img src="<?= $catImg ?>" alt="" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                            <?php else: ?>
                                <?= e($initial) ?>
                            <?php endif; ?>
                        </div>
                        <span><?= e($name) ?></span>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column:1/-1;text-align:center;color:#666">No categories yet.</p>
            <?php endif; ?>
        </section>

        <!-- FEATURED / HAND PICKED PRODUCTS -->
        <h2 class="sectionTitle">Hand Picked Products</h2>
        <div class="subtleRule"></div>
        <section class="grid">
            <?php if ($featured && count($featured)): ?>
                <?php foreach ($featured as $p):
                    $img = trim($p['product_image'] ?? '') ?: 'https://via.placeholder.com/600x600?text=No+Image';
                ?>
                    <a class="card" href="single_product.php?id=<?= (int)$p['product_id'] ?>">
                        <img src="<?= e($img) ?>" alt="">
                        <div class="meta">
                            <div class="title"><?= e($p['product_title']) ?></div>
                            <div class="price">$<?= number_format((float)$p['product_price'], 2) ?></div>
                            <div class="sub"><?= e($p['cat_name']) ?> • <?= e($p['brand_name']) ?></div>
                            <div class="addRow">
                                <button class="btn small" type="button">Add to Cart</button>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column:1/-1;text-align:center;color:#666">No products yet. Add some from the admin panel.</p>
            <?php endif; ?>
        </section>

        <footer>
            © <?= date('Y') ?> RadiantRoot — Health & Beauty
        </footer>
    </main>
</body>

</html>