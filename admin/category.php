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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Category Management</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f4f6f8;
      color: #333;
    }

    header {
      display: flex;
      padding: 30px 30px;
      background: pink;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      position: sticky;
      top: 0;
      justify-content: space-between;
      align-items: center;
    }

    nav a,
    nav form button {
      margin-left: 15px;
      padding: 10px 14px;
      text-decoration: none;
      border: 1px solid black;
      border-radius: 400px;
      color: black;
      font-size: 14px;
      background: transparent;
      cursor: pointer;
      transition: all 0.2s ease-in-out;
    }

    nav a:hover,
    nav form button:hover {
      background: black;
      color: white;
    }

    nav form {
      display: inline;
    }

    main {
      margin: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 16px;
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 8px;
    }

    td.actions {
      width: 160px;
    }

    .row {
      display: flex;
      gap: 8px;
      margin-top: 8px;
    }
  </style>
</head>

<body>
  <header>
    <div><strong>Lab1</strong></div>
    <nav>
      <a href="../index.php">Home</a>
      <a href="category.php">Category</a>
      <form action="../login/logout.php" method="post">
        <button type="submit">Logout</button>
      </form>
    </nav>
  </header>

  <main>
    <h1>Categories</h1>

  
    <form id="add-category-form">
      <div class="row">
        <input type="text" id="name" name="name" placeholder="New category name" required>
        <button type="submit" id="addBtn">Add</button>
      </div>
    </form>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="category-rows">
        <tr>
          <td colspan="4">Loading…</td>
        </tr>
      </tbody>
    </table>
  </main>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../js/category.js"></script>
</body>

</html>
