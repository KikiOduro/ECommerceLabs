<?php
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/category_controller.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login/login.php");
    exit;
}
$cats = fetch_categories_ctr((int)$_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Product Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --pink: #ffc0cb;
            --text: #333;
            --muted: #555;
            --border: #000;
            --white: #fff;
            --bg: #f4f6f8;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 30px;
            background: var(--pink);
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
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

        nav form {
            display: inline;
            margin: 0;
        }

        main {
            max-width: 980px;
            margin: 50px auto;
            padding: 0 20px;
        }

        h1 {
            margin: 0 0 10px;
        }

        p {
            color: var(--muted);
        }

        .card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 16px;
            padding: 20px 24px;
            margin: 18px 0;
        }

        .row {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        input,
        select,
        textarea {
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 14px;
        }

        .mini {
            width: 120px;
        }

        .wide {
            flex: 1;
            min-width: 230px;
        }

        textarea {
            flex: 1;
            min-width: 300px;
            min-height: 70px;
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

        .grid {
            display: grid;
            gap: 16px;
        }

        .prod {
            border-top: 1px solid #eee;
            padding: 12px 0;
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .prod:first-child {
            border-top: none;
        }

        img.thumb {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
        }
    </style>
</head>

<body>
    <header>
        <div><strong>RadiantRoot</strong></div>
        <nav>
            <a href="../index.php">Home</a>
            <a href="category.php">Category</a>
            <a href="brand.php">Brand</a>
            <form action="../login/logout.php" method="post"><button type="submit">Logout</button></form>
        </nav>
    </header>

    <main>
        <h1>Product Management</h1>
        <p>Add or edit your Health & Beauty products.</p>

        <!-- Add/Edit Form (same form) -->
        <div class="card">
            <form id="product-form">
                <input type="hidden" id="product_id" value="">
                <div class="row">
                    <select id="category_id" required>
                        <option value="">-- Category --</option>
                        <?php foreach ($cats as $c): ?>
                            <option value="<?= (int)$c['cat_id'] ?>"><?= htmlspecialchars($c['cat_name']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select id="brand_id" required>
                        <option value="">-- Brand --</option>
                    </select>

                    <input type="text" class="wide" id="title" placeholder="Product title (e.g., Nivea Soft Cream)" required>
                    <input type="number" step="0.01" class="mini" id="price" placeholder="Price" required>
                    <input type="text" class="wide" id="keyword" placeholder="Keyword (e.g., moisturizer)">
                    <textarea id="description" placeholder="Short description"></textarea>
                    <input type="file" id="image" accept="image/*">
                    <button type="submit" class="btn" id="saveBtn">Save</button>
                    <button type="button" class="btn" id="resetBtn">Reset</button>
                </div>
            </form>
        </div>

        <!-- List -->
        <div class="card">
            <div class="grid" id="product-list"></div>
        </div>
    </main>

    <script src="../js/product.js"></script>
</body>

</html>