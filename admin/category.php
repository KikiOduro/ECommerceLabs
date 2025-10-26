<?php
require_once __DIR__ . '/../settings/core.php';
if (!isLoggedIn() || !isAdmin()) {
  header('Location: ../login/login.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Category Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <style>
    /* ---------- THEME ---------- */
    :root {
      --pink: #ffc0cb;
      --text: #333;
      --muted: #555;
      --border: #000;
      --white: #fff;
      --card: #fff;
      --bg: #f4f6f8;
      --shadow: 0 2px 4px rgba(0, 0, 0, .1);
      --soft-shadow: 0 1px 3px rgba(0, 0, 0, .06);
      --table-border: #e6e6e6;
      --row-hover: #fafafa;
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

    /* ---------- HEADER ---------- */
    header {
      position: sticky;
      top: 0;
      z-index: 10;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 30px 30px;
      background: var(--pink);
      box-shadow: var(--shadow);
    }

    header strong {
      font-size: 1.2rem;
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
      transition: .2s all ease-in-out;
    }

    nav a:hover,
    nav form button:hover {
      background: var(--border);
      color: var(--white);
    }

    nav form {
      display: inline;
      margin: 0;
    }

    /* ---------- MAIN LAYOUT ---------- */
    main {
      max-width: 960px;
      margin: 50px auto;
      padding: 0 20px;
    }

    h1 {
      margin: 0 0 10px;
      font-size: 1.8rem;
    }

    p.desc {
      color: var(--muted);
      margin: 0 0 24px;
    }

    /* ---------- CARD ---------- */
    .card {
      background: var(--card);
      border: 1px solid #eee;
      border-radius: 16px;
      box-shadow: var(--soft-shadow);
      padding: 20px 24px;
      margin-bottom: 24px;
    }

    .section-title {
      font-weight: 700;
      font-size: 1.1rem;
      margin-bottom: 12px;
    }

    /* ---------- FORM ROW ---------- */
    .row {
      display: flex;
      gap: 10px;
      align-items: center;
      flex-wrap: wrap;
    }

    .row input[type="text"] {
      flex: 1;
      min-width: 240px;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 14px;
      background: #fff;
    }

    .btn {
      padding: 10px 16px;
      border: 1px solid var(--border);
      border-radius: 400px;
      background: transparent;
      color: var(--border);
      cursor: pointer;
      transition: .2s all ease-in-out;
    }

    .btn:hover {
      background: var(--border);
      color: var(--white);
    }

    /* ---------- TABLE ---------- */
    .table-wrap {
      background: var(--card);
      border: 1px solid #eee;
      border-radius: 16px;
      box-shadow: var(--soft-shadow);
      overflow: hidden;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    thead th {
      text-align: left;
      background: #f8f8f8;
      border-bottom: 1px solid var(--table-border);
      padding: 12px 14px;
      font-weight: 700;
      color: #444;
    }

    tbody td {
      border-top: 1px solid var(--table-border);
      padding: 10px 14px;
      vertical-align: middle;
    }

    tbody tr:hover {
      background: var(--row-hover);
    }

    td.actions {
      width: 180px;
      white-space: nowrap;
    }

    td.actions .btn {
      margin-right: 8px;
    }

    .cat-name {
      width: 100%;
      max-width: 260px;
      padding: 8px 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      background: #fff;
      font-size: 14px;
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
      transform: scale(1.6);
      transform-origin: left center;
      margin-left: 15px;

    }

    @media (max-width:700px) {
      td.actions {
        width: auto;
      }

      td.actions .btn {
        display: inline-block;
        margin: 6px 6px 0 0;
      }
    }
  </style>
</head>

<body>
  <header>
    <div class="brand">
      <a href="../index.php">
        <img src="../assets/Radiant.png" alt="RadiantRoot Logo" class="logo">
      </a>
    </div>

    <nav>
      <a href="../index.php">Home</a>
      <a href="brand.php">Brand</a>
      <form action="../login/logout.php" method="post">
        <button type="submit">Logout</button>
      </form>
    </nav>
  </header>

  <!-- MAIN -->
  <main>
    <h1>Categories</h1>
    <p class="desc">Create, rename, or delete your categories. These will organize your brands.</p>

    <!-- Add Category -->
    <div class="card">
      <div class="section-title">Add a new Category</div>
      <form id="add-category-form">
        <div class="row">
          <input type="text" id="name" name="name" placeholder="New category name" required>
          <button type="submit" id="addBtn" class="btn">Add</button>
        </div>
      </form>
    </div>

    <!-- Table -->
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:90px;">ID</th>
            <th>Name</th>
            <th style="width:220px;">Created</th>
            <th style="width:200px;">Actions</th>
          </tr>
        </thead>
        <tbody id="category-rows">
          <tr>
            <td colspan="4">Loading…</td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>

  <!-- SCRIPTS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../js/category.js"></script>
</body>

</html>