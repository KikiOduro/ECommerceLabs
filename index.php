<?php
// ---------- BOOTSTRAP ----------
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

require_once __DIR__ . '/settings/core.php';
require_once __DIR__ . '/settings/db_class.php';  // pulls db_cred.php internally

$logged_in = isLoggedIn();
$is_admin  = isAdmin();
$user_name = $logged_in ? ($_SESSION['user_name'] ?? 'User') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>RadiantRoot</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <style>
    /* ---------- THEME ---------- */
    :root{
      --pink:#ffc0cb;
      --text:#333;
      --muted:#555;
      --border:#000;
      --white:#fff;
      --bg:#f4f6f8;
      --shadow:0 2px 4px rgba(0,0,0,.1);
    }
    *{ box-sizing:border-box; font-family:Arial, sans-serif; }
    body{ margin:0; background:var(--bg); color:var(--text); }

    /* ---------- HEADER ---------- */
    header{
      position:sticky; top:0; z-index:10;
      display:flex; align-items:center; justify-content:space-between;
      padding:30px 30px; background:var(--pink); box-shadow:var(--shadow);
    }
    header strong{ font-size:1.2rem; }

    nav a, nav form button{
      margin-left:15px; padding:10px 14px;
      text-decoration:none; border:1px solid var(--border);
      border-radius:400px; background:transparent; color:var(--border);
      font-size:14px; cursor:pointer; transition:.2s all ease-in-out;
    }
    nav a:hover, nav form button:hover{ background:var(--border); color:var(--white); }
    nav form{ display:inline; margin:0; }

    /* ---------- MAIN ---------- */
    main{
      max-width:960px; margin:60px auto; padding:0 20px;
      display:flex; flex-direction:column; align-items:center; text-align:center;
    }
    h1{ margin:0 0 10px; font-size:2rem; }
    p{ color:var(--muted); margin:0 0 20px; }
  </style>
</head>
<body>
  <header>
    <div><strong>RadiantRoot</strong></div>

    <nav>
      <?php if (!$logged_in): ?>
        <a href="login/login.php">Login</a>
        <a href="login/register.php">Register</a>
      <?php else: ?>
        <?php if ($is_admin): ?>
          <!-- Admin shortcuts -->
          <a href="admin/category.php">Category</a>
          <a href="admin/brand.php">Brand</a>
        <?php endif; ?>
        <form action="login/logout.php" method="post">
          <button type="submit">Logout</button>
        </form>
      <?php endif; ?>
    </nav>
  </header>

  <main>
    <h1>Welcome</h1>
    <p>
      <?php if ($logged_in): ?>
        You are logged in as <strong><?= htmlspecialchars($user_name) ?></strong>.
      <?php else: ?>
        Please log in or register to continue.
      <?php endif; ?>
    </p>
  </main>
</body>
</html>
