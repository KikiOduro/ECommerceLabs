<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>



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
    <title>Brand Manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Match index.php styles (no external CSS frameworks) -->
    <style>
        :root {
            --pink: #ffc0cb;
            --text: #333;
            --muted: #555;
            --border: #000;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: var(--text);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 30px;
            background: var(--pink);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
        }

        header strong {
            font-weight: 700;
        }

        nav a,
        nav form button {
            margin-left: 15px;
            padding: 10px 14px;
            text-decoration: none;
            border: 1px solid var(--border);
            border-radius: 400px;
            color: #000;
            font-size: 14px;
            background: transparent;
            cursor: pointer;
            transition: all .2s ease-in-out;
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
            margin: 40px auto;
            padding: 0 20px;
        }

        h1 {
            margin: 0 0 10px;
        }

        p {
            color: var(--muted);
        }

        /* Cards / rows */
        .card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 16px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            padding: 18px 20px;
            margin: 18px 0;
        }

        .section-title {
            margin: 24px 0 8px;
            font-weight: 700;
        }

        .row {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .row input[type="text"] {
            flex: 1;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .row select {
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .row .btn {
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 400px;
            background: transparent;
            cursor: pointer;
            transition: all .2s ease-in-out;
        }

        .row .btn:hover {
            background: #000;
            color: #fff;
        }

        .brand-grid {
            display: grid;
            gap: 16px;
        }

        .cat-card h3 {
            margin-top: 0;
        }

        .brand-row {
            display: flex;
            gap: 10px;
            align-items: center;
            margin: 8px 0;
        }

        .brand-row input {
            flex: 1;
        }

        .logo {
            height: 50px;
            width: auto;
            object-fit: contain;
            transform: scale(1.3);
            transform-origin: left center;
            margin-left: 15px;
        }
    </style>
</head>

<body>
    <header>
        <div class="brand">
            <a href="index.php">
                <img src="../assets/Radiant.png" alt="RadiantRoot Logo" class="logo">
            </a>
        </div>

        <nav>
            <a href="../index.php">Home</a>
            <a href="category.php">Category</a>
            <form action="../login/logout.php" method="post">
                <button type="submit">Logout</button>
            </form>
        </nav>
    </header>

    <main>
        <h1>Brand Management</h1>
        <p>Create, update, or delete brands under your categories.</p>

        <div class="card">
            <div class="section-title">Add a new Brand</div>
            <form id="add-brand-form">
                <div class="row">
                    <input type="text" id="brand_name" placeholder="Brand name (e.g., Nike)" />
                    <select id="category_id">
                        <option value="">-- Choose category --</option>
                        <?php foreach ($cats as $c): ?>
                            <option value="<?= (int)$c['cat_id'] ?>"><?= htmlspecialchars($c['cat_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Add</button>
                </div>
            </form>
        </div>

        <div class="section-title">Your brands (grouped by category)</div>
        <div id="brand-list" class="brand-grid"></div>
    </main>

    <script src="../js/brand.js"></script>
</body>

</html>