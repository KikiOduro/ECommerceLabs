<?php
// Public page (no admin gate)
require_once __DIR__ . '/settings/db_class.php';

$db = new db_connection();
$db->db_conn();

// Categories and Brands for filters (global: all that exist)
$cats   = $db->db_fetch_all("SELECT cat_id, cat_name FROM categories ORDER BY cat_name");
$brands = $db->db_fetch_all("SELECT brand_id, brand_name FROM brands ORDER BY brand_name");

// Optional: pre-fill search from ?q=
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>All Products - RadiantRoot</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        :root {
            --pink: #ffc0cb;
            --border: #000;
            --bg: #f4f6f8;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: var(--bg)
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px;
            background: var(--pink)
        }

        nav a,
        nav form button {
            margin-left: 12px;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: transparent;
            cursor: pointer;
            text-decoration: none;
            color: #000
        }

        main {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px
        }

        .toolbar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 16px
        }

        .toolbar input,
        .toolbar select {
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 8px
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 14px
        }

        .card {
            display: block;
            border: 1px solid #eee;
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
            text-decoration: none;
            color: #111
        }

        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #fafafa
        }

        .meta {
            padding: 10px
        }

        .title {
            font-weight: bold;
            margin-bottom: 4px
        }

        .price {
            color: #111;
            margin-bottom: 4px
        }

        .sub {
            color: #666;
            font-size: 12px;
            margin-bottom: 6px
        }

        .btn.small {
            padding: 6px 10px;
            border: 1px solid #000;
            border-radius: 999px;
            background: transparent;
            cursor: pointer
        }

        #pager {
            margin-top: 16px
        }

        #pager .pg {
            margin-right: 6px;
            padding: 6px 10px;
            border: 1px solid #ccc;
            background: #fff;
            border-radius: 8px;
            cursor: pointer
        }

        #pager .pg.active {
            background: #000;
            color: #fff;
            border-color: #000
        }

        .brand {
            display: flex;
            align-items: center;
            margin-left: 25px;

        }

        .logo {
            height: 50px;
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
            <a href="../index.php">
                <img src="assets/Radiant.png" alt="RadiantRoot Logo" class="logo">
            </a>
        </div>

        <nav>
            <a href="index.php">Home</a>
            <a href="all_product.php">All Products</a>
            <a href="login/login.php">Login</a>
            <a href="login/register.php">Register</a>
        </nav>
    </header>

    <main>
        <h1>All Products</h1>

        <form id="searchForm" class="toolbar">
            <input type="text" id="q" placeholder="Search products..." value="<?= htmlspecialchars($q) ?>" />
            <select id="filter_cat">
                <option value="">Filter by Category</option>
                <?php foreach ($cats as $c): ?>
                    <option value="<?= (int)$c['cat_id'] ?>"><?= htmlspecialchars($c['cat_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filter_brand">
                <option value="">Filter by Brand</option>
                <?php foreach ($brands as $b): ?>
                    <option value="<?= (int)$b['brand_id'] ?>"><?= htmlspecialchars($b['brand_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Search</button>
        </form>

        <div id="product-grid" class="grid"></div>
        <div id="pager"></div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // If page loaded with ?q=..., kick off search mode immediately
        (function() {
            const q = "<?= htmlspecialchars($q) ?>";
            if (q) {
                // set mode=search by simulating a submit after storefront.js loads
                window.__initialSearch = q;
            }
        })();
    </script>
    <script src="js/storefront.js"></script>
    <script>
        // Interop to run initial search
        $(function() {
            if (window.__initialSearch) {
                $('#q').val(window.__initialSearch);
                $('#searchForm').trigger('submit');
            }
        });
    </script>
</body>

</html>